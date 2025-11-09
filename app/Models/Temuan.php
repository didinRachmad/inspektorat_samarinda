<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'lhp_id',
        'judul_temuan',
        'kode_temuan_id',
        'kondisi_temuan',
        'kriteria_temuan',
        'sebab_temuan',
        'akibat_temuan'
    ];

    public function lhp()
    {
        return $this->belongsTo(Lhp::class);
    }

    public function kodeTemuan()
    {
        return $this->belongsTo(KodeTemuan::class);
    }

    public function rekomendasis()
    {
        return $this->hasMany(TemuanRekomendasi::class);
    }

    public function files()
    {
        return $this->hasMany(TemuanFile::class);
    }
}
