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
        'movement_type_id',
        'reason_id',
        'notes',
    ];

    public function seller()
    {
        return $this->belongsTo(Partner::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Partner::class, 'buyer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Partner::class, 'supplier_id');
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
