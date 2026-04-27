<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementOrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'movement_order_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    public function movementOrder()
    {
        return $this->belongsTo(MovementOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
