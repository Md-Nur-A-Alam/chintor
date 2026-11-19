<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTestimonialRequest;
use App\Models\Course;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    use HandlesImageInput;

    public function index(): View
    {
        return view('admin.testimonials.index', [
            'testimonials' => Testimonial::query()->with('course')->orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.testimonials.form', ['testimonial' => new Testimonial, 'courses' => $this->courseOptions()]);
    }

    public function store(SaveTestimonialRequest $request): RedirectResponse
    {
        Testimonial::create([...$request->attributesForModel(), 'photo_url' => $this->resolveImageUrl($request, 'photo')]);

        return to_route('admin.testimonials.index')->with('status', 'রিভিউ যোগ হয়েছে।');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.form', ['testimonial' => $testimonial, 'courses' => $this->courseOptions()]);
    }

    public function update(SaveTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([...$request->attributesForModel(), 'photo_url' => $this->resolveImageUrl($request, 'photo', $testimonial->photo_url)]);

        return to_route('admin.testimonials.index')->with('status', 'রিভিউ আপডেট হয়েছে।');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return to_route('admin.testimonials.index')->with('status', 'রিভিউ মুছে ফেলা হয়েছে।');
    }

    /**
     * @return array<int, string>
     */
    private function courseOptions(): array
    {
        return Course::orderBy('sort_order')->get()->mapWithKeys(fn (Course $course) => [$course->id => $course->translate('title', 'bn')])->all();
    }
}
