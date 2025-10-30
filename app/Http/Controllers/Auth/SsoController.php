<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SSOController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('laravelpassport')->redirect();

    }

    public function callback(): RedirectResponse
    {
        try {
            Log::info('[SSO CALLBACK START]', request()->all());

            $ssoUser = Socialite::driver('laravelpassport')->user();


            $me = Http::withToken($ssoUser->token)
                ->get(config('services.laravelpassport.host') . '/api/me')
                ->throw()
                ->json();

            Log::info('[SSO /api/me RESPONSE]', $me);

            $roles       = collect($me['roles'] ?? [])->map(fn($r) => strtolower($r))->all();
            $primaryRole = $roles[0] ?? '';

            $user = User::where('sso_user_id', $ssoUser->getId())
                ->orWhere('email', $ssoUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'name'        => $ssoUser->getName(),
                    'email'       => $ssoUser->getEmail(),
                    'sso_user_id' => $ssoUser->getId(),
                    'role'        => $primaryRole !== '' ? $primaryRole : $user->role,
                ]);
            } else {
                $user = User::create([
                    'name'        => $ssoUser->getName(),
                    'email'       => $ssoUser->getEmail(),
                    'password'    => bcrypt(Str::random(16)),
                    'sso_user_id' => $ssoUser->getId(),
                    'role'        => $primaryRole,
                ]);
            }

            Auth::login($user);
            request()->session()->regenerate();

            Log::info('[CALLBACK AFTER LOGIN]', [
                'check' => Auth::check(),
                'user'  => Auth::id(),
                'role'  => $user->role,
            ]);

            return redirect()->route('after.sso');
        } catch (\Throwable $e) {
            Log::error('SSO Login Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect('/')->with('error', 'Login gagal. Silakan coba lagi.');
        }
    }
}
