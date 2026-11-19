<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StoreLeadRequest;
use App\Models\Course;
use App\Models\Lead;
use App\Models\Program;
use App\Services\LeadCapture;
use App\Support\Localization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Lead capture: the 3-step "চলো একসাথে শিখি" wizard and the short
 * call-back / assessment / waitlist forms used across the site.
 */
class LeadController extends Controller
{
    public function create(Request $request): View
    {
        $programs = Program::query()
            ->published()
            ->with(['courses' => fn ($query) => $query->published()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (Program $program) => $program->courses->isNotEmpty());

        return view('site.leads.create', [
            'programs' => $programs,
            'preselectedCourseId' => Course::query()->published()->whereKey($request->integer('course'))->value('id'),
            'wantsAssessment' => $request->boolean('assessment'),
        ]);
    }

    public function store(StoreLeadRequest $request, LeadCapture $leads): RedirectResponse
    {
        $lead = $leads->capture(
            $request->leadType(),
            $request->contactDetails(),
            $request,
            [
                'course_ids' => $request->validated('course_ids') ?? [],
                'program_ids' => $request->validated('program_ids') ?? [],
            ],
        );

        return redirect(Localization::route('leads.thanks'))->with('lead_reference', $lead->reference);
    }

    public function thanks(Request $request): View|RedirectResponse
    {
        $reference = $request->session()->get('lead_reference');

        if ($reference === null) {
            return redirect(Localization::route('home'));
        }

        return view('site.leads.thanks', [
            'lead' => Lead::firstWhere('reference', $reference),
        ]);
    }
}
