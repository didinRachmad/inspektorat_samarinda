<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pkpt extends Model
{
    use HasFactory;
    protected $fillable = [
        'tahun',
        'bulan',
        'no_pkpt',
        'auditi_id',
        'ruang_lingkup',
        'sasaran',
        'jenis_pengawasan',
        'jadwal_rmp_bulan',
        'jadwal_rsp_bulan',
        'jadwal_rpl_bulan',
        'jadwal_hp_hari',
        'jumlah_tenaga',
        'hp_5x6',
        'anggaran_total',
        'irbanwil',
        'auditor_ringkas',
        'keterangan',
        'pkpt',
        'file_surat_tugas',
        'created_by',
        'updated_by'
    ];

    public function jabatans()
    {
        return $this->hasMany(PkptJabatan::class);
    }

    public function auditors()
    {
        return $this->hasMany(PkptAuditor::class);
    }

    public function auditi()
    {
        return $this->belongsTo(Auditi::class);
    }
}
