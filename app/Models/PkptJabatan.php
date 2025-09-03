<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkptJabatan extends Model
{
    use HasFactory;
    protected $fillable = ['pkpt_id', 'jabatan', 'jumlah', 'anggaran'];

    public function pkpt()
    {
        return $this->belongsTo(Pkpt::class);
    }
}
