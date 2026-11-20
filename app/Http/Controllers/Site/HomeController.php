<?php

namespace App\Http\Controllers\Site;

use App\Enums\BatchStatus;
use App\Enums\TestimonialAudience;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Campaign;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Faq;
use App\Models\LearningVideo;
use App\Models\Program;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Services\HomePageContent;
use App\Services\SiteSettings;
use App\Support\Localization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

/**
 * The homepage. Its hero, impact numbers and section order/visibility are
 * edited in admin → Website → Homepage; data is loaded only for the
 * sections that are switched on.
 */
class HomeController extends Controller
{
    private const FEATURED_COURSE_LIMIT = 9;

    private const FEATURED_BOOK_LIMIT = 4;

    private const TEACHER_LIMIT = 8;

    private const REVIEW_LIMIT = 12;

    private const FAQ_LIMIT = 8;

    private const HIGHLIGHT_COURSE_LIMIT = 12;

    public function __invoke(SiteSettings $settings, HomePageContent $homePage): View
    {
        $sections = $homePage->visibleSections();
        $visibleKeys = array_column($sections, 'key');
        $shows = fn (string $key): bool => in_array($key, $visibleKeys, true);

        $campaigns = Campaign::query()->running()->get();

        $hero = $homePage->hero();

        return view('site.home', [
            'hero' => $hero,
            'heroBanner' => $this->heroBanner($hero, $campaigns),
            'nextBatch' => $this->nextBatch(),
            'heroCampaigns' => $campaigns->filter(fn (Campaign $campaign) => $campaign->show_in_hero && $campaign->image_url)->values(),
            'sections' => $sections,
            'stats' => $homePage->stats(),
            'highlightLinks' => $shows('highlights') ? $this->highlightLinks() : [],
            'campaigns' => $campaigns,
            'programs' => $shows('programs') ? $this->programs() : new Collection,
            'courses' => $shows('courses') ? $this->courses() : new Collection,
            'books' => $shows('books') ? $this->books() : new Collection,
            'featuredVideos' => $shows('videos')
                ? LearningVideo::query()->published()->where('is_featured', true)->with(['subject', 'topic'])->orderBy('sort_order')->limit(LearningVideo::MAX_FEATURED)->get()
                : new Collection,
            'teachers' => $shows('teachers') ? Teacher::query()->active()->orderBy('sort_order')->limit(self::TEACHER_LIMIT)->get() : new Collection,
            'parentReviews' => $shows('parent_reviews') ? $this->reviews(TestimonialAudience::Parent) : new Collection,
            'studentReviews' => $shows('student_reviews') ? $this->reviews(TestimonialAudience::Student) : new Collection,
            'faqs' => $shows('faq') ? Faq::query()->general()->limit(self::FAQ_LIMIT)->get() : new Collection,
            'refundPolicy' => $settings->refundPolicy(),
        ]);
    }

    /**
     * @return Collection<int, Program>
     */
    private function programs(): Collection
    {
        return Program::query()
            ->published()
            ->withCount(['courses' => fn ($query) => $query->published()])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * The full-width banner hero, if any: a running campaign with a wide
     * banner wins (showing its own title, summary, button and countdown),
     * otherwise the homepage's own brand banner with the hero texts.
     *
     * @param  array<string, mixed>  $hero
     * @param  Collection<int, Campaign>  $campaigns
     * @return array{banner: array{desktop: ?string, mobile: ?string, align: string, overlay: int}, content: array<string, mixed>}|null
     */
    private function heroBanner(array $hero, Collection $campaigns): ?array
    {
        $campaign = $campaigns->first(fn (Campaign $campaign) => $campaign->show_in_hero && $campaign->banner_url);

        if ($campaign !== null) {
            $link = $campaign->localizedLink();

            return [
                'banner' => $campaign->heroBanner(),
                'content' => [
                    'eyebrow' => __("site.home.channels.{$campaign->channel->value}"),
                    'title' => (string) $campaign->translate('title'),
                    'subtitle' => (string) $campaign->translate('summary'),
                    'words' => [],
                    'endsAt' => $campaign->ends_at,
                    'buttons' => $link ? [['label' => $campaign->translate('button_label') ?: __('site.home.campaign.details'), 'url' => $link, 'style' => 'primary']] : [],
                ],
            ];
        }

        if ($hero['banner'] === null) {
            return null;
        }

        return [
            'banner' => $hero['banner'],
            'content' => [
                'eyebrow' => $hero['eyebrow'],
                'title' => $hero['title'],
                'subtitle' => $hero['subtitle'],
                'words' => $hero['rotating_words'],
                'endsAt' => null,
                'buttons' => $hero['buttons'],
            ],
        ];
    }

    /**
     * The soonest open batch of a published course, for the hero's "next batch" card.
     */
    private function nextBatch(): ?CourseBatch
    {
        return CourseBatch::query()
            ->whereIn('status', [BatchStatus::Upcoming, BatchStatus::Enrolling])
            ->whereDate('starts_on', '>=', today())
            ->whereHas('course', fn ($query) => $query->published())
            ->with('course')
            ->orderBy('starts_on')
            ->first();
    }

    /**
     * Every published program and course, for the moving highlight lane.
     *
     * @return list<array{label: string, url: string}>
     */
    private function highlightLinks(): array
    {
        $programs = Program::query()->published()->orderBy('sort_order')->get(['id', 'slug', 'name'])
            ->map(fn (Program $program): array => [
                'label' => $program->translate('name'),
                'url' => Localization::route('programs.show', ['program' => $program]),
            ]);

        $courses = Course::query()->published()->orderByDesc('is_featured')->orderBy('sort_order')->limit(self::HIGHLIGHT_COURSE_LIMIT)->get(['id', 'slug', 'title'])
            ->map(fn (Course $course): array => [
                'label' => $course->translate('title'),
                'url' => Localization::route('courses.show', ['course' => $course]),
            ]);

        return $programs->concat($courses)->values()->all();
    }

    /**
     * @return Collection<int, Course>
     */
    private function courses(): Collection
    {
        return Course::query()
            ->published()
            ->with(['program', 'variations', 'openBatches'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(self::FEATURED_COURSE_LIMIT)
            ->get();
    }

    /**
     * @return Collection<int, Book>
     */
    private function books(): Collection
    {
        return Book::query()
            ->published()
            ->with('authors')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(self::FEATURED_BOOK_LIMIT)
            ->get();
    }

    /**
     * @return Collection<int, Testimonial>
     */
    private function reviews(TestimonialAudience $audience): Collection
    {
        return Testimonial::query()
            ->visible()
            ->where('audience', $audience)
            ->with('course')
            ->limit(self::REVIEW_LIMIT)
            ->get();
    }
}
