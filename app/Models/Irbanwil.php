<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Irbanwil extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    public function auditis()
    {
        return $this->hasMany(Auditi::class);
    }
}
