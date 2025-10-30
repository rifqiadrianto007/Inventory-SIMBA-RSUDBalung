<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthApiController extends Controller
{
    /**
     * Issue token untuk user yang sudah login via SSO (web).
     */
    public function issueToken(Request $request)
    {
        // pastikan user login (SSO)
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'User belum login SSO'], 401);
        }

        // jika model memiliki relation tokens() (HasApiTokens trait), hapus token lama
        if (method_exists($user, 'tokens')) {
            try {
                $user->tokens()->delete();
            } catch (\Throwable $e) {
                // tidak fatal — lanjutkan
            }
        } else {
            // fallback: hapus token manual dari table personal_access_tokens jika ada
            try {
                DB::table('personal_access_tokens')
                    ->where('tokenable_type', get_class($user))
                    ->where('tokenable_id', $user->getKey())
                    ->delete();
            } catch (\Throwable $e) {
                // mungkin tabel tidak ada — lanjutkan
            }
        }

        // buat token: pakai createToken kalau tersedia, jika tidak berikan error informatif
        if (method_exists($user, 'createToken')) {
            $plain = $user->createToken('frontend-app-token')->plainTextToken;
            return response()->json([
                'message' => 'Token berhasil dibuat',
                'token' => $plain,
                'user' => $user,
            ], 200);
        }

        // Jika sampai sini, trait HasApiTokens kemungkinan belum ter-setup dengan benar.
        return response()->json([
            'error' => 'Fitur token API belum tersedia. Pastikan Laravel Sanctum terpasang dan model User meng-`use HasApiTokens`.',
            'hint' => [
                '1' => 'composer require laravel/sanctum',
                '2' => 'tambah "use Laravel\\Sanctum\\HasApiTokens;" dan "use HasApiTokens" pada model App\\Models\\User',
                '3' => 'php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" (jika perlu)',
                '4' => 'php artisan migrate',
                '5' => 'php artisan optimize:clear; composer dump-autoload'
            ]
        ], 500);
    }
}
