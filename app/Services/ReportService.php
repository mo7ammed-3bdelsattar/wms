<?php

namespace App\Services;

use App\Models\MovementOrder;
use App\Models\MovementOrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getTopProducts($limit = 10)
    {
        return MovementOrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    public function getTopCategories($limit = 10)
    {
        return DB::table('movement_order_items')
            ->join('products', 'movement_order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(movement_order_items.quantity) as total_quantity'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    public function getRevenuePerWarehouse()
    {
        // Revenue calculated from 'sale' movement types
        return DB::table('movement_orders')
            ->join('movement_types', 'movement_orders.movement_type_id', '=', 'movement_types.id')
            ->join('movement_order_items', 'movement_orders.id', '=', 'movement_order_items.movement_order_id')
            ->join('warehouses', 'movement_orders.warehouse_id', '=', 'warehouses.id')
            ->where('movement_types.slug', 'sale')
            ->select('warehouses.name', DB::raw('SUM(movement_order_items.quantity * movement_order_items.unit_price) as total_revenue'))
            ->groupBy('warehouses.id', 'warehouses.name')
            ->get();
    }

    public function getPerSupplierReport()
    {
        return DB::table('movement_orders')
            ->join('suppliers', 'movement_orders.supplier_id', '=', 'suppliers.id')
            ->join('movement_order_items', 'movement_orders.id', '=', 'movement_order_items.movement_order_id')
            ->select('suppliers.name', 
                DB::raw('COUNT(DISTINCT movement_orders.id) as orders_count'),
                DB::raw('SUM(movement_order_items.quantity * movement_order_items.unit_price) as total_purchases')
            )
            ->groupBy('suppliers.id', 'suppliers.name')
            ->get();
    }
}
