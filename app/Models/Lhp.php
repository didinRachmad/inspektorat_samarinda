<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lhp extends Model
{
    use HasFactory;

    protected $fillable = [
        'pkpt_id',
        'auditi_id',
        'nomor_lhp',
        'tanggal_lhp',
        'file_lhp',
        'rekomendasi',
        'catatan',
        'created_by',
        'approval_status',
        'current_approval_sequence',
        'is_final_approved',
        'approval_note',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_lhp' => 'date', // otomatis jadi Carbon
    ];

    // Relasi ke PKPT
    public function pkpt()
    {
        return $this->belongsTo(Pkpt::class);
    }

    public function auditi()
    {
        return $this->belongsTo(Auditi::class);
    }

    // Relasi ke Temuan
    public function temuans()
    {
        return $this->hasMany(Temuan::class);
    }

    // Relasi ke KKA
    public function kkas()
    {
        return $this->hasMany(Kka::class);
    }

    // Relasi ke pembuat LHP
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function auditiUsers()
    {
        return $this->hasMany(User::class, 'auditi_id', 'auditi_id');
    }

    // Relasi ke user yang approve terakhir
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi approval route
    public function approvalRoutes()
    {
        // Asumsi: tabel approval_routes ada kolom module dan lhp_id atau module_id
        return $this->hasMany(ApprovalRoute::class, 'module_id')
            ->where('module', $this->module_name)
            ->orderBy('sequence');
    }

    // module_name bisa statis
    protected $module_name = 'lhp';
}
