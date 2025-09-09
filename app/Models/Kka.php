<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kka extends Model
{
    protected $fillable = [
        'lha_id',
        'judul',
        'uraian_prosedur',
        'hasil',
        'file_kka',
        'auditor_id'
    ];

    public function lha()
    {
        return $this->belongsTo(Lha::class);
    }

    public function auditor()
    {
        return $this->belongsTo(\App\Models\User::class, 'auditor_id');
    }
}
