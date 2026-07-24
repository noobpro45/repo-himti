<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'tb_pengaturan';

    protected $fillable = [
        'kunci',
        'nilai',
        'deskripsi',
    ];

    // ── Static helpers ──

    public static function ambil(string $kunci, mixed $default = null): mixed
    {
        $row = static::where('kunci', $kunci)->first();
        return $row ? $row->nilai : $default;
    }

    public static function setel(string $kunci, mixed $nilai): static
    {
        return static::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => (string) $nilai],
        );
    }
}
