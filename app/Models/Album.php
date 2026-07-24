<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Album extends Model
{
    use HasUuids;

    protected $table = 'tb_album';
    protected $primaryKey = 'id_album';

    protected $fillable = [
        'id_user',
        'nama_acara',
        'slug',
        'tanggal_acara',
        'tanggal_acara_selesai',
        'deskripsi',
        'cover_gradient',
        'id_media_cover',
        'cover_position',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_acara'         => 'date',
            'tanggal_acara_selesai' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Boot ──

    protected static function booted(): void
    {
        static::creating(function (Album $album) {
            if (empty($album->slug)) {
                $album->slug = Str::slug($album->nama_acara);
            }
            if (empty($album->cover_gradient)) {
                $palettes = [
                    '#1B2D6B,#0F172A', '#2EB253,#1E3A24', '#2E8B57,#153F2A',
                    '#B9503F,#4E2119', '#4B5F73,#1D2733', '#7A4FA0,#2E1E3D',
                    '#2438A0,#1B285A', '#3CC964,#1F5229',
                ];
                $album->cover_gradient = $palettes[array_rand($palettes)];
            }
        });
    }

    // ── Relationships ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'id_media_cover', 'id_media');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'id_album', 'id_album');
    }

    // ── Computed ──

    public function getTotalUkuranAttribute(): int
    {
        return $this->attributes['media_sum_ukuran_byte'] ?? $this->media()->sum('ukuran_byte') ?? 0;
    }

    public function getTotalMediaAttribute(): int
    {
        return $this->attributes['media_count'] ?? $this->media()->count();
    }

    public function getTotalUkuranFormattedAttribute(): string
    {
        $bytes = $this->total_ukuran;
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

    public function getTanggalFormattedAttribute(): string
    {
        $mulai = $this->tanggal_acara->translatedFormat('j M Y');
        if ($this->tanggal_acara_selesai && $this->tanggal_acara_selesai->ne($this->tanggal_acara)) {
            return $mulai . ' - ' . $this->tanggal_acara_selesai->translatedFormat('j M Y');
        }
        return $mulai;
    }

    public function getCoverGradientCssAttribute(): string
    {
        $colors = $this->cover_gradient ?? '#1B2D6B,#0F172A';
        return "linear-gradient(135deg, {$colors})";
    }
}
