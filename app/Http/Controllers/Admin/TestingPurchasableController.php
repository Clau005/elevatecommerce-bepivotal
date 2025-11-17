<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestingPurchasable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestingPurchasableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = TestingPurchasable::latest()->paginate(20);
        
        return view('admin.testing-purchasables.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.testing-purchasables.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:testing_purchasables,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'stock_quantity' => 'required|integer|min:0',
            'track_inventory' => 'boolean',
            'image_url' => 'nullable|url',
        ]);

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Convert prices to cents
        $validated['price'] = (int) ($validated['price'] * 100);
        if (isset($validated['compare_at_price'])) {
            $validated['compare_at_price'] = (int) ($validated['compare_at_price'] * 100);
        }

        TestingPurchasable::create($validated);

        return redirect()->route('admin.testing-purchasables.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TestingPurchasable $testingPurchasable)
    {
        return view('admin.testing-purchasables.show', compact('testingPurchasable'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TestingPurchasable $testingPurchasable)
    {
        return view('admin.testing-purchasables.edit', compact('testingPurchasable'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TestingPurchasable $testingPurchasable)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:testing_purchasables,sku,' . $testingPurchasable->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'stock_quantity' => 'required|integer|min:0',
            'track_inventory' => 'boolean',
            'image_url' => 'nullable|url',
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $testingPurchasable->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Convert prices to cents
        $validated['price'] = (int) ($validated['price'] * 100);
        if (isset($validated['compare_at_price'])) {
            $validated['compare_at_price'] = (int) ($validated['compare_at_price'] * 100);
        }

        $testingPurchasable->update($validated);

        return redirect()->route('admin.testing-purchasables.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TestingPurchasable $testingPurchasable)
    {
        $testingPurchasable->delete();

        return redirect()->route('admin.testing-purchasables.index')
            ->with('success', 'Product deleted successfully.');
    }
}
