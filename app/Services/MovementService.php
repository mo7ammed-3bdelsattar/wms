<?php

namespace App\Services;

use App\Models\MovementOrder;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\DB;

class MovementService
{
    /**
     * Process a movement order and update stock levels.
     */
    public function processMovement(MovementOrder $order)
    {
        $order->load('movementType');
        $typeSlug = $order->movementType->slug;

        DB::transaction(function () use ($order, $typeSlug) {
            foreach ($order->items as $item) {
                switch ($typeSlug) {
                    case 'purchase':
                        $this->increaseStock($item->product_id, $order->warehouse_id, $item->quantity);
                        break;
                    case 'sale':
                        $this->decreaseStock($item->product_id, $order->warehouse_id, $item->quantity);
                        break;
                    case 'transfer':
                        $this->decreaseStock($item->product_id, $order->warehouse_id, $item->quantity);
                        $this->increaseStock($item->product_id, $order->to_warehouse_id, $item->quantity);
                        break;
                    case 'adjustment-in':
                        $this->increaseStock($item->product_id, $order->warehouse_id, $item->quantity);
                        break;
                    case 'adjustment-out':
                        $this->decreaseStock($item->product_id, $order->warehouse_id, $item->quantity);
                        break;
                }
            }
        });
    }

    private function increaseStock($productId, $warehouseId, $quantity)
    {
        $stock = ProductWarehouse::firstOrCreate(
            ['product_id' => $productId, 'warehouse_id' => $warehouseId],
            ['quantity' => 0]
        );

        $stock->increment('quantity', $quantity);
    }

    private function decreaseStock($productId, $warehouseId, $quantity)
    {
        $stock = ProductWarehouse::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        if (!$stock || $stock->quantity < $quantity) {
            throw new \Exception("Insufficient stock for product ID: {$productId} in warehouse ID: {$warehouseId}");
        }

        $stock->decrement('quantity', $quantity);
    }
}
