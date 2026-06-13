<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;


class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        $referer = request()->headers->get('referer');
        if ($referer && !str_contains($referer, '/login') && !str_contains($referer, '/register') && !str_contains($referer, '/logout') && !str_contains($referer, '/password')) {
            if (str_starts_with($referer, request()->getSchemeAndHttpHost())) {
                session(['url.intended' => $referer]);
            }
        }

        return view('auth.sign-in');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
