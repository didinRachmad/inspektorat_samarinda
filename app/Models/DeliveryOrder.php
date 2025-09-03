<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $fillable = ['no_do', 'sales_order_id', 'tanggal', 'origin', 'origin_name', 'destination', 'destination_name', 'total_qty', 'total_diskon', 'grand_total', 'approval_level', 'status', 'keterangan'];
    protected $casts = [
        'tanggal' => 'date',
    ];

    public function sales_order()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function details()
    {
        return $this->hasMany(DeliveryOrderDetail::class);
    }

    public function shippings()
    {
        return $this->hasMany(DeliveryOrderShipping::class);
    }
}
