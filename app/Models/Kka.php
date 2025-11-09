<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kka extends Model
{
    protected $fillable = [
        'lhp_id',
        'judul',
        'uraian_prosedur',
        'hasil',
        'file_kka',
        'auditor_id'
    ];

    public function lhp()
    {
        return $this->belongsTo(Lhp::class);
    }

    public function auditor()
    {
        return $this->belongsTo(\App\Models\User::class, 'auditor_id');
    }
}
