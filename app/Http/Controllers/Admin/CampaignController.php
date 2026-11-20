<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Website → Campaigns: offers, Facebook/YouTube live sessions and events
 * shown in the homepage hero, the announcement bar and the campaigns section.
 */
class CampaignController extends Controller
{
    use HandlesImageInput;

    public function index(): View
    {
        return view('admin.campaigns.index', [
            'campaigns' => Campaign::query()->orderByDesc('is_active')->orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.campaigns.form', ['campaign' => new Campaign(['is_active' => true, 'show_in_hero' => true])]);
    }

    public function store(SaveCampaignRequest $request): RedirectResponse
    {
        Campaign::create([...$request->attributesForModel(), 'image_url' => $this->resolveImageUrl($request, 'image')]);

        return to_route('admin.campaigns.index')->with('status', 'ক্যাম্পেইন যোগ হয়েছে।');
    }

    public function edit(Campaign $campaign): View
    {
        return view('admin.campaigns.form', ['campaign' => $campaign]);
    }

    public function update(SaveCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update([...$request->attributesForModel(), 'image_url' => $this->resolveImageUrl($request, 'image', $campaign->image_url)]);

        return to_route('admin.campaigns.index')->with('status', 'ক্যাম্পেইন আপডেট হয়েছে।');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return to_route('admin.campaigns.index')->with('status', 'ক্যাম্পেইন মুছে ফেলা হয়েছে।');
    }
}
