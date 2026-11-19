<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Book;
use App\Models\DeliveryZone;
use App\Models\Guardian;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Places a book order (no login needed). The customer pays by mobile
 * banking first and submits the transaction ID; staff verify it, then
 * the books are couriered (requirements §5.3).
 */
class OrderPlacement
{
    /**
     * @param  array{customer_name: string, phone: string, alt_phone?: ?string, shipping_address: string, district: string, upazila?: ?string, notes?: ?string, payment_method: string, sender_number?: ?string, transaction_id: string}  $details
     */
    public function place(Book $book, int $quantity, array $details, Request $request): Order
    {
        return DB::transaction(function () use ($book, $quantity, $details, $request): Order {
            $zone = DeliveryZone::forDistrict($details['district']);
            $unitPrice = $book->payablePrice();
            $subtotal = $unitPrice * $quantity;
            $deliveryCharge = $zone?->charge ?? 0;

            $guardian = Guardian::updateOrCreate(['phone' => $details['phone']], ['name' => $details['customer_name']]);

            $order = Order::create([
                'guardian_id' => $guardian->id,
                'customer_name' => $details['customer_name'],
                'phone' => $details['phone'],
                'alt_phone' => $details['alt_phone'] ?? null,
                'shipping_address' => $details['shipping_address'],
                'district' => $details['district'],
                'upazila' => $details['upazila'] ?? null,
                'delivery_zone_id' => $zone?->id,
                'status' => OrderStatus::PendingVerification,
                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'total' => $subtotal + $deliveryCharge,
                'notes' => $details['notes'] ?? null,
                'locale' => app()->getLocale(),
                'ip_address' => $request->ip(),
            ]);

            $order->items()->create([
                'book_id' => $book->id,
                'title_snapshot' => $book->translate('title', 'bn'),
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'line_total' => $subtotal,
            ]);

            $order->payments()->create([
                'method' => PaymentMethod::from($details['payment_method']),
                'amount' => $order->total,
                'sender_number' => $details['sender_number'] ?? null,
                'transaction_id' => $details['transaction_id'],
                'status' => PaymentStatus::Pending,
                'submitted_by' => 'customer',
            ]);

            return $order;
        });
    }
}
