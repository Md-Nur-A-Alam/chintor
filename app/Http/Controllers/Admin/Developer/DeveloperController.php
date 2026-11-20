<?php

namespace App\Http\Controllers\Admin\Developer;

use App\Http\Controllers\Controller;
use App\Support\LogReader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Developer hub: system facts at a glance and the developer guide.
 */
class DeveloperController extends Controller
{
    public function index(): View
    {
        return view('admin.developer.index', [
            'system' => [
                'Laravel' => app()->version(),
                'PHP' => PHP_VERSION,
                'ডেটাবেস' => DB::connection()->getDriverName().' · '.DB::connection()->getDatabaseName(),
                'পরিবেশ (APP_ENV)' => app()->environment(),
                'ডিবাগ' => config('app.debug') ? 'চালু' : 'বন্ধ',
                'সময় অঞ্চল' => config('app.timezone'),
                'ক্যাশ / সেশন / কিউ' => config('cache.default').' / '.config('session.driver').' / '.config('queue.default'),
                'SQL write' => config('chintor.developer.allow_write_queries') ? 'অনুমোদিত' : 'বন্ধ',
            ],
            'recentErrorCount' => count(LogReader::latest(500, 'error')),
        ]);
    }

    public function guide(): View
    {
        $markdown = (string) file_get_contents(resource_path('docs/developer-guide.md'));

        return view('admin.developer.guide', [
            'html' => Str::markdown($markdown, ['html_input' => 'escape', 'allow_unsafe_links' => false]),
        ]);
    }
}
