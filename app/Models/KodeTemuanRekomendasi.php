<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeTemuanRekomendasi extends Model
{
    use HasFactory;

    protected $fillable = ['kode_temuan_id', 'kode_rekomendasi_id'];

    // Model KodeTemuan
    public function rekomendasis()
    {
        return $this->belongsToMany(KodeRekomendasi::class, 'kode_temuan_rekomendasi');
    }

    // Model KodeRekomendasi
    public function temuans()
    {
        return $this->belongsToMany(KodeTemuan::class, 'kode_temuan_rekomendasi');
    }
}
