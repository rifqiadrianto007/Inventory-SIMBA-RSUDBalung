<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;

class NotifikasiApiController extends Controller
{
    /**
     * GET /api/notifikasi
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Prioritaskan sso_user_id jika tersedia, fallback ke id (user.id)
        $ssoUserId = $user->sso_user_id ?? $user->id ?? null;

        $data = Notifikasi::where('sso_user_id', $ssoUserId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['ok' => true, 'data' => $data]);
    }

    /**
     * POST /api/notifikasi/{id}/read
     */
    public function markRead(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $ssoUserId = $user->sso_user_id ?? $user->id ?? null;

        $notif = Notifikasi::where('id_notifikasi', $id)
            ->where('sso_user_id', $ssoUserId)
            ->first();

        if (!$notif) {
            return response()->json(['ok' => false, 'error' => 'Notifikasi tidak ditemukan atau bukan milik Anda'], 404);
        }

        $notif->is_read = true;
        $notif->save();

        return response()->json(['ok' => true, 'message' => 'Notifikasi ditandai dibaca']);
    }
}
