<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hpd extends Model
{
    use HasFactory;

    protected $fillable = [
        'lha_id',
        'opd_id',
        'tahun',
        'judul',
        'kondisi',
        'kriteria',
        'saran',
        'file',
    ];

    public function lha()
    {
        return $this->belongsTo(Lha::class);
    }
}
