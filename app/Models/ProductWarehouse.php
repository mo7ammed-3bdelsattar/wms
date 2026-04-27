<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductWarehouse extends Pivot
{
    use HasFactory, HasUuids;

    protected $table = 'product_warehouses';

    public $incrementing = false;

    protected $fillable = ['product_id', 'warehouse_id', 'quantity'];
}
