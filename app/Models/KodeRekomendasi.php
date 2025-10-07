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

    public function kodeTemuans()
    {
        return $this->belongsToMany(
            KodeTemuan::class,
            'kode_rekomendasi_kode_temuan',
            'kode_rekomendasi_id',
            'kode_temuan_id'
        );
    }
}
