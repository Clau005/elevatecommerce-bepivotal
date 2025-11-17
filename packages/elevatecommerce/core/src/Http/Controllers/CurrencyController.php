<?php

namespace ElevateCommerce\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\Core\Models\Currency;

class CurrencyController extends Controller
{
    /**
     * Display currencies index
     */
    public function index()
    {
        $currencies = Currency::orderBy('is_default', 'desc')
            ->orderBy('code')
            ->get();

        return view('core::admin.settings.currencies.index', compact('currencies'));
    }

    /**
     * Show create currency form
     */
    public function create()
    {
        return view('core::admin.settings.currencies.create');
    }

    /**
     * Store a new currency
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:4',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'is_default' => 'boolean',
            'is_enabled' => 'boolean',
        ]);

        $currency = Currency::create($validated);

        if ($request->boolean('is_default')) {
            $currency->setAsDefault();
        }

        return redirect()
            ->route('admin.settings.currencies.index')
            ->with('success', 'Currency created successfully!');
    }

    /**
     * Show edit currency form
     */
    public function edit(Currency $currency)
    {
        return view('core::admin.settings.currencies.edit', compact('currency'));
    }

    /**
     * Update a currency
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:4',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'is_default' => 'boolean',
            'is_enabled' => 'boolean',
        ]);

        $currency->update($validated);

        if ($request->boolean('is_default')) {
            $currency->setAsDefault();
        }

        return redirect()
            ->route('admin.settings.currencies.index')
            ->with('success', 'Currency updated successfully!');
    }

    /**
     * Set currency as default
     */
    public function setDefault(Currency $currency)
    {
        $currency->setAsDefault();

        return redirect()
            ->route('admin.settings.currencies.index')
            ->with('success', 'Default currency updated!');
    }

    /**
     * Toggle currency enabled status
     */
    public function toggleEnabled(Currency $currency)
    {
        $currency->update(['is_enabled' => !$currency->is_enabled]);

        return redirect()
            ->route('admin.settings.currencies.index')
            ->with('success', 'Currency status updated!');
    }

    /**
     * Delete a currency
     */
    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return redirect()
                ->route('admin.settings.currencies.index')
                ->with('error', 'Cannot delete the default currency!');
        }

        $currency->delete();

        return redirect()
            ->route('admin.settings.currencies.index')
            ->with('success', 'Currency deleted successfully!');
    }
}
