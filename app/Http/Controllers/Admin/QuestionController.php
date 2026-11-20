<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveQuestionRequest;
use App\Models\LearningTopic;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Learning → Question bank: MCQs tagged by subject, topic and class, used
 * by ready-made exams and by students' custom practice.
 */
class QuestionController extends Controller
{
    use HandlesImageInput;

    private const PER_PAGE = 30;

    public function index(Request $request): View
    {
        return view('admin.questions.index', [
            'questions' => Question::query()
                ->with(['subject', 'topic'])
                ->when($request->integer('subject'), fn ($query, int $subjectId) => $query->where('subject_id', $subjectId))
                ->when($request->integer('topic'), fn ($query, int $topicId) => $query->where('topic_id', $topicId))
                ->when($request->integer('class'), fn ($query, int $class) => $query->where('class_level', $class))
                ->when($request->filled('search'), fn ($query) => $query->where('body', 'like', '%'.$request->string('search')->toString().'%'))
                ->latest('id')
                ->paginate(self::PER_PAGE)
                ->withQueryString(),
            'subjects' => LearningTopic::query()->subjects()->with('children')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        // Keep the last-used subject/topic/class to speed up entering many questions.
        return $this->form(new Question([
            'subject_id' => $request->integer('subject') ?: null,
            'topic_id' => $request->integer('topic') ?: null,
            'class_level' => $request->integer('class') ?: null,
            'options' => ['', '', '', ''],
            'correct_option' => 0,
            'is_active' => true,
        ]));
    }

    public function store(SaveQuestionRequest $request): RedirectResponse
    {
        $question = Question::create([...$request->attributesForModel(), ...$this->images($request)]);

        if ($request->boolean('add_another')) {
            return to_route('admin.questions.create', ['subject' => $question->subject_id, 'topic' => $question->topic_id, 'class' => $question->class_level])
                ->with('status', 'প্রশ্ন যোগ হয়েছে। পরেরটি লিখুন।');
        }

        return to_route('admin.questions.index')->with('status', 'প্রশ্ন যোগ হয়েছে।');
    }

    public function edit(Question $question): View
    {
        return $this->form($question);
    }

    public function update(SaveQuestionRequest $request, Question $question): RedirectResponse
    {
        $question->update([...$request->attributesForModel(), ...$this->images($request, $question)]);

        return to_route('admin.questions.index')->with('status', 'প্রশ্ন আপডেট হয়েছে।');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $question->delete();

        return back()->with('status', 'প্রশ্ন মুছে ফেলা হয়েছে।');
    }

    /**
     * @return array{image_url: ?string, explanation_image_url: ?string}
     */
    private function images(SaveQuestionRequest $request, ?Question $question = null): array
    {
        return [
            'image_url' => $this->resolveImageUrl($request, 'image', $question?->image_url),
            'explanation_image_url' => $this->resolveImageUrl($request, 'explanation_image', $question?->explanation_image_url),
        ];
    }

    private function form(Question $question): View
    {
        return view('admin.questions.form', [
            'question' => $question,
            'subjects' => LearningTopic::query()->subjects()->with('children')->get(),
        ]);
    }
}
