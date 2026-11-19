<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->admin = User::factory()->withRole(Role::SuperAdmin)->create();
});

function roleId(string $name): int
{
    return Role::where('name', $name)->value('id');
}

/**
 * @return array<string, mixed>
 */
function staffPayload(array $overrides = []): array
{
    return [
        'name' => 'Rahim Uddin',
        'phone' => '01812345678',
        'email' => 'rahim@example.com',
        'office_id' => 'CNT-05',
        'designation' => 'Sales Executive',
        'is_active' => '1',
        'roles' => [roleId(Role::SalesExecutive)],
        'password' => 'strong-pass-1',
        'password_confirmation' => 'strong-pass-1',
        ...$overrides,
    ];
}

test('only staff with the users permission can open staff management', function () {
    $sales = User::factory()->withRole(Role::SalesExecutive)->create();

    $this->actingAs($sales)->get('/admin/users')->assertForbidden();
    $this->actingAs($this->admin)->get('/admin/users')->assertOk()->assertSee($this->admin->name);
});

test('an admin can create a staff account that signs in by phone', function () {
    $this->actingAs($this->admin)
        ->post('/admin/users', staffPayload())
        ->assertRedirect(route('admin.users.index'));

    $staff = User::firstWhere('phone', '+8801812345678');

    expect($staff->hasRole(Role::SalesExecutive))->toBeTrue()
        ->and($staff->is_active)->toBeTrue();
});

test('phone numbers must be valid and unique', function () {
    $this->actingAs($this->admin)
        ->post('/admin/users', staffPayload(['phone' => '12345']))
        ->assertSessionHasErrors('phone');

    $this->actingAs($this->admin)
        ->post('/admin/users', staffPayload(['phone' => $this->admin->phone]))
        ->assertSessionHasErrors('phone');
});

test('an admin can update a staff member and keep the password when left blank', function () {
    $staff = User::factory()->withRole(Role::SalesExecutive)->create(['password' => 'original-pass']);

    $this->actingAs($this->admin)
        ->put("/admin/users/{$staff->id}", staffPayload([
            'phone' => $staff->phone,
            'email' => $staff->email,
            'name' => 'Updated Name',
            'roles' => [roleId(Role::Manager)],
            'password' => '',
            'password_confirmation' => '',
        ]))
        ->assertRedirect(route('admin.users.index'));

    $staff->refresh();

    expect($staff->name)->toBe('Updated Name')
        ->and($staff->hasRole(Role::Manager))->toBeTrue()
        ->and(Hash::check('original-pass', $staff->password))->toBeTrue();
});

test('only a super admin can grant the admin role', function () {
    $manager = User::factory()->withRole(Role::Manager)->create();
    Role::firstWhere('name', Role::Manager)->permissions()->attach(
        Permission::firstWhere('name', 'users.manage')
    );

    $this->actingAs($manager)
        ->post('/admin/users', staffPayload(['roles' => [roleId(Role::SuperAdmin)]]))
        ->assertSessionHasErrors('roles');
});

test('the last super admin cannot lose the admin role or deactivate themselves', function () {
    $this->actingAs($this->admin)
        ->put("/admin/users/{$this->admin->id}", staffPayload([
            'phone' => $this->admin->phone,
            'email' => $this->admin->email,
            'roles' => [roleId(Role::Manager)],
            'is_active' => '0',
            'password' => '',
            'password_confirmation' => '',
        ]))
        ->assertSessionHasErrors(['roles', 'is_active']);
});

test('deleting a staff member keeps the record (soft delete) and admins cannot delete themselves', function () {
    $staff = User::factory()->withRole(Role::SalesExecutive)->create();

    $this->actingAs($this->admin)->delete("/admin/users/{$staff->id}")->assertRedirect(route('admin.users.index'));
    $this->assertSoftDeleted($staff);

    $this->actingAs($this->admin)->delete("/admin/users/{$this->admin->id}")->assertSessionHasErrors('user');
    $this->assertNotSoftDeleted($this->admin);
});
