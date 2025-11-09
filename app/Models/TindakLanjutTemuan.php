<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakLanjutTemuan extends Model
{
    use HasFactory;
    protected $fillable = [
        'lhp_id',
        'temuan_id',
        'auditi_id',
        'deskripsi_tindak_lanjut',
        'lampiran',
        'tanggal_tindak_lanjut',
        'approval_status',
        'current_approval_sequence',
        'is_final_approved',
        'approval_note',
        'approved_by',
        'approved_at',
    ];

    public function lhp()
    {
        return $this->belongsTo(Lhp::class);
    }

    public function temuan()
    {
        return $this->belongsTo(Temuan::class);
    }

    public function auditi()
    {
        return $this->belongsTo(Auditi::class, 'auditi_id');
    }

    public function auditiUser()
    {
        return $this->belongsTo(User::class, 'auditi_id', 'auditi_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
