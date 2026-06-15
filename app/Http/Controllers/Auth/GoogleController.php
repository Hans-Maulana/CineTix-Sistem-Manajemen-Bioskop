<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Support\RedirectAfterAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
   public function redirectToGoogle()
    {
        $redirect = request('redirect');
        RedirectAfterAuth::remember($redirect);

        $driver = Socialite::driver('google')->with(['prompt' => 'select_account'])->stateless();
        
        if ($redirect) {
            $driver->with(['state' => base64_encode(json_encode(['redirect' => $redirect]))]);
        }

        return $driver->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'google' => 'Gagal login dengan Google, silakan coba lagi.',
            ]);
        }


        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            $user = User::create([
                'name'      => $googleUser->name,
                'email'     => $googleUser->email,
                'google_id' => $googleUser->id,
                'password'  => Hash::make(Str::random(16)),
                'role_id'   => Role::where('name', 'customer')->first()->id,
            ]);
        } else {

            if (empty($user->google_id)) {
                $user->update(['google_id' => $googleUser->id]);
            }
        }

        Auth::login($user);

        $state = request('state');
        $redirect = null;
        if ($state) {
            $decoded = json_decode(base64_decode($state), true);
            if (is_array($decoded) && isset($decoded['redirect'])) {
                $redirect = $decoded['redirect'];
            }
        }

        return $this->sendToDashboard($user, $redirect);
    }

    /**
     * Mengarahkan user ke dashboard yang tepat
     */
    protected function sendToDashboard($user, $redirect = null)
    {

        $user->load('role');

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $intended = session()->pull('url.intended');
        if (!$intended && $redirect) {
            $intended = RedirectAfterAuth::normalize($redirect);
        }

        return redirect()->intended($intended ?: RedirectAfterAuth::fallback());
    }
}
