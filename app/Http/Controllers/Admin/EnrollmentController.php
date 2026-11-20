<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrollmentStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseVariation;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Services\EnrollmentRequests;
use App\Services\StaffNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = $request->string('search')->trim()->toString();

        $enrollments = Enrollment::query()
            ->with(['student', 'guardian', 'course', 'batch', 'seller'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhereRelation('guardian', 'phone', 'like', "%{$search}%")
                    ->orWhereRelation('student', 'name', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.enrollments.index', compact('enrollments', 'status', 'search'));
    }

    /**
     * Sales executives enrol a lead after a call (with an optional discount).
     */
    public function create(Request $request): View
    {
        $lead = Lead::findOrFail($request->integer('lead'));
        abort_unless($lead->isVisibleTo($request->user()), 403);

        $courses = Course::query()
            ->published()
            ->with(['variations' => fn ($query) => $query->where('is_active', true), 'openBatches'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.enrollments.create', [
            'lead' => $lead->load('interests'),
            'courses' => $courses,
            'maxDiscountPercent' => $request->user()->maxDiscountPercent(),
        ]);
    }

    public function store(Request $request, EnrollmentRequests $enrollments): RedirectResponse
    {
        $lead = Lead::findOrFail($request->integer('lead_id'));
        abort_unless($lead->isVisibleTo($request->user()), 403);

        $validated = $request->validate([
            'lead_id' => ['required'],
            'course_variation_id' => ['required', Rule::exists('course_variations', 'id')],
            'course_batch_id' => ['nullable', Rule::exists('course_batches', 'id')],
            'guardian_name' => ['required', 'string', 'max:100'],
            'student_name' => ['required', 'string', 'max:100'],
            'class_level' => ['nullable', 'integer', 'between:0,12'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['nullable', 'required_with:transaction_id', Rule::enum(PaymentMethod::class)],
            'sender_number' => ['nullable', 'string', 'max:20'],
            'transaction_id' => ['nullable', 'string', 'max:50', Rule::unique('payments', 'transaction_id')->where('method', $request->input('payment_method'))],
        ]);

        $variation = CourseVariation::with('course')->findOrFail($validated['course_variation_id']);
        $batch = isset($validated['course_batch_id']) ? CourseBatch::where('course_id', $variation->course_id)->findOrFail($validated['course_batch_id']) : null;

        $this->ensureDiscountWithinLimit((int) ($validated['discount_amount'] ?? 0), $variation, $request);

        $enrollment = $enrollments->createForLead($lead, $variation->course, $variation, $batch, $validated, $request->user());

        return to_route('admin.enrollments.show', $enrollment)->with('status', "এনরোলমেন্ট {$enrollment->reference} তৈরি হয়েছে। পেমেন্ট যাচাই হলে সক্রিয় হবে।");
    }

    public function show(Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'guardian', 'course', 'batch', 'variation', 'lead', 'seller', 'payments.verifier']);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function update(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(EnrollmentStatus::class)],
            'first_class_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $enrollment->update($validated);

        return back()->with('status', 'এনরোলমেন্ট আপডেট হয়েছে।');
    }

    /**
     * Staff record a payment received by phone, bank or cash; it then waits
     * for verification like any other payment.
     */
    public function storePayment(Request $request, Enrollment $enrollment, StaffNotifier $notifier): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'amount' => ['required', 'integer', 'min:1'],
            'sender_number' => ['nullable', 'string', 'max:20'],
            'transaction_id' => ['nullable', 'string', 'max:50', Rule::unique('payments', 'transaction_id')->where('method', $request->input('method'))],
        ]);

        $payment = $enrollment->payments()->create([
            ...$validated,
            'status' => PaymentStatus::Pending,
            'submitted_by' => 'staff',
        ]);

        $notifier->paymentSubmitted($payment);

        return back()->with('status', 'পেমেন্ট যোগ হয়েছে — যাচাইয়ের অপেক্ষায়।');
    }

    private function ensureDiscountWithinLimit(int $discount, CourseVariation $variation, Request $request): void
    {
        $maxPercent = $request->user()->maxDiscountPercent();
        $maxAmount = (int) floor($variation->payablePrice() * $maxPercent / 100);

        if ($discount > $maxAmount) {
            throw ValidationException::withMessages([
                'discount_amount' => "আপনার রোলে সর্বোচ্চ {$maxPercent}% (৳{$maxAmount}) ডিসকাউন্ট দেওয়া যায়।",
            ]);
        }
    }
}
