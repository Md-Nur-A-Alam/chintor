<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StoreEnrollmentRequest;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseVariation;
use App\Models\Enrollment;
use App\Models\PaymentAccount;
use App\Services\EnrollmentRequests;
use App\Support\Localization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function create(Request $request, Course $course): View
    {
        abort_unless($course->isPublished(), 404);

        $course->load(['variations' => fn ($query) => $query->where('is_active', true), 'openBatches']);

        abort_if($course->variations->isEmpty(), 404);

        return view('site.enrollments.create', [
            'course' => $course,
            'selectedVariationId' => $request->integer('plan') ?: $course->variations->first()->id,
            'selectedBatchId' => $request->integer('batch') ?: $course->openBatches->first(fn (CourseBatch $batch) => $batch->isOpenForEnrollment())?->id,
            'paymentAccounts' => PaymentAccount::query()->active()->get(),
            'refundPolicy' => $course->effectiveRefundPolicy(),
        ]);
    }

    public function store(StoreEnrollmentRequest $request, Course $course, EnrollmentRequests $enrollments): RedirectResponse
    {
        abort_unless($course->isPublished(), 404);

        $batch = $request->validated('course_batch_id') ? CourseBatch::find($request->validated('course_batch_id')) : null;

        if ($batch !== null && ! $batch->isOpenForEnrollment()) {
            return back()->withInput()->withErrors(['course_batch_id' => __('site.course.seats_full')]);
        }

        $enrollment = $enrollments->submit(
            $course,
            CourseVariation::findOrFail($request->validated('course_variation_id')),
            $batch,
            $request->safe()->only(['guardian_name', 'student_name', 'phone', 'class_level', 'payment_method', 'sender_number', 'transaction_id']),
            $request,
        );

        return redirect(Localization::route('enrollments.success', ['enrollment' => $enrollment->ulid]));
    }

    /**
     * Reached by an unguessable ULID, so no login is needed.
     */
    public function success(Enrollment $enrollment): View
    {
        $enrollment->load(['course', 'batch', 'variation', 'payments']);

        return view('site.enrollments.success', compact('enrollment'));
    }
}
