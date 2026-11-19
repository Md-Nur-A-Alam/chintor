<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCourseRequest;
use App\Models\Course;
use App\Models\Program;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    use HandlesImageInput;

    public function index(Request $request): View
    {
        $programId = $request->integer('program') ?: null;

        $courses = Course::query()
            ->with('program')
            ->withCount(['variations', 'batches', 'enrollments'])
            ->when($programId, fn ($query) => $query->where('program_id', $programId))
            ->orderBy('program_id')
            ->orderBy('sort_order')
            ->paginate(25)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'programs' => Program::orderBy('sort_order')->get(),
            'programId' => $programId,
        ]);
    }

    public function create(): View
    {
        return view('admin.courses.form', $this->formData(new Course(['status' => PublishStatus::Draft])));
    }

    public function store(SaveCourseRequest $request): RedirectResponse
    {
        $course = Course::create([
            ...$request->attributesForModel(),
            'thumbnail_url' => $this->resolveImageUrl($request, 'thumbnail'),
            'published_at' => $request->validated('status') === PublishStatus::Published->value ? now() : null,
        ]);

        $this->syncTeachers($course, $request->validated('teacher_ids') ?? []);

        return to_route('admin.courses.edit', $course)->with('status', 'কোর্স তৈরি হয়েছে। এবার পেমেন্ট প্ল্যান ও ব্যাচ যোগ করুন।');
    }

    public function edit(Course $course): View
    {
        $course->load(['teachers', 'variations', 'batches']);

        return view('admin.courses.form', $this->formData($course));
    }

    public function update(SaveCourseRequest $request, Course $course): RedirectResponse
    {
        $attributes = $request->attributesForModel();

        $course->update([
            ...$attributes,
            'thumbnail_url' => $this->resolveImageUrl($request, 'thumbnail', $course->thumbnail_url),
            'published_at' => $attributes['status'] === PublishStatus::Published ? ($course->published_at ?? now()) : $course->published_at,
        ]);

        $this->syncTeachers($course, $request->validated('teacher_ids') ?? []);

        return back()->with('status', 'কোর্স আপডেট হয়েছে।');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->enrollments()->exists()) {
            return back()->withErrors(['general' => 'এই কোর্সে এনরোলমেন্ট আছে, তাই মোছা যাবে না। চাইলে অবস্থা “আর্কাইভ” করুন।']);
        }

        $course->delete();

        return to_route('admin.courses.index')->with('status', 'কোর্সটি মুছে ফেলা হয়েছে।');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Course $course): array
    {
        return [
            'course' => $course,
            'programs' => Program::orderBy('sort_order')->get(),
            'teachers' => Teacher::query()->active()->orderBy('sort_order')->get(),
        ];
    }

    /**
     * @param  list<int>  $teacherIds  In display order.
     */
    private function syncTeachers(Course $course, array $teacherIds): void
    {
        $course->teachers()->sync(
            collect($teacherIds)->values()->mapWithKeys(fn (int $id, int $order) => [$id => ['sort_order' => $order]])->all(),
        );
    }
}
