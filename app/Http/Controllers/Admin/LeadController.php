<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The central lead queue. Marketing, managers and admins see every lead
 * and assign them to sales executives; a sales executive only sees and
 * works the leads assigned to them.
 */
class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $request->only(['status', 'type', 'assignee', 'search', 'due']);

        $leads = Lead::query()
            ->visibleTo($user)
            ->with(['assignee', 'interests.course', 'interests.program'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when(($filters['assignee'] ?? null) === 'unassigned', fn ($query) => $query->unassigned())
            ->when(is_numeric($filters['assignee'] ?? null), fn ($query) => $query->where('assigned_to', (int) $filters['assignee']))
            ->when(($filters['due'] ?? null) === '1', fn ($query) => $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now()->endOfDay()))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(function ($query) use ($search): void {
                $query->where('phone', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'filters' => $filters,
            'salesTeam' => $this->salesTeam(),
            'canAssign' => $user->can('leads.assign'),
            'canSeeAll' => $user->can('leads.view_all'),
        ]);
    }

    public function show(Request $request, Lead $lead): View
    {
        abort_unless($lead->isVisibleTo($request->user()), 403);

        $lead->load(['assignee', 'interests.course', 'interests.program', 'interests.book', 'activities.user', 'assessment.teacher', 'enrollments.course']);

        return view('admin.leads.show', [
            'lead' => $lead,
            'salesTeam' => $this->salesTeam(),
            'canAssign' => $request->user()->can('leads.assign'),
        ]);
    }

    /**
     * Status, follow-up date and lost reason; every change is logged on the timeline.
     */
    public function update(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($lead->isVisibleTo($request->user()) && $request->user()->can('leads.work'), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(LeadStatus::class)],
            'next_follow_up_at' => ['nullable', 'date'],
            'lost_reason' => ['nullable', 'required_if:status,lost', 'string', 'max:255'],
        ]);

        $previousStatus = $lead->status;
        $lead->update($validated);

        if ($previousStatus !== $lead->status) {
            $lead->activities()->create([
                'user_id' => $request->user()->id,
                'type' => LeadActivity::TypeStatusChange,
                'body' => "{$previousStatus->label()} → {$lead->status->label()}",
                'meta' => ['from' => $previousStatus->value, 'to' => $lead->status->value],
            ]);
        }

        return back()->with('status', 'লিড আপডেট হয়েছে।');
    }

    /**
     * Log a call or a note.
     */
    public function storeActivity(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($lead->isVisibleTo($request->user()) && $request->user()->can('leads.work'), 403);

        $validated = $request->validate([
            'type' => ['required', Rule::in([LeadActivity::TypeCall, LeadActivity::TypeNote])],
            'outcome' => ['nullable', 'required_if:type,call', Rule::in(array_keys(LeadActivity::CALL_OUTCOMES))],
            'body' => ['nullable', 'required_if:type,note', 'string', 'max:2000'],
        ]);

        $lead->activities()->create([...$validated, 'user_id' => $request->user()->id]);

        if ($validated['type'] === LeadActivity::TypeCall && $lead->status === LeadStatus::New) {
            $lead->update(['status' => LeadStatus::Contacted]);
        }

        return back()->with('status', 'টাইমলাইনে যোগ হয়েছে।');
    }

    /**
     * Manual distribution from the central queue (one or many leads).
     */
    public function assign(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('leads.assign'), 403);

        $validated = $request->validate([
            'lead_ids' => ['required', 'array', 'min:1'],
            'lead_ids.*' => ['integer', Rule::exists('leads', 'id')],
            'assigned_to' => ['nullable', Rule::in($this->salesTeam()->pluck('id')->all())],
        ]);

        $assignee = isset($validated['assigned_to']) ? User::find($validated['assigned_to']) : null;

        DB::transaction(function () use ($validated, $assignee, $request): void {
            Lead::whereIn('id', $validated['lead_ids'])->each(function (Lead $lead) use ($assignee, $request): void {
                $lead->update(['assigned_to' => $assignee?->id, 'assigned_at' => $assignee ? now() : null]);

                $lead->activities()->create([
                    'user_id' => $request->user()->id,
                    'type' => LeadActivity::TypeAssignment,
                    'body' => $assignee ? "দায়িত্ব: {$assignee->name}" : 'দায়িত্ব সরানো হয়েছে (কিউতে ফেরত)',
                ]);
            });
        });

        $count = count($validated['lead_ids']);

        return back()->with('status', $assignee ? "{$count}টি লিড {$assignee->name}-কে দেওয়া হয়েছে।" : "{$count}টি লিড কিউতে ফেরত গেছে।");
    }

    /**
     * Active staff who can receive leads.
     *
     * @return Collection<int, User>
     */
    private function salesTeam(): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->whereIn('name', [Role::SalesExecutive, Role::Manager, Role::Marketing]))
            ->orderBy('name')
            ->get();
    }
}
