<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_auditi',
        'kode_auditi',
        'alamat',
        'telepon',
    ];

    public function pkpts()
    {
        return $this->hasMany(Pkpt::class);
    }
}
