<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StoreOrderRequest;
use App\Models\Book;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\PaymentAccount;
use App\Services\OrderPlacement;
use App\Support\Localization;
use App\Support\PhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(Book $book): View
    {
        abort_unless($book->isPublished() && $book->isInStock(), 404);

        return view('site.orders.create', [
            'book' => $book->load('authors'),
            'deliveryZones' => DeliveryZone::query()->active()->get(),
            'paymentAccounts' => PaymentAccount::query()->active()->get(),
        ]);
    }

    public function store(StoreOrderRequest $request, Book $book, OrderPlacement $orders): RedirectResponse
    {
        abort_unless($book->isPublished() && $book->isInStock(), 404);

        $order = $orders->place(
            $book,
            $request->integer('quantity'),
            $request->safe()->except(['quantity', 'website']),
            $request,
        );

        return redirect(Localization::route('orders.success', ['order' => $order->ulid]));
    }

    public function success(Order $order): View
    {
        $order->load(['items', 'payments']);

        return view('site.orders.success', compact('order'));
    }

    /**
     * Order tracking without login: the order number plus the phone it was placed with.
     */
    public function track(Request $request): View
    {
        $reference = $request->string('reference')->trim()->upper()->toString();
        $phone = PhoneNumber::normalize($request->string('phone')->toString());
        $order = null;

        if ($reference !== '' && $phone !== null) {
            $order = Order::query()
                ->with(['items', 'payments'])
                ->where('reference', $reference)
                ->where('phone', $phone)
                ->first();
        }

        return view('site.orders.track', [
            'order' => $order,
            'searched' => $reference !== '',
            'reference' => $reference,
        ]);
    }
}
