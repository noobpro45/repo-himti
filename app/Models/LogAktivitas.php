<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitas extends Model
{
    use HasUuids;

    protected $table = 'tb_log_aktivitas';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_user',
        'aktivitas',
        'alamat_ip',
        'detail',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // ── Helper: log an activity ──

    public static function catat(
        string $idUser,
        string $aktivitas,
        ?string $alamatIp = null,
        ?string $detail = null
    ): static {
        return static::create([
            'id_user'   => $idUser,
            'aktivitas' => $aktivitas,
            'alamat_ip' => $alamatIp,
            'detail'    => $detail,
        ]);
    }
}
