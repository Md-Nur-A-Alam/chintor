<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StartExamRequest;
use App\Http\Requests\Site\StartPracticeRequest;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\LearningTopic;
use App\Services\ExamArena;
use App\Support\Localization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Exam Arena: free MCQ exams set by staff and custom practice built by the
 * student. No login: each sitting has a private ULID link.
 */
class ArenaController extends Controller
{
    public function index(): View
    {
        return view('site.arena.index', [
            'exams' => Exam::query()->published()->withCount('questions')->orderBy('sort_order')->latest('id')->get(),
            'subjects' => LearningTopic::query()
                ->subjects()
                ->whereHas('questionsAsSubject', fn ($query) => $query->active())
                ->with('children')
                ->get(),
            'maxQuestions' => config('chintor.arena.max_questions'),
            'maxMinutesPerQuestion' => config('chintor.arena.max_minutes_per_question'),
        ]);
    }

    public function showExam(Exam $exam): View
    {
        abort_unless($exam->is_published, 404);

        return view('site.arena.exam', ['exam' => $exam]);
    }

    public function startExam(StartExamRequest $request, Exam $exam, ExamArena $arena): RedirectResponse
    {
        abort_unless($exam->is_published, 404);

        $attempt = $arena->startExam($exam, $request->student(), $request);

        return redirect(Localization::route('arena.attempts.show', ['attempt' => $attempt]));
    }

    public function startPractice(StartPracticeRequest $request, ExamArena $arena): RedirectResponse
    {
        $attempt = $arena->startPractice($request->settings(), $request->student(), $request);

        return redirect(Localization::route('arena.attempts.show', ['attempt' => $attempt]));
    }

    /**
     * The exam paper with its timer. Once submitted, the link shows the result.
     */
    public function show(ExamAttempt $attempt): View|RedirectResponse
    {
        if ($attempt->isSubmitted()) {
            return redirect(Localization::route('arena.attempts.result', ['attempt' => $attempt]));
        }

        return view('site.arena.sit', [
            'attempt' => $attempt->load('exam'),
            'questions' => $attempt->orderedQuestions(),
            'secondsLeft' => max(0, (int) now()->diffInSeconds($attempt->deadline_at, false)),
        ]);
    }

    public function submit(Request $request, ExamAttempt $attempt, ExamArena $arena): RedirectResponse
    {
        $answers = $request->input('answers', []);

        $arena->submit($attempt, is_array($answers) ? $answers : []);

        return redirect(Localization::route('arena.attempts.result', ['attempt' => $attempt]));
    }

    public function result(ExamAttempt $attempt): View|RedirectResponse
    {
        if (! $attempt->isSubmitted()) {
            return redirect(Localization::route('arena.attempts.show', ['attempt' => $attempt]));
        }

        $attempt->load('exam');

        return view('site.arena.result', [
            'attempt' => $attempt,
            'questions' => $attempt->resultsArePublished() ? $attempt->orderedQuestions() : collect(),
        ]);
    }
}
