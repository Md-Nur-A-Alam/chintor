<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        return view('site.teachers.index', [
            'teachers' => Teacher::query()->active()->orderBy('sort_order')->get(),
        ]);
    }

    public function show(Teacher $teacher): View
    {
        abort_unless($teacher->is_active, 404);

        $teacher->load(['courses' => fn ($query) => $query->published()->with(['program', 'variations', 'openBatches'])]);

        return view('site.teachers.show', compact('teacher'));
    }
}
