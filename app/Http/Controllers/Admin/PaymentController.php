<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

/**
 * The verification queue of the semi-automated payment system: match each
 * claim with the bKash/Nagad/bank statement, then verify or reject it.
 */
class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString() ?: PaymentStatus::Pending->value;

        $payments = Payment::query()
            ->with(['payable', 'verifier'])
            ->where('status', $status)
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.payments.index', compact('payments', 'status'));
    }

    public function verify(Request $request, Payment $payment, PaymentVerification $verification): RedirectResponse
    {
        try {
            $verification->verify($payment, $request->user());
        } catch (InvalidArgumentException) {
            return back()->withErrors(['general' => 'এই পেমেন্ট আগেই যাচাই বা বাতিল করা হয়েছে।']);
        }

        return back()->with('status', "পেমেন্ট {$payment->reference} যাচাই হয়েছে।");
    }

    public function reject(Request $request, Payment $payment, PaymentVerification $verification): RedirectResponse
    {
        $reason = $request->validate(['rejection_reason' => ['required', 'string', 'max:255']])['rejection_reason'];

        try {
            $verification->reject($payment, $request->user(), $reason);
        } catch (InvalidArgumentException) {
            return back()->withErrors(['general' => 'এই পেমেন্ট আগেই যাচাই বা বাতিল করা হয়েছে।']);
        }

        return back()->with('status', "পেমেন্ট {$payment->reference} বাতিল করা হয়েছে।");
    }
}
