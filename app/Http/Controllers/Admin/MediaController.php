<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Models\Media;
use App\Services\Media\MediaStorage;
use App\Services\Media\MediaStorageException;
use App\Support\YouTube;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request, MediaStorage $storage): View
    {
        $youtubeUrl = $request->string('youtube')->trim()->toString();

        return view('admin.media.index', [
            'mediaItems' => Media::query()->with('uploader')->latest()->paginate(24),
            'activeProvider' => $storage->activeProvider(),
            'youtubeUrl' => $youtubeUrl,
            'youtubeIsValid' => YouTube::isValid($youtubeUrl),
        ]);
    }

    public function store(StoreMediaRequest $request, MediaStorage $storage): RedirectResponse
    {
        try {
            $media = $storage->upload($request->file('image'), 'images', $request->user()->id);
        } catch (MediaStorageException $exception) {
            return back()->withErrors(['image' => $exception->getMessage()]);
        }

        if ($storage->lastFallbackReason !== null) {
            return back()
                ->with('status', 'ছবিটি এই সার্ভারে সেভ হয়েছে (ক্লাউড স্টোরেজ ব্যর্থ হয়েছে)।')
                ->withErrors(['general' => $storage->lastFallbackReason]);
        }

        return back()->with('status', "ছবি আপলোড হয়েছে ({$media->provider})।");
    }

    public function destroy(Request $request, Media $media, MediaStorage $storage): RedirectResponse
    {
        abort_unless($request->user()->can('media.manage'), 403);

        try {
            $storage->delete($media);
        } catch (MediaStorageException $exception) {
            return back()->withErrors(['image' => $exception->getMessage()]);
        }

        return back()->with('status', 'ছবিটি মুছে ফেলা হয়েছে।');
    }
}
