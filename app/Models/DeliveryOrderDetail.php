<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = ['delivery_order_id', 'product_id', 'qty', 'harga', 'diskon', 'subtotal'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }
}
