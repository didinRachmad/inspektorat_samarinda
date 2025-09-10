<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeTemuan extends Model
{
    use HasFactory;

    protected $fillable = ['kode', 'nama_temuan', 'parent_id', 'level', 'urutan'];

    // Relasi parent
    public function parent()
    {
        return $this->belongsTo(KodeTemuan::class, 'parent_id');
    }

    // Relasi children
    public function children()
    {
        return $this->hasMany(KodeTemuan::class, 'parent_id');
    }
}
