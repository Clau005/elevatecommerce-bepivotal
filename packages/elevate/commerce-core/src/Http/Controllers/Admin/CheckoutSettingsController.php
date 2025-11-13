<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\CheckoutRule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutSettingsController extends Controller
{
    /**
     * Display the checkout settings page.
     */
    public function index(): View
    {
        // Get recent checkout rules for embedded table
        $recentRules = CheckoutRule::orderBy('priority', 'desc')
                                   ->orderBy('created_at', 'desc')
                                   ->limit(5)
                                   ->get();
        
        return view('commerce::admin.settings.checkout.index', [
            'recentRules' => $recentRules,
        ]);
    }

    /**
     * Update checkout settings.
     */
    public function update(Request $request)
    {
        // TODO: Implement checkout settings update
        
        return redirect()->route('admin.settings.checkout.index')
            ->with('success', 'Checkout settings updated successfully');
    }
}
