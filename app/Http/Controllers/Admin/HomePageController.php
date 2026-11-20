<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomePageRequest;
use App\Services\HomePageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Website → Homepage: hero, impact numbers and which sections show in what order.
 */
class HomePageController extends Controller
{
    use HandlesImageInput;

    public function edit(HomePageContent $homePage): View
    {
        $content = $homePage->all();

        return view('admin.homepage.edit', [
            'hero' => $content['hero'],
            'buttons' => array_pad($content['hero']['buttons'], HomePageContent::MAX_BUTTONS, []),
            'stats' => array_pad($content['stats'], HomePageContent::MAX_STATS, []),
            'sections' => $content['sections'],
        ]);
    }

    public function update(UpdateHomePageRequest $request, HomePageContent $homePage): RedirectResponse
    {
        $hero = $homePage->all()['hero'];
        $bannerUrl = $this->resolveImageUrl($request, 'banner', $hero['banner_url']);
        $bannerMobileUrl = $this->resolveImageUrl($request, 'banner_mobile', $hero['banner_mobile_url']);

        if ($request->input('hero.layout') === 'banner' && $bannerUrl === null) {
            throw ValidationException::withMessages(['banner_file' => 'ব্যানার লেআউটের জন্য ডেস্কটপ ব্যানার ছবি দিন।']);
        }

        $homePage->update($request->content($bannerUrl, $bannerMobileUrl), $request->user()->id);

        return back()->with('status', 'হোমপেজ সেভ হয়েছে। ওয়েবসাইটে সাথে সাথে দেখাবে।');
    }
}
