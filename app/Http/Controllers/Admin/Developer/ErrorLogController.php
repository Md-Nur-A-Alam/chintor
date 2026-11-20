<?php

namespace App\Http\Controllers\Admin\Developer;

use App\Http\Controllers\Controller;
use App\Support\LogReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Recent application errors from storage/logs.
 */
class ErrorLogController extends Controller
{
    private const LEVELS = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

    public function index(Request $request): View
    {
        $level = $request->validate(['level' => ['nullable', Rule::in(self::LEVELS)]])['level'] ?? null;

        return view('admin.developer.errors', [
            'entries' => LogReader::latest(100, $level),
            'level' => $level,
            'levels' => self::LEVELS,
        ]);
    }

    public function clear(): RedirectResponse
    {
        LogReader::clear();

        return back()->with('status', 'লগ ফাইল খালি করা হয়েছে।');
    }
}
