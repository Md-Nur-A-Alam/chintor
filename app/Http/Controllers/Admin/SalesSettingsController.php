<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Refund policy (requirements §3.3.1) and delivery zones on one page.
 */
class SalesSettingsController extends Controller
{
    public function edit(SiteSettings $settings): View
    {
        return view('admin.settings.sales', [
            'refundPolicy' => $settings->refundPolicy(),
            'zones' => DeliveryZone::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * "classes" mode needs attendance from the LMS, so it is not accepted yet.
     */
    public function update(Request $request, SiteSettings $settings): RedirectResponse
    {
        $policy = $request->validate([
            'mode' => ['required', Rule::in(['days'])],
            'window' => ['required', 'integer', 'min:0', 'max:60'],
            'percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $settings->updateRefundPolicy([
            'mode' => $policy['mode'],
            'window' => (int) $policy['window'],
            'percent' => (int) $policy['percent'],
        ], $request->user()->id);

        return back()->with('status', 'রিফান্ড নীতি সেভ হয়েছে। নতুন ভর্তিতে এটি প্রযোজ্য হবে; আগের ভর্তিগুলোর নীতি অপরিবর্তিত থাকবে।');
    }
}
