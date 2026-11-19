<?php

use App\Enums\EnrollmentStatus;
use App\Enums\LeadType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Assessment;
use App\Models\Book;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseVariation;
use App\Models\DeliveryZone;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\Order;
use App\Services\SiteSettings;

/**
 * @return array<string, mixed>
 */
function wizardPayload(array $overrides = []): array
{
    return [
        'form' => 'wizard',
        'student_name' => 'Ayaan',
        'guardian_name' => 'Rahman',
        'phone' => '01712-345678',
        'class_level' => 5,
        'consent' => '1',
        ...$overrides,
    ];
}

test('the wizard creates a lead with several course interests and campaign attribution', function () {
    $courses = Course::factory()->count(2)->create();

    $this->get('/start?utm_source=facebook&utm_campaign=winter')->assertOk();

    $this->post('/leads', wizardPayload(['course_ids' => $courses->pluck('id')->all()]))
        ->assertRedirect('/thank-you');

    $lead = Lead::sole();

    expect($lead->phone)->toBe('+8801712345678')
        ->and($lead->type)->toBe(LeadType::Wizard)
        ->and($lead->utm_source)->toBe('facebook')
        ->and($lead->utm_campaign)->toBe('winter')
        ->and($lead->interests)->toHaveCount(2)
        ->and($lead->reference)->toStartWith('CH-L-');

    $this->get('/thank-you')->assertOk()->assertSee($lead->reference);
});

test('the same phone number within 30 days updates the existing lead instead of creating another', function () {
    $this->post('/leads', wizardPayload());
    $this->post('/leads', wizardPayload(['form' => 'callback', 'message' => 'Please call after 6 pm']));

    $lead = Lead::sole();

    expect($lead->activities()->where('type', 'form_resubmitted')->count())->toBe(1)
        ->and($lead->message)->toBe('Please call after 6 pm');
});

test('asking for a free assessment creates an assessment request', function () {
    $course = Course::factory()->create();

    $this->post('/leads', wizardPayload(['wants_assessment' => '1', 'course_ids' => [$course->id]]));

    expect(Lead::sole()->type)->toBe(LeadType::Assessment)
        ->and(Assessment::sole()->course_id)->toBe($course->id);
});

test('bots filling the honeypot and forms without consent are rejected', function () {
    $this->post('/leads', wizardPayload(['website' => 'spam.example']))->assertSessionHasErrors('website');
    $this->post('/leads', wizardPayload(['consent' => null]))->assertSessionHasErrors('consent');

    expect(Lead::count())->toBe(0);
});

test('enrolling freezes the price and refund policy and records the payment claim', function () {
    app(SiteSettings::class)->updateRefundPolicy(['mode' => 'days', 'window' => 7, 'percent' => 70]);

    $course = Course::factory()->create();
    $variation = CourseVariation::factory()->for($course)->create(['regular_price' => 6000, 'discounted_price' => 4800]);
    $batch = CourseBatch::factory()->for($course)->create(['starts_on' => '2026-12-01']);

    $this->post("/courses/{$course->slug}/enroll", [
        'course_variation_id' => $variation->id,
        'course_batch_id' => $batch->id,
        'guardian_name' => 'Rahman',
        'student_name' => 'Ayaan',
        'phone' => '01712345678',
        'payment_method' => 'bkash',
        'sender_number' => '01712345678',
        'transaction_id' => 'abc123xyz',
        'agree_refund' => '1',
    ])->assertRedirect();

    $enrollment = Enrollment::sole();

    expect($enrollment->status)->toBe(EnrollmentStatus::PendingPayment)
        ->and($enrollment->payable_amount)->toBe(4800)
        ->and($enrollment->discount_amount)->toBe(1200)
        ->and($enrollment->refund_policy)->toBe(['mode' => 'days', 'window' => 7, 'percent' => 70])
        ->and($enrollment->refund_eligible_until->toDateString())->toBe('2026-12-08')
        ->and($enrollment->payments()->sole()->transaction_id)->toBe('ABC123XYZ')
        ->and($enrollment->lead->type)->toBe(LeadType::Enrollment);
});

test('a transaction ID cannot be used twice', function () {
    $course = Course::factory()->create();
    $variation = CourseVariation::factory()->for($course)->create();

    $payload = [
        'course_variation_id' => $variation->id,
        'guardian_name' => 'A', 'student_name' => 'B', 'phone' => '01712345678',
        'payment_method' => 'bkash', 'transaction_id' => 'SAME001', 'agree_refund' => '1',
    ];

    $this->post("/courses/{$course->slug}/enroll", $payload)->assertSessionHasNoErrors();
    $this->post("/courses/{$course->slug}/enroll", [...$payload, 'phone' => '01812345678'])->assertSessionHasErrors('transaction_id');
});

test('book orders charge delivery by district and keep stock until the payment is verified', function (string $district, int $charge) {
    DeliveryZone::factory()->create(['charge' => 120]);
    DeliveryZone::factory()->insideDhaka()->create(['charge' => 70]);
    $book = Book::factory()->create(['regular_price' => 450, 'discounted_price' => 380, 'stock_quantity' => 10]);

    $this->post("/books/{$book->slug}/order", [
        'quantity' => 2,
        'customer_name' => 'Rahim',
        'phone' => '01712345678',
        'shipping_address' => 'House 1, Road 2',
        'district' => $district,
        'payment_method' => 'nagad',
        'sender_number' => '01712345678',
        'transaction_id' => 'NG'.$charge,
    ])->assertRedirect();

    $order = Order::sole();

    expect($order->subtotal)->toBe(760)
        ->and($order->delivery_charge)->toBe($charge)
        ->and($order->total)->toBe(760 + $charge)
        ->and($order->status)->toBe(OrderStatus::PendingVerification)
        ->and($order->payments()->sole()->status)->toBe(PaymentStatus::Pending)
        ->and($book->fresh()->stock_quantity)->toBe(10);
})->with([
    'inside Dhaka' => ['Dhaka', 70],
    'outside Dhaka' => ['Sylhet', 120],
]);

test('order tracking needs the phone number the order was placed with', function () {
    DeliveryZone::factory()->create();
    $book = Book::factory()->create();

    $this->post("/books/{$book->slug}/order", [
        'quantity' => 1, 'customer_name' => 'Rahim', 'phone' => '01712345678', 'shipping_address' => 'Road 2',
        'district' => 'Dhaka', 'payment_method' => 'bkash', 'sender_number' => '01712345678', 'transaction_id' => 'TRACK1',
    ]);

    $order = Order::sole();

    $this->get("/track-order?reference={$order->reference}&phone=01712345678")->assertSee($order->status->label());
    $this->get("/track-order?reference={$order->reference}&phone=01999999999")->assertSee(__('site.track.not_found'));
});

test('draft courses are not visible to visitors', function () {
    $course = Course::factory()->draft()->create();

    $this->get("/courses/{$course->slug}")->assertNotFound();
});
