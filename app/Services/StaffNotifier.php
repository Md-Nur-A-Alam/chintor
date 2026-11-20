<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\StaffAlert;
use App\Support\Money;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * Sends in-app notifications to the right staff for each business event.
 *
 * Recipients are chosen by permission (see PermissionSeeder), so a new
 * role automatically receives the alerts that match its permissions.
 * Admins receive everything; developers only receive alerts sent to them
 * personally, so business events do not flood their inbox.
 */
class StaffNotifier
{
    public function newLead(Lead $lead): void
    {
        $this->toPermission('leads.assign', new StaffAlert(
            title: "নতুন লিড: {$lead->displayName()}",
            body: "{$lead->type->label()} · {$lead->phone}",
            url: route('admin.leads.show', $lead),
            icon: 'users',
        ));
    }

    public function assessmentRequested(Assessment $assessment): void
    {
        $this->toPermission('assessments.manage', new StaffAlert(
            title: 'ফ্রি অ্যাসেসমেন্টের অনুরোধ',
            body: "{$assessment->lead->displayName()} · {$assessment->lead->phone}",
            url: route('admin.assessments.edit', $assessment),
            icon: 'target',
        ));
    }

    /**
     * @param  int  $count  Leads assigned in this action.
     */
    public function leadsAssigned(User $assignee, int $count, ?Lead $singleLead, User $assignedBy): void
    {
        if ($assignee->is($assignedBy)) {
            return;
        }

        $assignee->notify(new StaffAlert(
            title: $count === 1 && $singleLead ? "আপনাকে লিড দেওয়া হয়েছে: {$singleLead->displayName()}" : "আপনাকে {$count}টি লিড দেওয়া হয়েছে",
            body: "দিয়েছেন: {$assignedBy->name}",
            url: $count === 1 && $singleLead ? route('admin.leads.show', $singleLead) : route('admin.leads.index', ['assignee' => $assignee->id]),
            icon: 'users',
        ));
    }

    public function newOrder(Order $order): void
    {
        $this->toPermission('orders.manage', new StaffAlert(
            title: "নতুন বইয়ের অর্ডার {$order->reference}",
            body: "{$order->customer_name} · ".Money::taka($order->total, 'bn'),
            url: route('admin.orders.show', $order),
            icon: 'book',
        ));
    }

    public function paymentSubmitted(Payment $payment): void
    {
        $this->toPermission('payments.verify', new StaffAlert(
            title: 'যাচাইয়ের জন্য নতুন পেমেন্ট',
            body: Money::taka($payment->amount, 'bn')." · {$payment->method->label()} · TrxID {$payment->transaction_id}",
            url: route('admin.payments.index'),
            icon: 'shield',
        ));
    }

    /**
     * Tell the seller / lead owner that their sale is confirmed.
     */
    public function paymentVerified(Payment $payment, User $verifiedBy): void
    {
        $payable = $payment->payable;

        if (! $payable instanceof Enrollment) {
            return;
        }

        $recipients = collect([$payable->seller, $payable->lead?->assignee])
            ->filter()
            ->unique('id')
            ->reject(fn (User $user) => $user->is($verifiedBy));

        Notification::send($recipients, new StaffAlert(
            title: "ভর্তি নিশ্চিত: {$payable->reference}",
            body: 'পেমেন্ট যাচাই হয়েছে — '.Money::taka($payment->amount, 'bn'),
            url: route('admin.enrollments.show', $payable),
            icon: 'check',
        ));
    }

    public function passwordReset(User $user, User $resetBy): void
    {
        $user->notify(new StaffAlert(
            title: 'আপনার পাসওয়ার্ড রিসেট করা হয়েছে',
            body: "{$resetBy->name} নতুন পাসওয়ার্ড দিয়েছেন। লগইনের পর নিজে বদলে নিন।",
            icon: 'shield',
        ));
    }

    /**
     * Active staff who hold the permission directly through a role, plus admins.
     */
    public function toPermission(string $permission, StaffAlert $alert): void
    {
        Notification::send($this->recipientsFor($permission), $alert);
    }

    /**
     * @return Collection<int, User>
     */
    public function recipientsFor(string $permission): Collection
    {
        return User::query()
            ->where('type', User::TypeStaff)
            ->where('is_active', true)
            ->with('roles.permissions')
            ->get()
            ->filter(fn (User $user) => $user->isSuperAdmin() || $user->hasPermission($permission))
            ->values();
    }
}
