<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomePageRequest;
use App\Services\HomePageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Website → Homepage: hero, impact numbers and which sections show in what order.
 */
class HomePageController extends Controller
{
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
        $homePage->update($request->content(), $request->user()->id);

        return back()->with('status', 'হোমপেজ সেভ হয়েছে। ওয়েবসাইটে সাথে সাথে দেখাবে।');
    }
}
