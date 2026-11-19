<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveDeliveryZoneRequest;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/**
 * Delivery charge zones, managed on the sales settings page.
 */
class DeliveryZoneController extends Controller
{
    public function store(SaveDeliveryZoneRequest $request): RedirectResponse
    {
        $this->save(new DeliveryZone, $request);

        return redirect(route('admin.settings.sales.edit').'#zones')->with('status', 'ডেলিভারি জোন যোগ হয়েছে।');
    }

    public function update(SaveDeliveryZoneRequest $request, DeliveryZone $deliveryZone): RedirectResponse
    {
        $this->save($deliveryZone, $request);

        return redirect(route('admin.settings.sales.edit').'#zones')->with('status', 'ডেলিভারি জোন আপডেট হয়েছে।');
    }

    public function destroy(DeliveryZone $deliveryZone): RedirectResponse
    {
        abort_unless(request()->user()->can('settings.manage'), 403);

        $deliveryZone->delete();

        return redirect(route('admin.settings.sales.edit').'#zones')->with('status', 'ডেলিভারি জোন মুছে ফেলা হয়েছে।');
    }

    /**
     * Only one zone can be the default (used for districts no zone lists).
     */
    private function save(DeliveryZone $zone, SaveDeliveryZoneRequest $request): void
    {
        DB::transaction(function () use ($zone, $request): void {
            $zone->fill($request->attributesForModel())->save();

            if ($zone->is_default) {
                DeliveryZone::whereKeyNot($zone->id)->update(['is_default' => false]);
            }
        });
    }
}
