<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Program;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Services\SiteSettings;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const FEATURED_COURSE_LIMIT = 6;

    private const FEATURED_BOOK_LIMIT = 4;

    private const TEACHER_LIMIT = 8;

    public function __invoke(SiteSettings $settings): View
    {
        $programs = Program::query()
            ->published()
            ->withCount(['courses' => fn ($query) => $query->published()])
            ->orderBy('sort_order')
            ->get();

        $courses = Course::query()
            ->published()
            ->with(['program', 'variations', 'openBatches'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(self::FEATURED_COURSE_LIMIT)
            ->get();

        $books = Book::query()
            ->published()
            ->with('authors')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(self::FEATURED_BOOK_LIMIT)
            ->get();

        return view('site.home', [
            'programs' => $programs,
            'courses' => $courses,
            'books' => $books,
            'teachers' => Teacher::query()->active()->orderBy('sort_order')->limit(self::TEACHER_LIMIT)->get(),
            'testimonials' => Testimonial::query()->visible()->limit(6)->get(),
            'faqs' => Faq::query()->general()->limit(8)->get(),
            'refundPolicy' => $settings->refundPolicy(),
        ]);
    }
}
