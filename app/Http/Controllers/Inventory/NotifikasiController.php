<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

/**
 * @method void middleware(string|array $middleware, array $options = [])
 */
class NotifikasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan semua notifikasi user login
     */
    public function index()
    {
        $userId = Auth::check() ? Auth::id() : null;

        if (!$userId) {
            return response()->json(['error' => 'User belum login'], 401);
        }

        $notes = Notifikasi::where('sso_user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($notes);
    }

    /**
     * Tandai notifikasi sebagai dibaca
     */
    public function markRead($id)
    {
        $note = Notifikasi::findOrFail($id);
        $note->is_read = true;
        $note->save();

        return response()->json(['message' => 'Notifikasi telah ditandai sebagai dibaca.']);
    }

    /**
     * Tandai semua notifikasi user sebagai dibaca
     */
    public function markAllRead()
    {
        $userId = Auth::check() ? Auth::id() : null;

        if (!$userId) {
            return response()->json(['error' => 'User belum login'], 401);
        }

        Notifikasi::where('sso_user_id', $userId)->update(['is_read' => true]);

        return response()->json(['message' => 'Semua notifikasi telah ditandai sebagai dibaca.']);
    }
}
