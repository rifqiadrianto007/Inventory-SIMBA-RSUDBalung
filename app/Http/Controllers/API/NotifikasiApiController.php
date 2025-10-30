<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiApiController extends Controller
{
    /**
     * Ambil semua notifikasi untuk user tertentu (atau semua)
     */
    public function index(Request $request)
    {
        // Ambil user_id dari query, misal ?user_id=2
        $userId = $request->query('user_id');

        $query = Notifikasi::orderByDesc('created_at');

        if ($userId) {
            $query->where('sso_user_id', $userId);
        }

        $notes = $query->get();

        return response()->json([
            'ok' => true,
            'data' => $notes
        ]);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markRead($id)
    {
        $n = Notifikasi::find($id);

        if (!$n) {
            return response()->json(['error' => 'Notifikasi tidak ditemukan'], 404);
        }

        $n->update(['is_read' => true]);

        return response()->json([
            'ok' => true,
            'message' => 'Notifikasi berhasil ditandai dibaca.'
        ]);
    }
}
