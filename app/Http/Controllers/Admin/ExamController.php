<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveExamRequest;
use App\Models\Exam;
use App\Models\LearningTopic;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Learning → Exams: ready-made Exam Arena exams. The form lists the bank's
 * active questions with checkboxes and filters them in the page (subject,
 * topic, class, text), so ticks are never lost; questions already in the
 * exam are always listed.
 */
class ExamController extends Controller
{
    private const PICKER_LIMIT = 500;

    public function index(): View
    {
        return view('admin.exams.index', [
            'exams' => Exam::query()->withCount(['questions', 'attempts'])->orderBy('sort_order')->latest('id')->get(),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Exam(['duration_minutes' => 5, 'question_count' => 10]));
    }

    public function store(SaveExamRequest $request): RedirectResponse
    {
        $exam = Exam::create($request->attributesForModel());
        $exam->questions()->sync($request->questionIds());

        return to_route('admin.exams.index')->with('status', 'পরীক্ষা তৈরি হয়েছে।');
    }

    public function edit(Exam $exam): View
    {
        return $this->form($exam);
    }

    public function update(SaveExamRequest $request, Exam $exam): RedirectResponse
    {
        $exam->update($request->attributesForModel());
        $exam->questions()->sync($request->questionIds());

        return to_route('admin.exams.index')->with('status', 'পরীক্ষা আপডেট হয়েছে।');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();

        return to_route('admin.exams.index')->with('status', 'পরীক্ষা মুছে ফেলা হয়েছে। প্রশ্নগুলো প্রশ্ন ব্যাংকে আছে।');
    }

    private function form(Exam $exam): View
    {
        $selectedIds = $exam->exists ? $exam->questions()->pluck('questions.id')->all() : [];

        $candidates = Question::query()->active()->with(['subject', 'topic'])->latest('id')->limit(self::PICKER_LIMIT)->get();

        $selected = Question::query()->with(['subject', 'topic'])->findMany($selectedIds);

        return view('admin.exams.form', [
            'exam' => $exam,
            'subjects' => LearningTopic::query()->subjects()->with('children')->get(),
            'questions' => $selected->concat($candidates)->unique('id')->values(),
            'selectedIds' => $selectedIds,
        ]);
    }
}
