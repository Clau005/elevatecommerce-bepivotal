<?php

namespace Elevate\Shipping\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Elevate\Shipping\Models\ShippingCarrier;

class ShippingCarrierController extends Controller
{
    public function index()
    {
        $carriers = ShippingCarrier::orderBy('sort_order')->get();
        
        return view('shipping::admin.carriers.index', compact('carriers'));
    }

    public function update(Request $request, ShippingCarrier $carrier)
    {
        $validated = $request->validate([
            'is_enabled' => 'boolean',
            'test_mode' => 'boolean',
            'sort_order' => 'integer',
            'credentials' => 'array',
            'test_credentials' => 'array',
            'settings' => 'array',
        ]);

        // Ensure test_mode is set (checkbox won't send if unchecked)
        $validated['test_mode'] = $request->has('test_mode');

        $carrier->update($validated);

        return back()->with('success', 'Shipping carrier updated successfully');
    }

    public function toggle(ShippingCarrier $carrier)
    {
        $carrier->update(['is_enabled' => !$carrier->is_enabled]);

        return back()->with('success', 'Shipping carrier ' . ($carrier->is_enabled ? 'enabled' : 'disabled'));
    }
}
