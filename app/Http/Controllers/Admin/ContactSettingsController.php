<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContactSettingsRequest;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactSettingsController extends Controller
{
    public function edit(SiteSettings $settings): View
    {
        return view('admin.settings.contact', [
            'contact' => $settings->contact(),
            'legal' => $settings->legal(),
        ]);
    }

    public function update(UpdateContactSettingsRequest $request, SiteSettings $settings): RedirectResponse
    {
        $settings->updateContact($request->validated(), $request->user()->id);

        return back()->with('status', 'যোগাযোগের তথ্য সেভ হয়েছে। ওয়েবসাইটে সাথে সাথে দেখাবে।');
    }
}
