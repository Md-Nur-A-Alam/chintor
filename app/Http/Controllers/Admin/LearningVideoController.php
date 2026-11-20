<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveLearningVideoRequest;
use App\Models\LearningTopic;
use App\Models\LearningVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Learning → Videos: YouTube explainers for the free library (/learn);
 * up to six featured on the homepage.
 */
class LearningVideoController extends Controller
{
    public function index(): View
    {
        return view('admin.learning-videos.index', [
            'videos' => LearningVideo::query()->with(['subject', 'topic'])->orderByDesc('is_featured')->orderBy('sort_order')->latest('id')->paginate(30),
            'featuredCount' => LearningVideo::query()->where('is_featured', true)->count(),
        ]);
    }

    public function create(): View
    {
        return $this->form(new LearningVideo(['is_published' => true]));
    }

    public function store(SaveLearningVideoRequest $request): RedirectResponse
    {
        LearningVideo::create($request->attributesForModel());

        return to_route('admin.learning-videos.index')->with('status', 'ভিডিও যোগ হয়েছে।');
    }

    public function edit(LearningVideo $learningVideo): View
    {
        return $this->form($learningVideo);
    }

    public function update(SaveLearningVideoRequest $request, LearningVideo $learningVideo): RedirectResponse
    {
        $learningVideo->update($request->attributesForModel());

        return to_route('admin.learning-videos.index')->with('status', 'ভিডিও আপডেট হয়েছে।');
    }

    public function destroy(LearningVideo $learningVideo): RedirectResponse
    {
        $learningVideo->delete();

        return to_route('admin.learning-videos.index')->with('status', 'ভিডিও মুছে ফেলা হয়েছে।');
    }

    private function form(LearningVideo $video): View
    {
        return view('admin.learning-videos.form', [
            'video' => $video,
            'subjects' => LearningTopic::query()->subjects()->with('children')->get(),
        ]);
    }
}
