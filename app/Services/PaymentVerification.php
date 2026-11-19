<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\LeadStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * The manual step of the semi-automated payment system: staff confirm a
 * claimed transaction against the bKash/Nagad/bank statement, and
 * everything that depends on it updates automatically.
 */
class PaymentVerification
{
    public function verify(Payment $payment, User $staff): void
    {
        $this->ensurePending($payment);

        DB::transaction(function () use ($payment, $staff): void {
            $payment->update([
                'status' => PaymentStatus::Verified,
                'verified_by' => $staff->id,
                'verified_at' => now(),
                'rejection_reason' => null,
            ]);

            match (true) {
                $payment->payable instanceof Order => $this->confirmOrder($payment->payable),
                $payment->payable instanceof Enrollment => $this->activateEnrollment($payment->payable, $staff),
                default => null,
            };
        });
    }

    public function reject(Payment $payment, User $staff, string $reason): void
    {
        $this->ensurePending($payment);

        $payment->update([
            'status' => PaymentStatus::Rejected,
            'verified_by' => $staff->id,
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    private function ensurePending(Payment $payment): void
    {
        if ($payment->status !== PaymentStatus::Pending) {
            throw new InvalidArgumentException('Only pending payments can be verified or rejected.');
        }
    }

    /**
     * A paid order is confirmed and its books leave stock.
     */
    private function confirmOrder(Order $order): void
    {
        if ($order->status !== OrderStatus::PendingVerification) {
            return;
        }

        $order->update(['status' => OrderStatus::Confirmed]);

        foreach ($order->items()->with('book')->get() as $item) {
            $item->book?->decrement('stock_quantity', min($item->quantity, $item->book->stock_quantity));
        }
    }

    /**
     * A paid enrollment becomes active, takes a seat in its batch and
     * closes its lead as won.
     */
    private function activateEnrollment(Enrollment $enrollment, User $staff): void
    {
        if ($enrollment->status !== EnrollmentStatus::PendingPayment) {
            return;
        }

        $enrollment->update([
            'status' => EnrollmentStatus::Active,
            'activated_at' => now(),
            'sold_by' => $enrollment->sold_by ?? $enrollment->lead?->assigned_to ?? $staff->id,
        ]);

        $enrollment->batch?->increment('seats_taken');
        $enrollment->lead?->update(['status' => LeadStatus::Enrolled]);
    }
}
