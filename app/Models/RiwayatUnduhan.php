<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatUnduhan extends Model
{
    use HasUuids;

    protected $table = 'tb_riwayat_unduhan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_user',
        'id_media',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'id_media', 'id_media');
    }
}
