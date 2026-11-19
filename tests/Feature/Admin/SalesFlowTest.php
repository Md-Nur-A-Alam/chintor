<?php

use App\Enums\EnrollmentStatus;
use App\Enums\LeadStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PublishStatus;
use App\Models\Book;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseVariation;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->manager = User::factory()->create();
    $this->manager->roles()->attach(Role::firstWhere('name', Role::Manager));

    $this->sales = User::factory()->create();
    $this->sales->roles()->attach(Role::firstWhere('name', Role::SalesExecutive));

    $this->marketing = User::factory()->create();
    $this->marketing->roles()->attach(Role::firstWhere('name', Role::Marketing));
});

test('a sales executive only sees and opens their own leads', function () {
    $mine = Lead::factory()->assignedTo($this->sales)->create(['guardian_name' => 'My Lead']);
    $other = Lead::factory()->create(['guardian_name' => 'Queue Lead']);

    $this->actingAs($this->sales)->get('/admin/leads')->assertSee('My Lead')->assertDontSee('Queue Lead');
    $this->actingAs($this->sales)->get("/admin/leads/{$other->id}")->assertForbidden();
    $this->actingAs($this->sales)->get("/admin/leads/{$mine->id}")->assertOk();
});

test('marketing assigns leads from the central queue and the timeline records it', function () {
    $leads = Lead::factory()->count(2)->create();

    $this->actingAs($this->marketing)
        ->post('/admin/leads/assign', ['lead_ids' => $leads->pluck('id')->all(), 'assigned_to' => $this->sales->id])
        ->assertSessionHas('status');

    expect(Lead::where('assigned_to', $this->sales->id)->count())->toBe(2)
        ->and($leads->first()->activities()->where('type', 'assignment')->exists())->toBeTrue();
});

test('sales executives cannot assign leads', function () {
    $lead = Lead::factory()->create();

    $this->actingAs($this->sales)
        ->post('/admin/leads/assign', ['lead_ids' => [$lead->id], 'assigned_to' => $this->sales->id])
        ->assertForbidden();
});

test('logging the first call moves a new lead to contacted', function () {
    $lead = Lead::factory()->assignedTo($this->sales)->create();

    $this->actingAs($this->sales)
        ->post("/admin/leads/{$lead->id}/activities", ['type' => 'call', 'outcome' => 'answered', 'body' => 'Interested in evening batch']);

    expect($lead->fresh()->status)->toBe(LeadStatus::Contacted);
});

test('verifying a book payment confirms the order and takes the books out of stock', function () {
    $book = Book::factory()->create(['stock_quantity' => 10]);
    $order = Order::create([
        'customer_name' => 'Rahim', 'phone' => '+8801712345678', 'shipping_address' => 'Road 2', 'district' => 'Dhaka',
        'status' => OrderStatus::PendingVerification, 'subtotal' => 760, 'delivery_charge' => 70, 'total' => 830,
    ]);
    $order->items()->create(['book_id' => $book->id, 'title_snapshot' => 'Book', 'unit_price' => 380, 'quantity' => 2, 'line_total' => 760]);
    $payment = $order->payments()->create(['method' => PaymentMethod::Bkash, 'amount' => 830, 'transaction_id' => 'TX1', 'status' => PaymentStatus::Pending]);

    $this->actingAs($this->manager)->post("/admin/payments/{$payment->id}/verify")->assertSessionHas('status');

    expect($order->fresh()->status)->toBe(OrderStatus::Confirmed)
        ->and($book->fresh()->stock_quantity)->toBe(8)
        ->and($payment->fresh()->verified_by)->toBe($this->manager->id);

    // A second click does nothing harmful.
    $this->actingAs($this->manager)->post("/admin/payments/{$payment->id}/verify")->assertSessionHasErrors('general');
    expect($book->fresh()->stock_quantity)->toBe(8);
});

test('a sales executive converts a lead within their discount limit and verification activates it', function () {
    $course = Course::factory()->create();
    $variation = CourseVariation::factory()->for($course)->create(['regular_price' => 5000, 'discounted_price' => null]);
    $batch = CourseBatch::factory()->for($course)->create(['seats_taken' => 3]);
    $lead = Lead::factory()->assignedTo($this->sales)->create();

    $payload = [
        'lead_id' => $lead->id,
        'course_variation_id' => $variation->id,
        'course_batch_id' => $batch->id,
        'guardian_name' => 'Rahman',
        'student_name' => 'Ayaan',
        'payment_method' => 'bkash',
        'transaction_id' => 'SALE01',
    ];

    // Sales executives may give up to 10% (৳500 here).
    $this->actingAs($this->sales)->post('/admin/enrollments', [...$payload, 'discount_amount' => 600])->assertSessionHasErrors('discount_amount');
    $this->actingAs($this->sales)->post('/admin/enrollments', [...$payload, 'discount_amount' => 500])->assertRedirect();

    $enrollment = Enrollment::sole();
    expect($enrollment->payable_amount)->toBe(4500)
        ->and($enrollment->sold_by)->toBe($this->sales->id)
        ->and($lead->fresh()->status)->toBe(LeadStatus::PaymentPending);

    $this->actingAs($this->manager)->post('/admin/payments/'.Payment::sole()->id.'/verify');

    expect($enrollment->fresh()->status)->toBe(EnrollmentStatus::Active)
        ->and($batch->fresh()->seats_taken)->toBe(4)
        ->and($lead->fresh()->status)->toBe(LeadStatus::Enrolled);
});

test('sales executives cannot verify payments', function () {
    $this->actingAs($this->sales)->get('/admin/payments')->assertForbidden();
});

test('a course cannot be published without an explainer video', function () {
    $coordinator = User::factory()->create();
    $coordinator->roles()->attach(Role::firstWhere('name', Role::CourseCoordinator));
    $program = Program::factory()->create();

    $payload = [
        'program_id' => $program->id,
        'title' => ['bn' => 'নতুন কোর্স', 'en' => 'New course'],
        'status' => PublishStatus::Published->value,
        'explainer_video_url' => '',
    ];

    $this->actingAs($coordinator)->post('/admin/courses', $payload)->assertSessionHasErrors('status');

    $this->actingAs($coordinator)
        ->post('/admin/courses', [...$payload, 'explainer_video_url' => 'https://youtu.be/dQw4w9WgXcQ'])
        ->assertSessionHasNoErrors();

    expect(Course::sole())
        ->slug->toBe('new-course')
        ->status->toBe(PublishStatus::Published);
});

test('the dashboard shows each role only its own cards', function () {
    $this->actingAs($this->sales)->get('/admin')->assertSee('আমার খোলা লিড')->assertDontSee('যাচাই বাকি পেমেন্ট');
    $this->actingAs($this->manager)->get('/admin')->assertSee('যাচাই বাকি পেমেন্ট');
});
