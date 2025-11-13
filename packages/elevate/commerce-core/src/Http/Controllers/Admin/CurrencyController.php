<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    /**
     * Display a listing of currencies.
     */
    public function index(Request $request): View
    {
        $query = Currency::query();
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('enabled')) {
            $query->where('is_enabled', true);
        }
        
        $currencies = $query->orderBy('is_default', 'desc')
                           ->orderBy('code')
                           ->paginate(15);
        
        // Prepare data array
        $tableData = $currencies->map(function($currency) {
            return [
                'id' => $currency->id,
                'code' => $currency->code,
                'name' => $currency->name,
                'symbol' => $currency->symbol,
                'exchange_rate' => $currency->exchange_rate,
                'decimal_places' => $currency->decimal_places ?? 2,
                'enabled' => $currency->is_enabled,
                'default' => $currency->is_default,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'code' => [
                'label' => 'Code',
                'sortable' => true,
            ],
            'name' => [
                'label' => 'Name',
                'sortable' => true,
            ],
            'symbol' => [
                'label' => 'Symbol',
                'sortable' => false,
            ],
            'exchange_rate' => [
                'label' => 'Exchange Rate',
                'sortable' => false,
            ],
            'enabled' => [
                'label' => 'Status',
                'sortable' => false,
                'render' => function($row) {
                    return $row['enabled']
                        ? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Enabled</span>'
                        : '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Disabled</span>';
                }
            ],
            'default' => [
                'label' => 'Default',
                'sortable' => false,
                'render' => function($row) {
                    return $row['default']
                        ? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Default</span>'
                        : '';
                }
            ],
            'actions' => [
                'label' => 'Actions',
                'sortable' => false,
                'render' => function($row) {
                    $editUrl = route('admin.settings.currencies.edit', $row['id']);
                    $deleteUrl = route('admin.settings.currencies.destroy', $row['id']);
                    
                    $html = '<div class="flex items-center gap-2">';
                    $html .= '<a href="'.$editUrl.'" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">Edit</a>';
                    
                    if (!$row['default']) {
                        $html .= '<button onclick="confirmDelete(\''.$row['id'].'\')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">Delete</button>';
                    }
                    
                    $html .= '</div>';
                    return $html;
                }
            ],
        ];
        
        return view('commerce::admin.settings.currencies.index', [
            'currencies' => $currencies,
            'tableData' => $tableData,
            'columns' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new currency.
     */
    public function create(): View
    {
        return view('commerce::admin.settings.currencies.form', [
            'currency' => new Currency(),
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created currency.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'is_enabled' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        Currency::create($validated);

        return redirect()->route('admin.settings.currencies.index')
                        ->with('success', 'Currency created successfully.');
    }

    /**
     * Show the form for editing a currency.
     */
    public function edit(Currency $currency): View
    {
        return view('commerce::admin.settings.currencies.form', [
            'currency' => $currency,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified currency.
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'is_enabled' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Currency::where('id', '!=', $currency->id)
                   ->where('is_default', true)
                   ->update(['is_default' => false]);
        }

        $currency->update($validated);

        return redirect()->route('admin.settings.currencies.index')
                        ->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified currency.
     */
    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->with('error', 'Cannot delete the default currency.');
        }

        $currency->delete();

        return redirect()->route('admin.settings.currencies.index')
                        ->with('success', 'Currency deleted successfully.');
    }
}
