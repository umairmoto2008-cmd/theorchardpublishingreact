<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('pos.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('pos.index'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password provided.',
        ])->onlyInput('email');
    }

    public function quickLogin($role)
    {
        $user = User::where('role', $role)->where('is_active', true)->first();
        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();
            return redirect()->route('pos.index')->with('success', 'Logged in as ' . $user->name . ' (' . ucfirst($user->role) . ')');
        }
        return redirect()->route('login')->with('error', 'User not found.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
