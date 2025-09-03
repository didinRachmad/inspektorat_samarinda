<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkptAuditor extends Model
{
    use HasFactory;
    protected $fillable = ['pkpt_id', 'user_id', 'nama_manual', 'jabatan'];

    public function pkpt()
    {
        return $this->belongsTo(Pkpt::class);
    }
}
