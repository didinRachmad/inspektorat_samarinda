<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeRekomendasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama_rekomendasi',
        'urutan',
    ];

    // Relasi ke kode temuan
    public function temuans()
    {
        return $this->belongsToMany(
            KodeTemuan::class,
            'kode_temuan_rekomendasi',
            'kode_rekomendasi_id',
            'kode_temuan_id'
        );
    }
}
