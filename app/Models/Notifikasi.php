<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'sso_user_id',
        'title',
        'message',
        'link',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    // 🔗 Relasi ke user_inventory
    public function user()
    {
        return $this->belongsTo(UserInventory::class, 'sso_user_id', 'user_id');
    }

    // 🔹 Helper untuk menandai notifikasi sudah dibaca
    public function markAsRead(): void
    {
        $this->is_read = true;
        $this->save();
    }

    // 🔹 Helper cepat untuk kirim notifikasi baru
    public static function send(int $userId, string $title, string $message = null, string $link = null): self
    {
        return self::create([
            'sso_user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false
        ]);
    }
}
