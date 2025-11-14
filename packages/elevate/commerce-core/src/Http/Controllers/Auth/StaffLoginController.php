<?php

namespace Elevate\CommerceCore\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StaffLoginController extends Controller
{
    /**
     * Show the login form
     */
    public function create()
    {
        return view('commerce::auth.login');
    }

    /**
     * Handle login request
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('staff')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->intended('/admin');
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Log out the user
     */
    public function destroy(Request $request)
    {
        Auth::guard('staff')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
