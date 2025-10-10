<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    private array $httpOpts = [
        'force_ip_resolve' => 'v4',
        'curl'             => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4],
    ];

    public function redirect(Request $r)
    {
        $state = Str::random(40);
        $r->session()->put('oauth_state', $state);

        $redirect = (string) config('services.sso.redirect');
        if (str_starts_with($redirect, '//'))       $redirect = 'http:' . $redirect;
        elseif (!preg_match('#^https?://#i', $redirect)) $redirect = 'http://' . ltrim($redirect, '/');

        $q = http_build_query([
            'client_id'     => config('services.sso.client_id'),
            'redirect_uri'  => $redirect,
            'response_type' => 'code',
            'scope'         => config('services.sso.scope'),
            'state'         => $state,
        ]);

        $authUrl = rtrim(config('services.sso.base'), '/') . '/oauth/authorize?' . $q;
        Log::info('AUTH_URL', ['url' => $authUrl]);

        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        abort_unless($request->state === $request->session()->pull('oauth_state'), 403, 'Invalid state');

        // Tukar code -> token
        $tokenRes = Http::asForm()
            ->withOptions($this->httpOpts)
            ->timeout(12)
            ->post(rtrim(config('services.sso.base'), '/') . '/oauth/token', [
                'grant_type'    => 'authorization_code',
                'client_id'     => config('services.sso.client_id'),
                'client_secret' => config('services.sso.client_secret'),
                'redirect_uri'  => config('services.sso.redirect'),
                'code'          => $request->code,
            ])->throw()->json();

        // Log::info('SSO token response', $tokenRes);

        $accessToken  = $tokenRes['access_token']  ?? null;
        $refreshToken = $tokenRes['refresh_token'] ?? null;
        if (!$accessToken) {
            Log::warning('SSO: no access_token', ['resp' => $tokenRes]);
            abort(500, 'No access token from SSO');
        }

        // Ambil profil user
        $me = Http::withOptions([
            'force_ip_resolve' => 'v4',
            'verify' => false,         // lokal, non-SSL
            'proxy'  => null,          // matikan proxy env
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
        ])
            ->withHeaders([
                'Host' => 'sso-server.test',            // penting untuk vhost
            ])
            ->withToken($accessToken)
            ->acceptJson()
            ->timeout(30)
            ->get('http://127.0.0.1/api/me')           // ← pakai IP langsung
            ->throw()
            ->json();


        // Sinkron & login user lokal
        $user = User::updateOrCreate(
            ['email' => $me['email']],
            ['name'  => $me['name'] ?? $me['email'], 'password' => Hash::make(Str::random(40))]
        );
        Auth::login($user);

        // Simpan token (opsional untuk call API selanjutnya)
        $request->session()->put([
            'sso_access_token'  => $accessToken,
            'sso_refresh_token' => $refreshToken,
        ]);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
