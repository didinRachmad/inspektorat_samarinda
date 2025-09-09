<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemuanRekomendasi extends Model
{
    use HasFactory;

    protected $fillable = ['temuan_id', 'kode_rekomendasi', 'rekomendasi_temuan'];

    public function temuan()
    {
        return $this->belongsTo(Temuan::class);
    }
}
