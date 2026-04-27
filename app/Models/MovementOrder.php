<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementOrder extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'seller_id',
        'buyer_id',
        'supplier_id',
        'warehouse_id',
        'to_warehouse_id',
        'movement_type_id',
        'reason_id',
        'notes',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function movementType()
    {
        return $this->belongsTo(MovementType::class);
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }

    public function items()
    {
        return $this->hasMany(MovementOrderItem::class);
    }
}
