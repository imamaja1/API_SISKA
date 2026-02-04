<?php

namespace App\Http\Controllers\ApiPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('api_user_login');
    }

    public function login(Request $request)
    {
        return dd($request->all());
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('api_users_web')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/api-panel');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('api_users_web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('api_user.login');
    }
}
