<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRoute extends Model
{
    use HasFactory;

    protected $fillable = ['module', 'role_id', 'sequence', 'assigned_user_id'];

    // Relasi ke Role (gunakan model Role milik Anda)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relasi ke User (jika ada assigned user)
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
