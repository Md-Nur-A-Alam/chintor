<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCourseBatchRequest;
use App\Models\Course;
use App\Models\CourseBatch;
use Illuminate\Http\RedirectResponse;

/**
 * Live batches, managed from the course edit page.
 */
class CourseBatchController extends Controller
{
    public function store(SaveCourseBatchRequest $request, Course $course): RedirectResponse
    {
        $course->batches()->create($request->attributesForModel());

        return redirect(route('admin.courses.edit', $course).'#batches')->with('status', 'ব্যাচ যোগ হয়েছে।');
    }

    public function update(SaveCourseBatchRequest $request, Course $course, CourseBatch $batch): RedirectResponse
    {
        $batch->update($request->attributesForModel());

        return redirect(route('admin.courses.edit', $course).'#batches')->with('status', 'ব্যাচ আপডেট হয়েছে।');
    }

    public function destroy(Course $course, CourseBatch $batch): RedirectResponse
    {
        abort_unless(request()->user()->can('catalog.manage'), 403);

        $batch->delete();

        return redirect(route('admin.courses.edit', $course).'#batches')->with('status', 'ব্যাচ মুছে ফেলা হয়েছে।');
    }
}
