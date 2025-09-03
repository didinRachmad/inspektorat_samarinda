<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = ['no_so', 'customer_id', 'metode_pembayaran', 'tanggal', 'total_qty', 'total_diskon', 'grand_total', 'approval_level', 'status', 'keterangan'];
    protected $casts = [
        'tanggal' => 'date',
    ];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function details()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }
}
