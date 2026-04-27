<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'type',
        'name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address',
    ];

    public function movementOrders()
    {
        // This won't perfectly capture all relations since we might relate as seller, buyer, or supplier.
        // But we'll leave this empty or define specific relations if needed.
        return $this->hasMany(MovementOrder::class, 'supplier_id');
    }
}
