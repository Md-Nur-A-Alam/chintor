<?php

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\SiteSettings;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->admin = User::factory()->withRole(Role::SuperAdmin)->create();
});

test('contact settings are saved to the database and shown on the website', function () {
    $this->actingAs($this->admin)
        ->put('/admin/settings/contact', [
            'hotline' => '09610-000000',
            'whatsapp' => '01712345678',
            'email' => 'hello@example.com',
            'office_hours' => '10:00-20:00',
        ])
        ->assertSessionHasNoErrors();

    expect(app(SiteSettings::class)->contact()['whatsapp'])->toBe('+8801712345678');

    $this->get('/')->assertSee('wa.me/8801712345678', false)->assertSee('hello@example.com');
});

test('staff without the settings permission cannot change settings', function () {
    $marketing = User::factory()->withRole(Role::Marketing)->create();

    $this->actingAs($marketing)->get('/admin/settings/contact')->assertForbidden();
    $this->actingAs($marketing)->get('/admin/settings/media')->assertForbidden();
});

test('media credentials are stored encrypted and never shown back in full', function () {
    $this->actingAs($this->admin)
        ->put('/admin/settings/media', [
            'driver' => 'auto',
            'imgbb_api_key' => 'imgbb-secret-key-1234',
        ])
        ->assertSessionHasNoErrors();

    $raw = DB::table('settings')->where('key', 'media')->value('secrets');

    expect($raw)->not->toContain('imgbb-secret-key-1234')
        ->and(Setting::firstWhere('key', 'media')->secrets['imgbb_api_key'])->toBe('imgbb-secret-key-1234');

    $this->actingAs($this->admin)
        ->get('/admin/settings/media')
        ->assertDontSee('imgbb-secret-key-1234')
        ->assertSee('••••1234');
});

test('a blank secret keeps the saved value and the clear option removes it', function () {
    $settings = app(SiteSettings::class);
    $settings->updateMedia(['driver' => 'auto'], ['imgbb_api_key' => 'saved-key']);

    $this->actingAs($this->admin)->put('/admin/settings/media', ['driver' => 'imgbb', 'imgbb_api_key' => '']);
    expect($settings->mediaSecrets()['imgbb_api_key'])->toBe('saved-key');

    $this->actingAs($this->admin)->put('/admin/settings/media', ['driver' => 'auto', 'clear' => ['imgbb_api_key']]);
    expect($settings->mediaSecrets()['imgbb_api_key'])->toBeNull();
});

test('the ImgBB connection check reports success and failure', function () {
    app(SiteSettings::class)->updateMedia(['driver' => 'auto'], ['imgbb_api_key' => 'test-key']);

    Http::fake(['api.imgbb.com/*' => Http::sequence()
        ->push(['success' => true, 'data' => ['url' => 'https://i.ibb.co/x/check.png']])
        ->push(['success' => false, 'error' => ['message' => 'Invalid API v1 key.']], 400),
    ]);

    $this->actingAs($this->admin)
        ->post('/admin/settings/media/test', ['provider' => 'imgbb'])
        ->assertSessionHas('status');

    $this->actingAs($this->admin)
        ->post('/admin/settings/media/test', ['provider' => 'imgbb'])
        ->assertSessionHasErrors(['connection' => 'ImgBB আপলোড ব্যর্থ: Invalid API v1 key.']);
});
