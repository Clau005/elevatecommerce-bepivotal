<?php

namespace Elevate\Payments\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Elevate\Payments\Models\PaymentGateway;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();
        
        return view('payments::admin.gateways.index', compact('gateways'));
    }

    public function update(Request $request, PaymentGateway $gateway)
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

        $gateway->update($validated);

        return back()->with('success', 'Payment gateway updated successfully');
    }

    public function toggle(PaymentGateway $gateway)
    {
        $gateway->update(['is_enabled' => !$gateway->is_enabled]);

        return back()->with('success', 'Payment gateway ' . ($gateway->is_enabled ? 'enabled' : 'disabled'));
    }
}
