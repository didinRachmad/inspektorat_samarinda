<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPengawasan extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'parent_id', 'urutan'];

    // Relasi ke parent
    public function parent()
    {
        return $this->belongsTo(JenisPengawasan::class, 'parent_id');
    }

    // Relasi ke children
    public function children()
    {
        return $this->hasMany(JenisPengawasan::class, 'parent_id');
    }
}