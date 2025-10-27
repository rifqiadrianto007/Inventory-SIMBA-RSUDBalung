<?php

namespace App\Services;

use App\Models\Notifikasi;

class NotificationService
{
    /**
     * Mengirim notifikasi ke user tertentu.
     *
     * @param int|null $userId → ID user tujuan (sso_user_id)
     * @param string $title → Judul notifikasi
     * @param string|null $message → Isi notifikasi
     * @param string|null $link → Link menuju halaman terkait
     * @return \App\Models\Notifikasi
     */
    public function send(?int $userId, string $title, ?string $message = null, ?string $link = null)
    {
        return Notifikasi::create([
            'sso_user_id' => $userId,
            'title'       => $title,
            'message'     => $message,
            'link'        => $link,
            'is_read'     => false,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca.
     *
     * @param int $id_notifikasi
     * @return bool
     */
    public function markAsRead(int $id_notifikasi)
    {
        $notif = Notifikasi::find($id_notifikasi);
        if (!$notif) return false;
        $notif->is_read = true;
        $notif->save();
        return true;
    }

    /**
     * Tandai semua notifikasi user sebagai dibaca.
     *
     * @param int $userId
     * @return int jumlah notifikasi yang diperbarui
     */
    public function markAllAsRead(int $userId)
    {
        return Notifikasi::where('sso_user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
