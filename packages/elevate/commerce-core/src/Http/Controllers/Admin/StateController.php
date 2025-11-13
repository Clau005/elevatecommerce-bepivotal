<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Country;
use Elevate\CommerceCore\Models\State;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StateController extends Controller
{
    /**
     * Display a listing of countries and states.
     */
    public function index(Request $request): View
    {
        $query = Country::withCount('states');
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iso2', 'like', "%{$search}%")
                  ->orWhere('iso3', 'like', "%{$search}%");
            });
        }
        
        $countries = $query->orderBy('name')
                          ->paginate(15);
        
        // Prepare data array
        $tableData = $countries->map(function($country) {
            return [
                'id' => $country->id,
                'name' => $country->name,
                'iso2' => $country->iso2,
                'iso3' => $country->iso3,
                'states_count' => $country->states_count,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'name' => [
                'label' => 'Country',
                'sortable' => true,
            ],
            'iso2' => [
                'label' => 'ISO2',
                'sortable' => false,
            ],
            'iso3' => [
                'label' => 'ISO3',
                'sortable' => false,
            ],
            'states_count' => [
                'label' => 'States/Regions',
                'sortable' => false,
            ],
            'actions' => [
                'label' => 'Actions',
                'sortable' => false,
                'render' => function($row) {
                    $viewUrl = route('admin.settings.states.country', $row['id']);
                    
                    return '<a href="'.$viewUrl.'" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">View States</a>';
                }
            ],
        ];
        
        return view('commerce::admin.settings.states.index', [
            'countries' => $countries,
            'tableData' => $tableData,
            'columns' => $columns,
        ]);
    }

    /**
     * Show states for a specific country.
     */
    public function showCountry(Country $country): View
    {
        $states = $country->states()->orderBy('name')->paginate(50);
        
        // Prepare data array
        $tableData = $states->map(function($state) {
            return [
                'id' => $state->id,
                'name' => $state->name,
                'code' => $state->code,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'name' => [
                'label' => 'State/Region',
                'sortable' => true,
            ],
            'code' => [
                'label' => 'Code',
                'sortable' => false,
            ],
            'actions' => [
                'label' => 'Actions',
                'sortable' => false,
                'render' => function($row) use ($country) {
                    $editUrl = route('admin.settings.states.edit', [$country->id, $row['id']]);
                    
                    $html = '<div class="flex items-center gap-2">';
                    $html .= '<a href="'.$editUrl.'" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">Edit</a>';
                    $html .= '<button onclick="confirmDelete(\''.$row['id'].'\')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">Delete</button>';
                    $html .= '</div>';
                    return $html;
                }
            ],
        ];
        
        return view('commerce::admin.settings.states.country', [
            'country' => $country,
            'states' => $states,
            'tableData' => $tableData,
            'columns' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new state.
     */
    public function create(Country $country): View
    {
        return view('commerce::admin.settings.states.form', [
            'country' => $country,
            'state' => new State(),
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created state.
     */
    public function store(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
        ]);

        $country->states()->create($validated);

        return redirect()->route('admin.settings.states.country', $country)
                        ->with('success', 'State/Region created successfully.');
    }

    /**
     * Show the form for editing a state.
     */
    public function edit(Country $country, State $state): View
    {
        return view('commerce::admin.settings.states.form', [
            'country' => $country,
            'state' => $state,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified state.
     */
    public function update(Request $request, Country $country, State $state)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
        ]);

        $state->update($validated);

        return redirect()->route('admin.settings.states.country', $country)
                        ->with('success', 'State/Region updated successfully.');
    }

    /**
     * Remove the specified state.
     */
    public function destroy(Country $country, State $state)
    {
        $state->delete();

        return redirect()->route('admin.settings.states.country', $country)
                        ->with('success', 'State/Region deleted successfully.');
    }
}
