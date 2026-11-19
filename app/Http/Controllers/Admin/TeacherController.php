<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherController extends Controller
{
    use HandlesImageInput;

    public function index(): View
    {
        return view('admin.teachers.index', [
            'teachers' => Teacher::query()->withCount('courses')->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.teachers.form', ['teacher' => new Teacher(['details_mode' => Teacher::DetailsRich, 'is_active' => true])]);
    }

    public function store(SaveTeacherRequest $request): RedirectResponse
    {
        Teacher::create([
            ...$request->attributesForModel(),
            'photo_url' => $this->resolveImageUrl($request, 'photo'),
        ]);

        return to_route('admin.teachers.index')->with('status', 'শিক্ষকের প্রোফাইল তৈরি হয়েছে।');
    }

    public function edit(Teacher $teacher): View
    {
        return view('admin.teachers.form', compact('teacher'));
    }

    public function update(SaveTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $teacher->update([
            ...$request->attributesForModel(),
            'photo_url' => $this->resolveImageUrl($request, 'photo', $teacher->photo_url),
        ]);

        return back()->with('status', 'শিক্ষকের প্রোফাইল আপডেট হয়েছে।');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return to_route('admin.teachers.index')->with('status', 'প্রোফাইলটি মুছে ফেলা হয়েছে।');
    }
}
