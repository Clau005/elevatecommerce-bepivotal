<?php

namespace ElevateCommerce\Purchasable\Http\Controllers\Admin;

use ElevateCommerce\Purchasable\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentSettingsController extends Controller
{
    /**
     * Display payment gateway settings
     */
    public function index()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();

        return view('purchasable::admin.settings.payments', [
            'gateways' => $gateways,
        ]);
    }

    /**
     * Update payment gateway settings
     */
    public function update(Request $request, PaymentGateway $gateway)
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'test_mode' => 'required|boolean',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $gateway->update($validated);

        return redirect()
            ->route('admin.settings.payments')
            ->with('success', "{$gateway->name} settings updated successfully!");
    }

    /**
     * Check if gateway credentials are configured
     */
    public function checkCredentials(PaymentGateway $gateway)
    {
        $isConfigured = $gateway->isConfigured();
        $credentials = [];

        if ($gateway->gateway === 'stripe') {
            $credentials = [
                'test_pk' => !empty(config('stripe.stripe_test_pk')),
                'test_sk' => !empty(config('stripe.stripe_test_sk')),
                'live_pk' => !empty(config('stripe.stripe_live_pk')),
                'live_sk' => !empty(config('stripe.stripe_live_sk')),
            ];
        } elseif ($gateway->gateway === 'paypal') {
            $mode = $gateway->test_mode ? 'sandbox' : 'live';
            $credentials = [
                'client_id' => !empty(config("paypal.{$mode}.client_id")),
                'client_secret' => !empty(config("paypal.{$mode}.client_secret")),
            ];
        }

        return response()->json([
            'configured' => $isConfigured,
            'credentials' => $credentials,
        ]);
    }
}
