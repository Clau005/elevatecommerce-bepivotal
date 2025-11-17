<?php

namespace Elevate\CommerceCore\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the user's addresses.
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return view('commerce::storefront.account.addresses', [
            'addresses' => $addresses,
        ]);
    }

    /**
     * Store a newly created address.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:billing,shipping,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:20',
            'country' => 'required|string|size:2',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = Auth::user()->addresses()->create($validated);

        return redirect()
            ->route('storefront.addresses')
            ->with('success', 'Address added successfully.');
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, UserAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:billing,shipping,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:20',
            'country' => 'required|string|size:2',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()
            ->route('storefront.addresses')
            ->with('success', 'Address updated successfully.');
    }

    /**
     * Remove the specified address.
     */
    public function destroy(UserAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // If this was the default address, make another one default
        if ($address->is_default) {
            $nextAddress = Auth::user()->addresses()->where('id', '!=', $address->id)->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        $address->delete();

        return redirect()
            ->route('storefront.addresses')
            ->with('success', 'Address deleted successfully.');
    }

    /**
     * Set the specified address as default.
     */
    public function setDefault(UserAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // Unset all other defaults
        Auth::user()->addresses()->update(['is_default' => false]);

        // Set this one as default
        $address->update(['is_default' => true]);

        return redirect()
            ->route('storefront.addresses')
            ->with('success', 'Default address updated successfully.');
    }
}
