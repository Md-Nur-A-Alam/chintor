<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\LearningTopic;
use App\Models\LearningVideo;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The free video library (/learn): short explainers anyone can watch,
 * filtered by subject and topic through the query string (?subject=maths&topic=geometry),
 * so a filtered list can be shared as a link.
 */
class LearnController extends Controller
{
    private const PER_PAGE = 12;

    public function index(Request $request): View
    {
        $subjects = LearningTopic::query()
            ->subjects()
            ->whereHas('videosAsSubject', fn ($query) => $query->published())
            ->with('children')
            ->get();

        $subject = $subjects->firstWhere('slug', $request->string('subject')->toString());
        $topic = $subject?->children->firstWhere('slug', $request->string('topic')->toString());

        $videos = LearningVideo::query()
            ->published()
            ->with(['subject', 'topic'])
            ->when($subject, fn ($query) => $query->where('subject_id', $subject->id))
            ->when($topic, fn ($query) => $query->where('topic_id', $topic->id))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('site.learn.index', [
            'subjects' => $subjects,
            'subject' => $subject,
            'topic' => $topic,
            'videos' => $videos,
        ]);
    }
}
