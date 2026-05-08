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
        return view('auth.sign-in');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role?->name === 'admin') {

            return redirect('/admin');
        }

        if ($user->role?->name === 'resepsionis') {

            return redirect('/resepsionis');
        }

        if ($user->role?->name === 'customer') {

            return redirect('/customer');
        }

        return redirect('/');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
