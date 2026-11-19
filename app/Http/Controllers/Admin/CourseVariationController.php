<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCourseVariationRequest;
use App\Models\Course;
use App\Models\CourseVariation;
use Illuminate\Http\RedirectResponse;

/**
 * Pricing options, managed from the course edit page.
 */
class CourseVariationController extends Controller
{
    public function store(SaveCourseVariationRequest $request, Course $course): RedirectResponse
    {
        $course->variations()->create($request->attributesForModel());

        return redirect(route('admin.courses.edit', $course).'#plans')->with('status', 'পেমেন্ট প্ল্যান যোগ হয়েছে।');
    }

    public function update(SaveCourseVariationRequest $request, Course $course, CourseVariation $variation): RedirectResponse
    {
        $variation->update($request->attributesForModel());

        return redirect(route('admin.courses.edit', $course).'#plans')->with('status', 'পেমেন্ট প্ল্যান আপডেট হয়েছে।');
    }

    public function destroy(Course $course, CourseVariation $variation): RedirectResponse
    {
        abort_unless(request()->user()->can('catalog.manage'), 403);

        $variation->delete();

        return redirect(route('admin.courses.edit', $course).'#plans')->with('status', 'পেমেন্ট প্ল্যান মুছে ফেলা হয়েছে।');
    }
}
