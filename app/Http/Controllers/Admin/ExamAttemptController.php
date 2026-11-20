<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\View\View;

/**
 * Learning → Arena results: who took which exam and how they did. A
 * sitting with a phone number is also a lead (linked here).
 */
class ExamAttemptController extends Controller
{
    public function index(): View
    {
        return view('admin.exam-attempts.index', [
            'attempts' => ExamAttempt::query()->with(['exam', 'lead'])->whereNotNull('submitted_at')->latest('submitted_at')->paginate(40),
        ]);
    }
}
