<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SavePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * About, contact and policy pages (Markdown). The footer links to the
 * slugs about, contact, refund-policy, privacy-policy, terms and
 * delivery-policy.
 */
class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', ['pages' => Page::orderBy('type')->orderBy('slug')->get()]);
    }

    public function create(): View
    {
        return view('admin.pages.form', ['page' => new Page(['type' => 'policy', 'status' => PublishStatus::Draft])]);
    }

    public function store(SavePageRequest $request): RedirectResponse
    {
        Page::create($request->attributesForModel());

        return to_route('admin.pages.index')->with('status', 'পেজ তৈরি হয়েছে।');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(SavePageRequest $request, Page $page): RedirectResponse
    {
        $page->update($request->attributesForModel());

        return back()->with('status', 'পেজ আপডেট হয়েছে।');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return to_route('admin.pages.index')->with('status', 'পেজটি মুছে ফেলা হয়েছে।');
    }
}
