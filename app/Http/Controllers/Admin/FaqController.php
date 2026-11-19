<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveFaqRequest;
use App\Models\Course;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('admin.faqs.index', [
            'faqs' => Faq::query()->with('faqable')->orderBy('faqable_id')->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.faqs.form', ['faq' => new Faq(['is_active' => true]), 'courses' => $this->courseOptions()]);
    }

    public function store(SaveFaqRequest $request): RedirectResponse
    {
        Faq::create($request->attributesForModel());

        return to_route('admin.faqs.index')->with('status', 'প্রশ্নোত্তর যোগ হয়েছে।');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.form', ['faq' => $faq, 'courses' => $this->courseOptions()]);
    }

    public function update(SaveFaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->attributesForModel());

        return to_route('admin.faqs.index')->with('status', 'প্রশ্নোত্তর আপডেট হয়েছে।');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return to_route('admin.faqs.index')->with('status', 'প্রশ্নোত্তর মুছে ফেলা হয়েছে।');
    }

    /**
     * @return array<int, string>
     */
    private function courseOptions(): array
    {
        return Course::orderBy('sort_order')->get()->mapWithKeys(fn (Course $course) => [$course->id => $course->translate('title', 'bn')])->all();
    }
}
