<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lha extends Model
{
    protected $fillable = [
        'pkpt_id',
        'nomor_lha',
        'tanggal_lha',
        'uraian_temuan',
        'rekomendasi',
        'file_lha',
        'created_by',
    ];

    protected $casts = [
        'tanggal_lha' => 'date', // ✅ otomatis jadi Carbon
    ];

    public function pkpt()
    {
        return $this->belongsTo(Pkpt::class);
    }


    public function kkas()
    {
        return $this->hasMany(Kka::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function temuan()
    {
        return $this->hasMany(Temuan::class);
    }

    public function hpds()
    {
        return $this->hasMany(Hpd::class);
    }
}
