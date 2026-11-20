<?php

use App\Enums\TestimonialAudience;
use App\Models\Campaign;
use App\Models\Role;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\HomePageContent;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);
});

/**
 * A valid homepage form submission; tests override the parts they check.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function homePagePayload(array $overrides = []): array
{
    $sections = [];

    foreach (array_keys(HomePageContent::SECTIONS) as $index => $key) {
        $sections[$key] = ['visible' => '1', 'order' => $index + 1];
    }

    return array_replace_recursive([
        'hero' => ['title' => ['bn' => 'নতুন শিরোনাম', 'en' => 'New headline'], 'show_trust_chips' => '1'],
        'buttons' => [['label' => ['bn' => 'ক্যাম্পেইন দেখুন', 'en' => 'See the campaign'], 'url' => '/courses', 'style' => 'primary']],
        'stats' => [],
        'sections' => $sections,
    ], $overrides);
}

test('the homepage shows the hero text and buttons saved in the admin panel', function () {
    $developer = User::factory()->withRole(Role::Developer)->create();

    $this->actingAs($developer)->put('/admin/homepage', homePagePayload())->assertSessionHasNoErrors();

    $this->get('/')->assertSee('নতুন শিরোনাম')->assertSee('ক্যাম্পেইন দেখুন')->assertSee(url('/courses'));
    $this->get('/en')->assertSee('New headline')->assertSee(url('/en/courses'));
});

test('hidden sections disappear and sections follow their order numbers', function () {
    $admin = User::factory()->withRole(Role::SuperAdmin)->create();
    Testimonial::create(['author_name' => 'Rahima', 'audience' => TestimonialAudience::Parent, 'body' => ['bn' => 'ভালো।'], 'consent_confirmed' => true, 'is_published' => true]);

    $this->actingAs($admin)->put('/admin/homepage', homePagePayload([
        'sections' => ['parent_reviews' => ['order' => 0], 'steps' => ['visible' => '0']],
    ]));

    $this->get('/')
        ->assertDontSee(__('site.home.sections.steps.title'))
        ->assertSeeInOrder([__('site.home.sections.parent_reviews.title'), __('site.home.sections.why.title')]);
});

test('impact numbers show only when staff enter them', function () {
    $admin = User::factory()->withRole(Role::SuperAdmin)->create();

    $this->get('/')->assertDontSee('শিক্ষার্থী শিখেছে');

    $this->actingAs($admin)->put('/admin/homepage', homePagePayload([
        'stats' => [['value' => '1200+', 'label' => ['bn' => 'শিক্ষার্থী শিখেছে', 'en' => 'students taught']]],
    ]));

    $this->get('/')->assertSee('১২০০+')->assertSee('শিক্ষার্থী শিখেছে');
});

test('button links must be a site path or a full URL', function () {
    $admin = User::factory()->withRole(Role::SuperAdmin)->create();

    $this->actingAs($admin)
        ->put('/admin/homepage', homePagePayload(['buttons' => [['url' => 'javascript:alert(1)']]]))
        ->assertSessionHasErrors('buttons.0.url');
});

test('staff without the homepage permission cannot edit the homepage or campaigns', function () {
    $marketing = User::factory()->withRole(Role::Marketing)->create();

    $this->actingAs($marketing)->get('/admin/homepage')->assertForbidden();
    $this->actingAs($marketing)->put('/admin/homepage', homePagePayload())->assertForbidden();
    $this->actingAs($marketing)->get('/admin/campaigns')->assertForbidden();
});

test('a running campaign appears in the hero, the campaigns section and the announcement bar', function () {
    Campaign::factory()->inBar()->create(['title' => ['bn' => 'বিজয় দিবস অফার'], 'image_url' => 'https://example.com/victory.jpg']);

    $this->get('/')->assertSee('https://example.com/victory.jpg')->assertSee(__('site.home.sections.campaigns.title'));
    $this->get('/courses')->assertSee('বিজয় দিবস অফার');
});

test('ended, future and switched-off campaigns are not shown', function () {
    Campaign::factory()->ended()->inBar()->create(['title' => ['bn' => 'শেষ হওয়া অফার']]);
    Campaign::factory()->inBar()->create(['title' => ['bn' => 'আসন্ন অফার'], 'starts_at' => now()->addDay()]);
    Campaign::factory()->inBar()->create(['title' => ['bn' => 'বন্ধ অফার'], 'is_active' => false]);

    $this->get('/')->assertDontSee('শেষ হওয়া অফার')->assertDontSee('আসন্ন অফার')->assertDontSee('বন্ধ অফার');
});

test('a scheduled campaign appears once its start time arrives', function () {
    Campaign::factory()->create(['title' => ['bn' => 'সময়মতো অফার'], 'starts_at' => now()->addHour()]);

    $this->get('/')->assertDontSee('সময়মতো অফার');

    $this->travel(2)->hours();

    $this->get('/')->assertSee('সময়মতো অফার');
});

test('parent and student reviews are shown in their own sections', function () {
    Testimonial::create(['author_name' => 'Parent One', 'audience' => TestimonialAudience::Parent, 'body' => ['bn' => 'অভিভাবকের কথা'], 'consent_confirmed' => true, 'is_published' => true]);
    Testimonial::create(['author_name' => 'Student One', 'audience' => TestimonialAudience::Student, 'body' => ['bn' => 'শিক্ষার্থীর কথা'], 'consent_confirmed' => true, 'is_published' => true]);

    $this->get('/')->assertSeeInOrder([
        __('site.home.sections.parent_reviews.title'), 'অভিভাবকের কথা',
        __('site.home.sections.student_reviews.title'), 'শিক্ষার্থীর কথা',
    ]);
});

test('a developer can create a campaign with a schedule', function () {
    $developer = User::factory()->withRole(Role::Developer)->create();

    $this->actingAs($developer)->post('/admin/campaigns', [
        'title' => ['bn' => 'ফেসবুক লাইভ: গণিত অলিম্পিয়াড প্রস্তুতি', 'en' => 'Facebook Live'],
        'channel' => 'facebook',
        'link_url' => 'https://facebook.com/events/1',
        'image_url' => 'https://example.com/live.jpg',
        'starts_at' => now()->format('Y-m-d\TH:i'),
        'ends_at' => now()->addDays(3)->format('Y-m-d\TH:i'),
        'is_active' => '1',
        'show_in_hero' => '1',
        'show_in_bar' => '1',
    ])->assertRedirect(route('admin.campaigns.index'));

    expect(Campaign::sole())
        ->image_url->toBe('https://example.com/live.jpg')
        ->show_in_bar->toBeTrue()
        ->isRunning()->toBeTrue();
});

test('a campaign cannot end before it starts', function () {
    $developer = User::factory()->withRole(Role::Developer)->create();

    $this->actingAs($developer)->post('/admin/campaigns', [
        'title' => ['bn' => 'অফার'],
        'channel' => 'website',
        'starts_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
        'ends_at' => now()->format('Y-m-d\TH:i'),
    ])->assertSessionHasErrors('ends_at');
});
