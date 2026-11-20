<?php

use App\Enums\LeadType;
use App\Enums\PublishStatus;
use App\Models\Assessment;
use App\Models\Book;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseVariation;
use App\Models\DeliveryZone;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\Page;
use App\Models\PaymentAccount;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\EnrollmentRequests;
use App\Services\OrderPlacement;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Request;

/**
 * Smoke test: every public and admin page renders with realistic data,
 * in both languages. Catches broken Blade templates early.
 */
beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->course = Course::factory()->featured()->create();
    CourseVariation::factory()->for($this->course)->create();
    $this->batch = CourseBatch::factory()->for($this->course)->create();
    $this->teacher = Teacher::factory()->create();
    $this->course->teachers()->attach($this->teacher);
    $this->book = Book::factory()->create(['program_id' => $this->course->program_id]);

    PaymentAccount::factory()->create();
    DeliveryZone::factory()->create();
    DeliveryZone::factory()->insideDhaka()->create();
    Page::create(['slug' => 'about', 'type' => 'page', 'title' => ['bn' => 'আমাদের সম্পর্কে'], 'body' => ['bn' => "## আমরা\nচিন্তর।"], 'status' => PublishStatus::Published]);
    Faq::create(['question' => ['bn' => 'প্রশ্ন?'], 'answer' => ['bn' => 'উত্তর।'], 'is_active' => true]);
    Testimonial::create(['author_name' => 'অভিভাবক', 'body' => ['bn' => 'চমৎকার।'], 'consent_confirmed' => true, 'is_published' => true]);

    $request = Request::create('/');
    $request->setLaravelSession(app('session.store'));

    $this->enrollment = app(EnrollmentRequests::class)->submit(
        $this->course,
        $this->course->variations->first(),
        $this->batch,
        ['guardian_name' => 'Guardian', 'student_name' => 'Student', 'phone' => '+8801712345678', 'payment_method' => 'bkash', 'sender_number' => '+8801712345678', 'transaction_id' => 'TRX123'],
        $request,
    );

    $this->order = app(OrderPlacement::class)->place(
        $this->book,
        1,
        ['customer_name' => 'Buyer', 'phone' => '+8801812345678', 'shipping_address' => 'Road 1', 'district' => 'Dhaka', 'payment_method' => 'nagad', 'sender_number' => '+8801812345678', 'transaction_id' => 'NGD456'],
        $request,
    );

    $this->lead = Lead::factory()->create(['type' => LeadType::Assessment]);
    $this->assessment = Assessment::create(['lead_id' => $this->lead->id, 'status' => 'requested']);

    $this->admin = User::factory()->withRole(Role::SuperAdmin)->create();
});

test('public pages render in Bangla and English', function (string $prefix) {
    $urls = [
        '/',
        '/programs',
        "/programs/{$this->course->program->slug}",
        '/courses',
        "/courses/{$this->course->slug}",
        "/courses/{$this->course->slug}/enroll",
        "/enrollments/{$this->enrollment->ulid}",
        '/books',
        "/books/{$this->book->slug}",
        "/books/{$this->book->slug}/order",
        "/orders/{$this->order->ulid}",
        "/track-order?reference={$this->order->reference}&phone=01812345678",
        '/teachers',
        "/teachers/{$this->teacher->slug}",
        '/start',
        '/info/about',
    ];

    foreach ($urls as $url) {
        $this->get($prefix.$url)->assertOk();
    }
})->with(['bn' => '', 'en' => '/en']);

test('unknown pages show the branded 404 page', function () {
    $this->get('/no-such-page')->assertNotFound()->assertSee('চিন্তু', false);
});

test('every admin page renders for an admin', function () {
    $urls = [
        '/admin',
        '/admin/leads',
        "/admin/leads/{$this->lead->id}",
        '/admin/assessments',
        "/admin/assessments/{$this->assessment->id}/edit",
        '/admin/enrollments',
        "/admin/enrollments/create?lead={$this->lead->id}",
        "/admin/enrollments/{$this->enrollment->id}",
        '/admin/orders',
        "/admin/orders/{$this->order->id}",
        '/admin/payments',
        '/admin/programs',
        '/admin/programs/create',
        "/admin/programs/{$this->course->program->slug}/edit",
        '/admin/courses',
        '/admin/courses/create',
        "/admin/courses/{$this->course->slug}/edit",
        '/admin/teachers',
        '/admin/teachers/create',
        "/admin/teachers/{$this->teacher->slug}/edit",
        '/admin/books',
        '/admin/books/create',
        "/admin/books/{$this->book->slug}/edit",
        '/admin/pages',
        '/admin/pages/create',
        '/admin/pages/about/edit',
        '/admin/faqs',
        '/admin/faqs/create',
        '/admin/testimonials',
        '/admin/testimonials/create',
        '/admin/media',
        '/admin/users',
        '/admin/users/create',
        '/admin/settings/contact',
        '/admin/settings/media',
        '/admin/settings/sales',
        '/admin/payment-accounts',
        '/admin/payment-accounts/create',
        '/admin/guide',
        '/admin/notifications',
        '/admin/developer',
        '/admin/developer/guide',
        '/admin/developer/database',
        '/admin/developer/schema',
        '/admin/developer/errors',
        '/admin/developer/staff',
    ];

    foreach ($urls as $url) {
        $this->actingAs($this->admin)->get($url)->assertOk();
    }
});
