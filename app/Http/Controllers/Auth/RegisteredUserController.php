<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $referer = request()->headers->get('referer');
        if ($referer && !str_contains($referer, '/login') && !str_contains($referer, '/register') && !str_contains($referer, '/logout') && !str_contains($referer, '/password')) {
            if (str_starts_with($referer, request()->getSchemeAndHttpHost())) {
                session(['url.intended' => $referer]);
            }
        }

        return view('auth.sign-up');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,filter', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.email' => 'email tidak valid, gunakan email yang valid',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::firstOrCreate(['name' => 'customer'])->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false))
            ->with('success', 'Selamat datang! Anda mendapat kode promo WELCOME2026 senilai Rp 20.000 untuk pembelian pertama.');
    }
}
