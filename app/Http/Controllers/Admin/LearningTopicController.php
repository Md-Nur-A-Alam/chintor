<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveLearningTopicRequest;
use App\Models\LearningTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Learning → Subjects & topics, on one page: subjects with their topics,
 * add / rename / delete. Used by videos and the question bank.
 */
class LearningTopicController extends Controller
{
    public function index(): View
    {
        return view('admin.learning-topics.index', [
            'subjects' => LearningTopic::query()
                ->subjects()
                ->withCount(['videosAsSubject', 'questionsAsSubject'])
                ->with('children')
                ->get(),
        ]);
    }

    public function store(SaveLearningTopicRequest $request): RedirectResponse
    {
        LearningTopic::create([...$request->attributesForModel(), 'parent_id' => $request->validated('parent_id')]);

        return back()->with('status', 'যোগ হয়েছে।');
    }

    public function update(SaveLearningTopicRequest $request, LearningTopic $learningTopic): RedirectResponse
    {
        $learningTopic->update($request->attributesForModel());

        return back()->with('status', 'আপডেট হয়েছে।');
    }

    /**
     * A subject that still has videos or questions cannot be deleted.
     */
    public function destroy(LearningTopic $learningTopic): RedirectResponse
    {
        $isUsed = $learningTopic->parent_id === null
            && ($learningTopic->videosAsSubject()->exists() || $learningTopic->questionsAsSubject()->exists());

        if ($isUsed) {
            return back()->withErrors(['general' => 'এই বিষয়ে ভিডিও বা প্রশ্ন আছে — আগে সেগুলো অন্য বিষয়ে সরান বা মুছুন।']);
        }

        $learningTopic->delete();

        return back()->with('status', 'মুছে ফেলা হয়েছে।');
    }
}
