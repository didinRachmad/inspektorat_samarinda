<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'kode_customer',
        'nama_toko',
        'alamat',
        'pemilik',
        'id_pasar',
        'nama_pasar',
        'tipe_outlet',
    ];

    protected static function booted()
    {
        static::deleting(function ($parent) {
            if ($parent->isForceDeleting()) {
                return;
            }
        });

        static::restoring(function ($parent) {});
    }
}
