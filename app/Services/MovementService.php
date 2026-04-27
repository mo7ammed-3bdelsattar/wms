<?php

namespace App\Services;

use App\Models\MovementOrder;
use App\Models\Product;
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
                    case 'in':
                    case 'purchase':
                    case 'adjustment-in':
                        $this->increaseStock($item->product_id, $item->quantity);
                        break;
                    case 'out':
                    case 'sale':
                    case 'adjustment-out':
                        $this->decreaseStock($item->product_id, $item->quantity);
                        break;
                }
            }
        });
    }

    private function increaseStock($productId, $quantity)
    {
        $product = Product::findOrFail($productId);
        $product->increment('quantity', $quantity);
    }

    private function decreaseStock($productId, $quantity)
    {
        $product = Product::findOrFail($productId);

        if ($product->quantity < $quantity) {
            throw new \Exception("Insufficient stock for product: {$product->name}");
        }

        $product->decrement('quantity', $quantity);
    }
}
