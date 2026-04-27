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
        return MovementOrderItem::with('product:id,name')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
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

    public function getTotalRevenue()
    {
        // Revenue calculated from 'sale' or 'out' movement types
        return DB::table('movement_orders')
            ->join('movement_types', 'movement_orders.movement_type_id', '=', 'movement_types.id')
            ->join('movement_order_items', 'movement_orders.id', '=', 'movement_order_items.movement_order_id')
            ->whereIn('movement_types.slug', ['sale', 'out'])
            ->select(DB::raw('SUM(movement_order_items.quantity * movement_order_items.unit_price) as total_revenue'))
            ->get();
    }

    public function getPerSupplierReport()
    {
        return DB::table('movement_orders')
            ->join('partners', 'movement_orders.supplier_id', '=', 'partners.id')
            ->join('movement_order_items', 'movement_orders.id', '=', 'movement_order_items.movement_order_id')
            ->where('partners.type', 'supplier')
            ->select('partners.name', 
                DB::raw('COUNT(DISTINCT movement_orders.id) as orders_count'),
                DB::raw('SUM(movement_order_items.quantity * movement_order_items.unit_price) as total_purchases')
            )
            ->groupBy('partners.id', 'partners.name')
            ->get();
    }
}
