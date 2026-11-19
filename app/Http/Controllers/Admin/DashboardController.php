<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssessmentStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Support\Localization;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * One dashboard for every role: each card appears only for staff who hold
 * the permission behind it, so a sales executive sees their own pipeline
 * while a manager sees the whole business.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('roles');

        return view('admin.dashboard', [
            'staff' => $user,
            'cards' => $this->cards($user),
            'recentLeads' => $user->can('leads.work')
                ? Lead::query()->visibleTo($user)->open()->with('assignee')->latest()->limit(8)->get()
                : collect(),
        ]);
    }

    /**
     * @return list<array{label: string, value: string, icon: string, url: ?string, tone: string}>
     */
    private function cards(User $user): array
    {
        $cards = [];

        if ($user->can('leads.work')) {
            $myOpenLeads = Lead::query()->where('assigned_to', $user->id)->open();

            $cards[] = $this->card('আমার খোলা লিড', (clone $myOpenLeads)->count(), 'users', route('admin.leads.index', ['assignee' => $user->id]));
            $cards[] = $this->card('আজকের ফলো-আপ', (clone $myOpenLeads)->where('next_follow_up_at', '<=', now()->endOfDay())->count(), 'calendar', route('admin.leads.index', ['due' => 1]), 'warning');
        }

        if ($user->can('leads.view_all')) {
            $cards[] = $this->card('দায়িত্বহীন লিড (কিউ)', Lead::query()->unassigned()->open()->count(), 'layers', route('admin.leads.index', ['assignee' => 'unassigned']), 'danger');
            $cards[] = $this->card('আজকের নতুন লিড', Lead::query()->whereDate('created_at', today())->count(), 'star', route('admin.leads.index'));
        }

        if ($user->can('assessments.manage')) {
            $cards[] = $this->card('অ্যাসেসমেন্ট অনুরোধ', Assessment::query()->where('status', AssessmentStatus::Requested)->count(), 'target', route('admin.assessments.index', ['status' => 'requested']), 'warning');
        }

        if ($user->can('payments.verify')) {
            $cards[] = $this->card('যাচাই বাকি পেমেন্ট', Payment::query()->where('status', PaymentStatus::Pending)->count(), 'shield', route('admin.payments.index'), 'danger');
        }

        if ($user->can('orders.manage')) {
            $cards[] = $this->card('পাঠানোর অপেক্ষায় অর্ডার', Order::query()->whereIn('status', [OrderStatus::Confirmed, OrderStatus::Packed])->count(), 'truck', route('admin.orders.index', ['status' => 'confirmed']));
        }

        if ($user->can('enrollments.manage')) {
            $cards[] = $this->card('এ মাসে সক্রিয় ভর্তি', Enrollment::query()->where('status', EnrollmentStatus::Active)->where('activated_at', '>=', now()->startOfMonth())->count(), 'check', route('admin.enrollments.index', ['status' => 'active']), 'success');
        }

        if ($user->can('reports.view')) {
            $revenue = (int) Payment::query()->where('status', PaymentStatus::Verified)->where('verified_at', '>=', now()->startOfMonth())->sum('amount');
            $cards[] = ['label' => 'এ মাসে যাচাইকৃত আয়', 'value' => Money::taka($revenue, 'bn'), 'icon' => 'wallet', 'url' => null, 'tone' => 'success'];
        }

        return $cards;
    }

    /**
     * @return array{label: string, value: string, icon: string, url: ?string, tone: string}
     */
    private function card(string $label, int $count, string $icon, ?string $url, string $tone = 'info'): array
    {
        return ['label' => $label, 'value' => Localization::digits($count, 'bn'), 'icon' => $icon, 'url' => $url, 'tone' => $tone];
    }
}
