<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasUuids;

    protected $table = 'tb_media';
    protected $primaryKey = 'id_media';

    protected $fillable = [
        'id_album',
        'id_user',
        'nama_file_asli',
        'nama_file_server',
        'path_file',
        'path_thumbnail',
        'mime_type',
        'tipe',
        'ukuran_byte',
        'width',
        'height',
        'durasi',
        'status_proses',
        'catatan_proses',
        'file_hash',
    ];

    // ── Relationships ──

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class, 'id_album', 'id_album');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // ── Computed ──

    public function getUkuranFormattedAttribute(): string
    {
        $bytes = $this->ukuran_byte;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 1) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->tipe === 'video';
    }

    public function getIsFotoAttribute(): bool
    {
        return $this->tipe === 'foto';
    }

    public function getResolusiAttribute(): ?string
    {
        if ($this->width && $this->height) {
            $mp = round(($this->width * $this->height) / 1_000_000, 1);
            return "{$mp}MP {$this->width}x{$this->height}";
        }
        return null;
    }
}
