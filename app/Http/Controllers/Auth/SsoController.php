<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SSOController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('laravelpassport')->stateless()->redirect();
    }

    public function callback()
    {
        try {
            Log::info('[CLIENT] SSO callback HIT');

            // Log::info('HOST', ['host' => config('services.laravelpassport.host')]);
            // Log::info('REDIRECT', ['redirect' => config('services.laravelpassport.redirect')]);

            $ssoUser = Socialite::driver('laravelpassport')->stateless()->user();

            // dd($ssoUser);

            $user = User::firstOrCreate(
                ['email' => $ssoUser->getEmail()],
                [
                    'name' => $ssoUser->getName(),
                    'sso_user_id' => $ssoUser->getId(),
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            if (!$user->wasRecentlyCreated) {
                $user->update([
                    'name' => $ssoUser->getName(),
                    'sso_user_id' => $ssoUser->getId(),
                ]);
            }

            Auth::login($user);
            request()->session()->regenerate();
            logger('After login -> ' . (Auth::check() ? 'YES' : 'NO'));

            return redirect('/dashboard');
        } catch (\Exception $e) {
            Log::error('SSO Login Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Login gagal. Silakan coba lagi.');
        }
    }
}
