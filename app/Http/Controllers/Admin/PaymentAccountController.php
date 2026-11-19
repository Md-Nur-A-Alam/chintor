<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SavePaymentAccountRequest;
use App\Models\PaymentAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * The bKash / Nagad / Upay / bank accounts shown at checkout.
 */
class PaymentAccountController extends Controller
{
    public function index(): View
    {
        return view('admin.payment-accounts.index', ['accounts' => PaymentAccount::orderBy('sort_order')->get()]);
    }

    public function create(): View
    {
        return view('admin.payment-accounts.form', ['account' => new PaymentAccount(['method' => PaymentMethod::Bkash, 'account_type' => 'personal', 'is_active' => true])]);
    }

    public function store(SavePaymentAccountRequest $request): RedirectResponse
    {
        PaymentAccount::create($request->attributesForModel());

        return to_route('admin.payment-accounts.index')->with('status', 'পেমেন্ট অ্যাকাউন্ট যোগ হয়েছে।');
    }

    public function edit(PaymentAccount $paymentAccount): View
    {
        return view('admin.payment-accounts.form', ['account' => $paymentAccount]);
    }

    public function update(SavePaymentAccountRequest $request, PaymentAccount $paymentAccount): RedirectResponse
    {
        $paymentAccount->update($request->attributesForModel());

        return to_route('admin.payment-accounts.index')->with('status', 'পেমেন্ট অ্যাকাউন্ট আপডেট হয়েছে।');
    }

    public function destroy(PaymentAccount $paymentAccount): RedirectResponse
    {
        $paymentAccount->delete();

        return to_route('admin.payment-accounts.index')->with('status', 'পেমেন্ট অ্যাকাউন্ট মুছে ফেলা হয়েছে।');
    }
}
