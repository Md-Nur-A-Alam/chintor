<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;

test('the command creates a super admin who can sign in by phone', function () {
    $this->artisan('chintor:create-super-admin', [
        '--name' => 'Test Founder',
        '--phone' => '01712-345678',
        '--email' => 'founder@example.com',
        '--password' => 'local-pass',
        '--office-id' => 'CNT-01',
        '--designation' => 'CEO',
    ])->assertSuccessful();

    $admin = User::firstWhere('phone', '+8801712345678');

    expect($admin->isSuperAdmin())->toBeTrue()
        ->and($admin->office_id)->toBe('CNT-01')
        ->and(Role::count())->toBe(count(RoleSeeder::ROLES));

    $this->post('/admin/login', ['phone' => '01712345678', 'password' => 'local-pass'])
        ->assertRedirect(route('admin.dashboard'));
});

test('running the command twice updates the same account', function () {
    $options = ['--name' => 'Founder', '--phone' => '01712345678', '--password' => 'local-pass', '--email' => '', '--office-id' => '', '--designation' => ''];

    $this->artisan('chintor:create-super-admin', $options)->assertSuccessful();
    $this->artisan('chintor:create-super-admin', [...$options, '--name' => 'Renamed'])->assertSuccessful();

    expect(User::count())->toBe(1)
        ->and(User::first()->name)->toBe('Renamed');
});

test('an invalid phone number is rejected', function () {
    $this->artisan('chintor:create-super-admin', [
        '--name' => 'Founder', '--phone' => '12345', '--password' => 'local-pass',
        '--email' => '', '--office-id' => '', '--designation' => '',
    ])->assertFailed();

    expect(User::count())->toBe(0);
});

test('a short password is rejected in production', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->artisan('chintor:create-super-admin', [
        '--name' => 'Founder', '--phone' => '01712345678', '--password' => '123456',
        '--email' => '', '--office-id' => '', '--designation' => '',
    ])->assertFailed();

    expect(User::count())->toBe(0);
});
