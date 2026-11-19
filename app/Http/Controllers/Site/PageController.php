<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Page $page): View
    {
        abort_unless($page->isPublished(), 404);

        // Markdown written by staff; raw HTML is escaped and unsafe links are dropped.
        $html = Str::markdown((string) $page->translate('body'), [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);

        return view('site.pages.show', compact('page', 'html'));
    }
}
