<?php

use App\Models\LearningTopic;
use App\Models\LearningVideo;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->math = LearningTopic::factory()->create(['slug' => 'math', 'name' => ['bn' => 'গণিত', 'en' => 'Maths']]);
    $this->geometry = LearningTopic::factory()->topicOf($this->math)->create(['slug' => 'geometry', 'name' => ['bn' => 'জ্যামিতি', 'en' => 'Geometry']]);
    $this->science = LearningTopic::factory()->create(['slug' => 'science', 'name' => ['bn' => 'বিজ্ঞান', 'en' => 'Science']]);
});

test('the video library filters by subject and topic', function () {
    LearningVideo::factory()->for($this->math, 'subject')->create(['title' => ['bn' => 'অ্যালজেব্রার ভিডিও'], 'topic_id' => null]);
    LearningVideo::factory()->for($this->math, 'subject')->create(['title' => ['bn' => 'ত্রিভুজের ভিডিও'], 'topic_id' => $this->geometry->id]);
    LearningVideo::factory()->for($this->science, 'subject')->create(['title' => ['bn' => 'চুম্বকের ভিডিও']]);
    LearningVideo::factory()->for($this->science, 'subject')->create(['title' => ['bn' => 'খসড়া ভিডিও'], 'is_published' => false]);

    $this->get('/learn')->assertOk()->assertSee('অ্যালজেব্রার ভিডিও')->assertSee('চুম্বকের ভিডিও')->assertDontSee('খসড়া ভিডিও');

    $this->get('/learn?subject=math')->assertSee('অ্যালজেব্রার ভিডিও')->assertSee('ত্রিভুজের ভিডিও')->assertDontSee('চুম্বকের ভিডিও');

    $this->get('/learn?subject=math&topic=geometry')->assertSee('ত্রিভুজের ভিডিও')->assertDontSee('অ্যালজেব্রার ভিডিও');

    $this->get('/en/learn?subject=science')->assertOk()->assertSee('Science');
});

test('featured videos appear on the homepage', function () {
    LearningVideo::factory()->featured()->for($this->math, 'subject')->create(['title' => ['bn' => 'ফিচার্ড ধাঁধা']]);
    LearningVideo::factory()->for($this->math, 'subject')->create(['title' => ['bn' => 'সাধারণ ধাঁধা']]);

    $this->get('/')->assertSee('ফিচার্ড ধাঁধা')->assertDontSee('সাধারণ ধাঁধা');
});

test('staff can feature at most six videos', function () {
    $marketing = User::factory()->withRole(Role::Marketing)->create();
    LearningVideo::factory()->featured()->count(6)->for($this->math, 'subject')->create();

    $this->actingAs($marketing)->post('/admin/learning-videos', [
        'title' => ['bn' => 'সপ্তম ফিচার্ড'],
        'youtube_url' => 'https://youtu.be/aqz-KE-bpKQ',
        'subject_id' => $this->math->id,
        'is_featured' => '1',
        'is_published' => '1',
    ])->assertSessionHasErrors('is_featured');

    expect(LearningVideo::where('is_featured', true)->count())->toBe(6);
});

test('a saved video gets its orientation from the link and must use a topic of its subject', function () {
    $coordinator = User::factory()->withRole(Role::CourseCoordinator)->create();

    $this->actingAs($coordinator)->post('/admin/learning-videos', [
        'title' => ['bn' => 'ভুল টপিক'],
        'youtube_url' => 'https://www.youtube.com/shorts/aqz-KE-bpKQ',
        'subject_id' => $this->science->id,
        'topic_id' => $this->geometry->id,
    ])->assertSessionHasErrors('topic_id');

    $this->actingAs($coordinator)->post('/admin/learning-videos', [
        'title' => ['bn' => 'জ্যামিতির শর্টস'],
        'youtube_url' => 'https://www.youtube.com/shorts/aqz-KE-bpKQ',
        'subject_id' => $this->math->id,
        'topic_id' => $this->geometry->id,
        'is_published' => '1',
    ])->assertRedirect(route('admin.learning-videos.index'));

    expect(LearningVideo::sole()->orientation)->toBe(LearningVideo::Vertical);
});

test('sales executives cannot manage learning content', function () {
    $sales = User::factory()->withRole(Role::SalesExecutive)->create();

    $this->actingAs($sales)->get('/admin/learning-videos')->assertForbidden();
    $this->actingAs($sales)->get('/admin/questions')->assertForbidden();
});
