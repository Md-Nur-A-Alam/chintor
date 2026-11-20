<?php

namespace App\Http\Controllers\Site;

use App\Enums\TestimonialAudience;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Campaign;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Program;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Services\HomePageContent;
use App\Services\SiteSettings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

/**
 * The homepage. Its hero, impact numbers and section order/visibility are
 * edited in admin → Website → Homepage; data is loaded only for the
 * sections that are switched on.
 */
class HomeController extends Controller
{
    private const FEATURED_COURSE_LIMIT = 6;

    private const FEATURED_BOOK_LIMIT = 4;

    private const TEACHER_LIMIT = 8;

    private const REVIEW_LIMIT = 6;

    private const FAQ_LIMIT = 8;

    public function __invoke(SiteSettings $settings, HomePageContent $homePage): View
    {
        $sections = $homePage->visibleSections();
        $visibleKeys = array_column($sections, 'key');
        $shows = fn (string $key): bool => in_array($key, $visibleKeys, true);

        $campaigns = Campaign::query()->running()->get();

        return view('site.home', [
            'hero' => $homePage->hero(),
            'heroCampaigns' => $campaigns->filter(fn (Campaign $campaign) => $campaign->show_in_hero && $campaign->image_url)->values(),
            'sections' => $sections,
            'stats' => $homePage->stats(),
            'campaigns' => $campaigns,
            'programs' => $shows('programs') ? $this->programs() : new Collection,
            'courses' => $shows('courses') ? $this->courses() : new Collection,
            'books' => $shows('books') ? $this->books() : new Collection,
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
