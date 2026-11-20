<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseVariation;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Creates course enrollments, from the website (submit) or by a sales
 * executive converting a lead (createForLead). Either way the guardian and
 * student records are reused, the price and refund policy are frozen on the
 * enrollment, and an optional payment claim waits for verification.
 */
class EnrollmentRequests
{
    public function __construct(
        private readonly LeadCapture $leads,
        private readonly StaffNotifier $notifier,
    ) {}

    /**
     * @param  array{guardian_name: string, phone: string, student_name: string, class_level?: ?int, payment_method?: ?string, sender_number?: ?string, transaction_id?: ?string}  $details
     */
    public function submit(Course $course, CourseVariation $variation, ?CourseBatch $batch, array $details, Request $request): Enrollment
    {
        return DB::transaction(function () use ($course, $variation, $batch, $details, $request): Enrollment {
            $lead = $this->leads->capture(LeadType::Enrollment, [
                'guardian_name' => $details['guardian_name'],
                'student_name' => $details['student_name'],
                'phone' => $details['phone'],
                'class_level' => $details['class_level'] ?? null,
            ], $request, ['course_ids' => [$course->id]]);

            $enrollment = $this->createEnrollment($lead, $course, $variation, $batch, $details);

            $this->recordPayment($enrollment, $details, 'customer');

            return $enrollment;
        });
    }

    /**
     * A sales executive enrols a lead after a call, optionally with a
     * discount (already checked against their role's limit).
     *
     * @param  array{guardian_name: string, student_name: string, class_level?: ?int, discount_amount?: int, payment_method?: ?string, sender_number?: ?string, transaction_id?: ?string}  $details
     */
    public function createForLead(Lead $lead, Course $course, CourseVariation $variation, ?CourseBatch $batch, array $details, User $seller): Enrollment
    {
        return DB::transaction(function () use ($lead, $course, $variation, $batch, $details, $seller): Enrollment {
            $enrollment = $this->createEnrollment($lead, $course, $variation, $batch, [
                ...$details,
                'phone' => $lead->phone,
            ], $seller);

            $lead->update(['status' => LeadStatus::PaymentPending]);
            $this->recordPayment($enrollment, $details, 'staff');

            return $enrollment;
        });
    }

    /**
     * @param  array{guardian_name: string, phone: string, student_name: string, class_level?: ?int, discount_amount?: int}  $details
     */
    private function createEnrollment(Lead $lead, Course $course, CourseVariation $variation, ?CourseBatch $batch, array $details, ?User $seller = null): Enrollment
    {
        $guardian = Guardian::updateOrCreate(['phone' => $details['phone']], ['name' => $details['guardian_name']]);
        $student = $guardian->students()->firstOrCreate(
            ['name' => $details['student_name']],
            ['class_level' => $details['class_level'] ?? null],
        );

        $lead->update(['guardian_id' => $guardian->id, 'student_id' => $student->id]);

        $variationDiscount = $variation->regular_price - $variation->payablePrice();
        $extraDiscount = min($details['discount_amount'] ?? 0, $variation->payablePrice());
        $refundPolicy = $course->effectiveRefundPolicy();
        $firstClassAt = $batch?->starts_on?->copy()->startOfDay();

        return Enrollment::create([
            'guardian_id' => $guardian->id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'course_batch_id' => $batch?->id,
            'course_variation_id' => $variation->id,
            'lead_id' => $lead->id,
            'sold_by' => $seller?->id,
            'status' => EnrollmentStatus::PendingPayment,
            'list_price' => $variation->regular_price,
            'discount_amount' => $variationDiscount + $extraDiscount,
            'payable_amount' => $variation->payablePrice() - $extraDiscount,
            'refund_policy' => $refundPolicy,
            'first_class_at' => $firstClassAt,
            'refund_eligible_until' => $refundPolicy['mode'] === 'days' && $firstClassAt
                ? $firstClassAt->copy()->addDays($refundPolicy['window'])->endOfDay()
                : null,
        ]);
    }

    /**
     * @param  array{payment_method?: ?string, sender_number?: ?string, transaction_id?: ?string}  $details
     */
    private function recordPayment(Enrollment $enrollment, array $details, string $submittedBy): void
    {
        if (blank($details['transaction_id'] ?? null) || blank($details['payment_method'] ?? null)) {
            return;
        }

        $payment = $enrollment->payments()->create([
            'method' => PaymentMethod::from($details['payment_method']),
            'amount' => $enrollment->payable_amount,
            'sender_number' => $details['sender_number'] ?? null,
            'transaction_id' => $details['transaction_id'],
            'status' => PaymentStatus::Pending,
            'submitted_by' => $submittedBy,
        ]);

        $this->notifier->paymentSubmitted($payment);
    }
}
