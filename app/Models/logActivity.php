<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogActivity extends Model
{
    protected $table = 'log_activity';
    protected $primaryKey = 'id_log';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'sso_user_id',
        'action',
        'module',
        'description',
        'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(UserInventory::class, 'sso_user_id', 'user_id');
    }

    /**
     * Buat log dengan aman — tidak melempar error kalau tidak ada auth atau request (mis. di CLI)
     *
     * @param string $action
     * @param string $module
     * @param string|null $description
     * @return self
     */
    public static function record(string $action, string $module, ?string $description = null): self
    {
        // Ambil id user dengan facade Auth (lebih dapat diandalkan)
        $userId = Auth::id();

        // Jika menggunakan custom user model dengan primaryKey 'user_id' dan Auth::id() tidak bekerja,
        // coba fallback ke Auth::user()->user_id bila tersedia:
        if (!$userId && Auth::user()) {
            $user = Auth::user();
            if (isset($user->user_id)) {
                $userId = $user->user_id;
            } elseif (isset($user->id)) {
                $userId = $user->id;
            }
        }

        // Ambil IP secara aman (fallback untuk console)
        $ip = null;
        try {
            $ip = Request::ip();
        } catch (\Throwable $e) {
            $ip = app()->runningInConsole() ? '127.0.0.1' : null;
        }

        return static::create([
            'sso_user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => $ip,
        ]);
    }
}
