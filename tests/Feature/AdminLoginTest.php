<?php

use App\Models\Role;
use App\Models\User;

function staffMember(array $attributes = []): User
{
    return User::factory()->create([
        'phone' => '+8801712345678',
        'password' => 'secret-pass',
        ...$attributes,
    ]);
}

test('guests are redirected from the admin panel to the login page', function () {
    $this->get('/admin')->assertRedirect(route('admin.login'));
});

test('the login page is not indexed by search engines', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('noindex, nofollow', false)
        ->assertSee('স্টাফ লগইন');
});

test('staff can sign in with phone and password in any common phone format', function (string $typedPhone) {
    $staff = staffMember();

    $this->post('/admin/login', ['phone' => $typedPhone, 'password' => 'secret-pass'])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($staff);
    expect($staff->fresh()->last_login_at)->not->toBeNull();
})->with(['01712345678', '01712-345678', '+8801712345678', '০১৭১২৩৪৫৬৭৮']);

test('a wrong password is rejected without revealing which field was wrong', function () {
    staffMember();

    $this->from('/admin/login')
        ->post('/admin/login', ['phone' => '01712345678', 'password' => 'wrong'])
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors(['phone' => 'ফোন নম্বর বা পাসওয়ার্ড সঠিক নয়।']);

    $this->assertGuest();
});

test('inactive staff and non-staff accounts cannot sign in', function (string $state) {
    User::factory()->{$state}()->create(['phone' => '+8801712345678', 'password' => 'secret-pass']);

    $this->post('/admin/login', ['phone' => '01712345678', 'password' => 'secret-pass'])
        ->assertSessionHasErrors('phone');

    $this->assertGuest();
})->with(['inactive', 'student']);

test('a staff member deactivated mid-session is signed out', function () {
    $staff = staffMember();

    $this->actingAs($staff)->get('/admin')->assertOk();

    $staff->update(['is_active' => false]);

    $this->actingAs($staff->fresh())->get('/admin')->assertRedirect(route('admin.login'));
    $this->assertGuest();
});

test('login is locked after five failed attempts', function () {
    staffMember();

    foreach (range(1, 5) as $attempt) {
        $this->post('/admin/login', ['phone' => '01712345678', 'password' => 'wrong']);
    }

    $this->post('/admin/login', ['phone' => '01712345678', 'password' => 'secret-pass'])
        ->assertSessionHasErrors('phone');

    $this->assertGuest();
});

test('the dashboard shows the signed-in staff profile', function () {
    $staff = staffMember([
        'name' => 'Test Admin',
        'office_id' => 'CNT-99',
        'designation' => 'CEO, Chintor',
    ]);
    $staff->roles()->attach(Role::create(['name' => Role::SuperAdmin, 'label' => 'সুপার অ্যাডমিন']));

    $this->actingAs($staff)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Test Admin')
        ->assertSee('CNT-99')
        ->assertSee('CEO, Chintor')
        ->assertSee('সুপার অ্যাডমিন');
});

test('staff can sign out', function () {
    $this->actingAs(staffMember())
        ->post('/admin/logout')
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();
});

test('a super admin passes every authorization check', function () {
    $superAdmin = User::factory()->withRole(Role::SuperAdmin)->create();
    $salesExecutive = User::factory()->withRole(Role::SalesExecutive)->create();

    expect($superAdmin->can('payments.verify'))->toBeTrue()
        ->and($salesExecutive->can('payments.verify'))->toBeFalse();
});
