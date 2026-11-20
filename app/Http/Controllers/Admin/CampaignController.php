<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCampaignRequest;
use App\Models\Campaign;
use App\Support\HeroBanner;
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
        return view('admin.campaigns.form', ['campaign' => new Campaign([
            'is_active' => true,
            'show_in_hero' => true,
            'banner_align' => 'left',
            'banner_overlay' => HeroBanner::DEFAULT_OVERLAY,
        ])]);
    }

    public function store(SaveCampaignRequest $request): RedirectResponse
    {
        Campaign::create([...$request->attributesForModel(), ...$this->images($request)]);

        return to_route('admin.campaigns.index')->with('status', 'ক্যাম্পেইন যোগ হয়েছে।');
    }

    public function edit(Campaign $campaign): View
    {
        return view('admin.campaigns.form', ['campaign' => $campaign]);
    }

    public function update(SaveCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update([...$request->attributesForModel(), ...$this->images($request, $campaign)]);

        return to_route('admin.campaigns.index')->with('status', 'ক্যাম্পেইন আপডেট হয়েছে।');
    }

    /**
     * The square banner, the wide hero banner and its phone version.
     *
     * @return array{image_url: ?string, banner_url: ?string, banner_mobile_url: ?string}
     */
    private function images(SaveCampaignRequest $request, ?Campaign $campaign = null): array
    {
        return [
            'image_url' => $this->resolveImageUrl($request, 'image', $campaign?->image_url),
            'banner_url' => $this->resolveImageUrl($request, 'banner', $campaign?->banner_url),
            'banner_mobile_url' => $this->resolveImageUrl($request, 'banner_mobile', $campaign?->banner_mobile_url),
        ];
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return to_route('admin.campaigns.index')->with('status', 'ক্যাম্পেইন মুছে ফেলা হয়েছে।');
    }
}
