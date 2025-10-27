<?php

namespace App\Services;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogService
{
    /**
     * Catat log aktivitas ke database.
     *
     * @param string $action  → Jenis aksi, misalnya: 'create', 'update', 'delete', 'approve'
     * @param string $module  → Nama modul, misalnya: 'pemesanan', 'penerimaan', 'item'
     * @param string|null $description → Penjelasan tambahan
     * @return \App\Models\LogActivity
     */
    public function record(string $action, string $module, ?string $description = null)
    {
        $userId = Auth::check() ? Auth::id() : null;

        return LogActivity::create([
            'sso_user_id' => $userId,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'ip_address'  => Request::ip(),
        ]);
    }
}
