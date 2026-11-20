<?php

use App\Models\DeveloperQueryLog;
use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->developer = User::factory()->withRole(Role::Developer)->create(['password' => 'dev-password']);
    $this->manager = User::factory()->withRole(Role::Manager)->create(['name' => 'Manager Person']);
});

test('only developers and admins can open the developer tools', function () {
    $this->actingAs($this->manager)->get('/admin/developer')->assertForbidden();

    foreach (['/admin/developer', '/admin/developer/guide', '/admin/developer/database', '/admin/developer/schema', '/admin/developer/errors', '/admin/developer/staff'] as $url) {
        $this->actingAs($this->developer)->get($url)->assertOk();
    }
});

test('the schema page draws the ER diagram from the live database', function () {
    $this->actingAs($this->developer)
        ->get('/admin/developer/schema')
        ->assertSee('erDiagram')
        ->assertSee('LEADS ||--o{ LEAD_ACTIVITIES', false);

    $this->actingAs($this->developer)
        ->get('/admin/developer/schema?table=courses')
        ->assertSee('COURSE_VARIATIONS', false);
});

test('the SQL console runs several read queries and logs them', function () {
    Lead::factory()->count(2)->create();

    $this->actingAs($this->developer)
        ->post('/admin/developer/database', ['sql' => 'SELECT COUNT(*) AS total_leads FROM leads; SELECT 1 AS one'])
        ->assertOk()
        ->assertSeeInOrder(['total_leads', 'title="2"', '>one<'], false);

    expect(DeveloperQueryLog::count())->toBe(2)
        ->and(DeveloperQueryLog::first()->is_write)->toBeFalse();
});

test('write queries need write mode and the developer password', function () {
    $lead = Lead::factory()->create(['guardian_name' => 'Before']);
    $sql = "UPDATE leads SET guardian_name = 'After' WHERE id = {$lead->id}";

    $this->actingAs($this->developer)->post('/admin/developer/database', ['sql' => $sql])->assertSessionHasErrors('sql');
    $this->actingAs($this->developer)->post('/admin/developer/database', ['sql' => $sql, 'write_mode' => '1', 'password' => 'wrong'])->assertSessionHasErrors('password');
    expect($lead->fresh()->guardian_name)->toBe('Before');

    $this->actingAs($this->developer)
        ->post('/admin/developer/database', ['sql' => $sql, 'write_mode' => '1', 'password' => 'dev-password'])
        ->assertOk();

    expect($lead->fresh()->guardian_name)->toBe('After')
        ->and(DeveloperQueryLog::where('is_write', true)->sole()->affected_rows)->toBe(1);
});

test('writes are refused when disabled for the server', function () {
    config()->set('chintor.developer.allow_write_queries', false);

    $this->actingAs($this->developer)
        ->post('/admin/developer/database', ['sql' => 'DELETE FROM leads', 'write_mode' => '1', 'password' => 'dev-password'])
        ->assertSessionHasErrors('sql');
});

test('a developer can switch to a staff profile and back', function () {
    $this->actingAs($this->developer)
        ->post("/admin/developer/staff/{$this->manager->id}/impersonate")
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($this->manager);
    $this->get('/admin')->assertSee('Manager Person-এর প্রোফাইলে আছেন', false);

    $this->post('/admin/impersonation/stop')->assertRedirect(route('admin.developer.staff'));
    $this->assertAuthenticatedAs($this->developer);
});

test('staff without the developer tools cannot switch profiles', function () {
    $this->actingAs($this->manager)->post("/admin/developer/staff/{$this->developer->id}/impersonate")->assertForbidden();
    $this->actingAs($this->manager)->post('/admin/impersonation/stop')->assertForbidden();
});

test('a developer can reset any staff password; the new one is shown once and the user is notified', function () {
    $response = $this->actingAs($this->developer)
        ->post("/admin/developer/staff/{$this->manager->id}/reset-password")
        ->assertSessionHas('generated_password');

    $password = session('generated_password')['password'];

    expect(Hash::check($password, $this->manager->fresh()->password))->toBeTrue()
        ->and($this->manager->notifications()->count())->toBe(1);
});

test('the error log page shows recent log entries', function () {
    logger()->error('Test failure for the developer log page');

    $this->actingAs($this->developer)
        ->get('/admin/developer/errors?level=error')
        ->assertSee('Test failure for the developer log page');
});
