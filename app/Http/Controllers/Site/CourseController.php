<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $programSlug = $request->string('program')->toString();
        $classLevel = $request->integer('class') ?: null;

        $courses = Course::query()
            ->published()
            ->with(['program', 'variations', 'openBatches'])
            ->when($programSlug !== '', fn ($query) => $query->whereRelation('program', 'slug', $programSlug))
            ->when($classLevel !== null, fn ($query) => $query
                ->where('class_from', '<=', $classLevel)
                ->where('class_to', '>=', $classLevel))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        return view('site.courses.index', [
            'courses' => $courses,
            'programs' => Program::query()->published()->orderBy('sort_order')->get(),
            'selectedProgram' => $programSlug,
            'selectedClass' => $classLevel,
        ]);
    }

    public function show(Course $course): View
    {
        abort_unless($course->isPublished() && $course->program->isPublished(), 404);

        $course->load([
            'program',
            'teachers' => fn ($query) => $query->active(),
            'variations' => fn ($query) => $query->where('is_active', true),
            'openBatches',
            'faqs',
        ]);

        return view('site.courses.show', [
            'course' => $course,
            'refundPolicy' => $course->effectiveRefundPolicy(),
            'testimonials' => Testimonial::query()->visible()->where('course_id', $course->id)->get(),
        ]);
    }
}
