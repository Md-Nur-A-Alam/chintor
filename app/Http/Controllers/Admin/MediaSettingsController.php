<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateMediaSettingsRequest;
use App\Models\Media;
use App\Services\Media\MediaStorage;
use App\Services\Media\MediaStorageException;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MediaSettingsController extends Controller
{
    public function edit(SiteSettings $settings, MediaStorage $storage): View
    {
        return view('admin.settings.media', [
            'media' => $settings->media(),
            'savedSecrets' => $this->maskedSecrets($settings->mediaSecrets()),
            'activeProvider' => $storage->activeProvider(),
            'configured' => collect([Media::ProviderR2, Media::ProviderImgbb, Media::ProviderLocal])
                ->mapWithKeys(fn (string $provider) => [$provider => $storage->isConfigured($provider)]),
        ]);
    }

    public function update(UpdateMediaSettingsRequest $request, SiteSettings $settings): RedirectResponse
    {
        $validated = $request->validated();

        $settings->updateMedia(
            values: Arr::only($validated, array_keys(SiteSettings::MEDIA_DEFAULTS)),
            secrets: Arr::only($validated, array_keys(SiteSettings::MEDIA_SECRET_DEFAULTS)),
            secretsToClear: $validated['clear'] ?? [],
            updatedBy: $request->user()->id,
        );

        return back()->with('status', 'স্টোরেজ সেটিংস সেভ হয়েছে।');
    }

    public function test(Request $request, MediaStorage $storage): RedirectResponse
    {
        abort_unless($request->user()->can('settings.manage'), 403);

        $provider = $request->validate([
            'provider' => ['required', Rule::in([Media::ProviderR2, Media::ProviderImgbb, Media::ProviderLocal])],
        ])['provider'];

        try {
            $storage->store($provider)->checkConnection();
        } catch (MediaStorageException $exception) {
            return back()->withErrors(['connection' => $exception->getMessage()]);
        }

        return back()->with('status', "“{$provider}” সংযোগ সফল হয়েছে।");
    }

    /**
     * Show only the last four characters of each saved credential.
     *
     * @param  array<string, ?string>  $secrets
     * @return array<string, ?string>
     */
    private function maskedSecrets(array $secrets): array
    {
        return array_map(
            fn (?string $secret): ?string => filled($secret) ? '••••'.substr($secret, -4) : null,
            $secrets,
        );
    }
}
