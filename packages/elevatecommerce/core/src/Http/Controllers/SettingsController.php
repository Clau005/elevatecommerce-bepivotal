<?php

namespace ElevateCommerce\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    /**
     * Display settings index page
     */
    public function index()
    {
        return view('core::admin.settings.index');
    }

    /**
     * Display general settings page
     */
    public function general()
    {
        return view('core::admin.settings.general');
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_email' => 'required|email|max:255',
            'store_phone' => 'nullable|string|max:50',
            'store_description' => 'nullable|string|max:1000',
            'timezone' => 'required|string|in:' . implode(',', timezone_identifiers_list()),
            'date_format' => 'nullable|string',
            'time_format' => 'nullable|string',
            'week_start' => 'nullable|integer|in:0,1',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
        ]);

        // TODO: Save settings to database or config file
        // For now, we'll just flash a success message

        return redirect()
            ->route('admin.settings.general')
            ->with('success', 'General settings updated successfully!');
    }
}
