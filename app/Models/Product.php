<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['kode_produk', 'nama_produk', 'harga', 'kemasan'];

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
