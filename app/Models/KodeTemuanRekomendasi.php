<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeTemuanRekomendasi extends Model
{
    use HasFactory;

    protected $fillable = ['kode_temuan_id', 'kode_rekomendasi_id'];
}
