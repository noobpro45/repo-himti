<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mahasiswa extends Model
{
    use HasUuids;

    protected $table = 'tb_mahasiswa';
    protected $primaryKey = 'id_mahasiswa';

    protected $fillable = [
        'id_user',
        'nim',
        'program_studi',
        'angkatan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
