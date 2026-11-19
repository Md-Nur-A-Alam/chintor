<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Free 1:1 assessments: schedule a teacher, then record the result so
 * the sales team can recommend the right course.
 */
class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $assessments = Assessment::query()
            ->with(['lead', 'course', 'teacher'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByRaw('scheduled_at is null desc')
            ->orderBy('scheduled_at')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.assessments.index', compact('assessments', 'status'));
    }

    public function edit(Assessment $assessment): View
    {
        return view('admin.assessments.edit', [
            'assessment' => $assessment->load('lead'),
            'teachers' => Teacher::query()->active()->orderBy('sort_order')->get(),
            'courses' => Course::query()->published()->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(AssessmentStatus::class)],
            'teacher_id' => ['nullable', Rule::exists('teachers', 'id')],
            'course_id' => ['nullable', Rule::exists('courses', 'id')],
            'scheduled_at' => ['nullable', 'required_if:status,scheduled', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:240'],
            'meeting_url' => ['nullable', 'url', 'max:255'],
            'result_level' => ['nullable', 'required_if:status,completed', Rule::in(array_keys(Assessment::LEVELS))],
            'teacher_notes' => ['nullable', 'string', 'max:3000'],
            'recommended_course_id' => ['nullable', Rule::exists('courses', 'id')],
        ]);

        $assessment->update([...$validated, 'scheduled_by' => $assessment->scheduled_by ?? $request->user()->id]);

        // Keep the assessed level on the student record for future recommendations.
        if ($assessment->result_level && $assessment->lead?->student_id) {
            Student::whereKey($assessment->lead->student_id)->update(['assessed_level' => $assessment->result_level]);
        }

        return to_route('admin.assessments.index')->with('status', 'অ্যাসেসমেন্ট আপডেট হয়েছে।');
    }
}
