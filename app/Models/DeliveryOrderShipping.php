<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderShipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_order_id',
        'courier_code',
        'courier_name',
        'courier_service_name',
        'shipment_duration_range',
        'price',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }
}
