<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        $programs = Program::query()
            ->published()
            ->withCount(['courses' => fn ($query) => $query->published()])
            ->orderBy('sort_order')
            ->get();

        return view('site.programs.index', compact('programs'));
    }

    public function show(Program $program): View
    {
        abort_unless($program->isPublished(), 404);

        $courses = $program->courses()
            ->published()
            ->with(['program', 'variations', 'openBatches'])
            ->orderBy('sort_order')
            ->get();

        $books = $program->books()->published()->with('authors')->orderBy('sort_order')->get();

        return view('site.programs.show', compact('program', 'courses', 'books'));
    }
}
