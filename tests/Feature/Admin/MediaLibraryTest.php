<?php

use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use App\Services\Media\MediaStorage;
use App\Services\SiteSettings;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * A real 1×1 PNG (the GD extension is not available to fake images locally).
 */
function pngUpload(string $name = 'banner.png'): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));
}

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->staff = User::factory()->withRole(Role::Marketing)->create();

    Storage::fake('uploads');
});

test('without cloud credentials images are stored on this server', function () {
    expect(app(MediaStorage::class)->activeProvider())->toBe(Media::ProviderLocal);

    $this->actingAs($this->staff)
        ->post('/admin/media', ['image' => pngUpload()])
        ->assertSessionHas('status');

    $media = Media::sole();

    expect($media->provider)->toBe(Media::ProviderLocal)
        ->and($media->width)->toBe(1)
        ->and($media->uploaded_by)->toBe($this->staff->id);

    Storage::disk('uploads')->assertExists($media->path);
});

test('with an ImgBB key images are uploaded to ImgBB and previewed from its URL', function () {
    app(SiteSettings::class)->updateMedia(['driver' => 'auto'], ['imgbb_api_key' => 'test-key']);

    Http::fake(['api.imgbb.com/*' => Http::response([
        'success' => true,
        'data' => ['id' => 'abc123', 'url' => 'https://i.ibb.co/abc123/banner.png', 'delete_url' => 'https://ibb.co/abc123/delete'],
    ])]);

    $this->actingAs($this->staff)
        ->post('/admin/media', ['image' => pngUpload()])
        ->assertSessionHas('status');

    expect(Media::sole())
        ->provider->toBe(Media::ProviderImgbb)
        ->url->toBe('https://i.ibb.co/abc123/banner.png');

    Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://api.imgbb.com/1/upload?key=test-key'));

    $this->actingAs($this->staff)->get('/admin/media')->assertSee('https://i.ibb.co/abc123/banner.png');
});

test('a failed cloud upload is reported and nothing is saved', function () {
    app(SiteSettings::class)->updateMedia(['driver' => 'imgbb'], ['imgbb_api_key' => 'bad-key']);

    Http::fake(['api.imgbb.com/*' => Http::response(['success' => false, 'error' => ['message' => 'Invalid API v1 key.']], 400)]);

    $this->actingAs($this->staff)
        ->post('/admin/media', ['image' => pngUpload()])
        ->assertSessionHasErrors('image');

    expect(Media::count())->toBe(0);
});

test('only images are accepted', function () {
    $this->actingAs($this->staff)
        ->post('/admin/media', ['image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')])
        ->assertSessionHasErrors('image');
});

test('deleting removes the file and the record', function () {
    $this->actingAs($this->staff)->post('/admin/media', ['image' => pngUpload('a.png')]);
    $media = Media::sole();

    $this->actingAs($this->staff)->delete("/admin/media/{$media->id}")->assertSessionHas('status');

    Storage::disk('uploads')->assertMissing($media->path);
    expect(Media::count())->toBe(0);
});

test('the YouTube preview accepts unlisted share links', function () {
    $this->actingAs($this->staff)
        ->get('/admin/media?youtube='.urlencode('https://youtu.be/dQw4w9WgXcQ?si=share'))
        ->assertSee('https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg', false);
});

test('sales executives cannot manage media', function () {
    $sales = User::factory()->withRole(Role::SalesExecutive)->create();

    $this->actingAs($sales)->get('/admin/media')->assertForbidden();
});
