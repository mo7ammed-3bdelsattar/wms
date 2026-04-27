<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovementOrderRequest;
use App\Http\Resources\MovementOrderResource;
use App\Models\MovementOrder;
use App\Services\MovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementOrderController extends Controller
{
    protected $movementService;

    public function __construct(MovementService $movementService)
    {
        $this->movementService = $movementService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = MovementOrder::with(['items.product', 'seller', 'buyer', 'supplier', 'warehouse', 'toWarehouse', 'movementType', 'reason']);

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15);
        return $this->sendResponse(MovementOrderResource::collection($orders)->response()->getData(true), 'Movement orders retrieved successfully.');
    }

    public function store(MovementOrderRequest $request): JsonResponse
    {
        try {
            $order = DB::transaction(function () use ($request) {
                $order = MovementOrder::create($request->validated());

                foreach ($request->items as $item) {
                    $order->items()->create($item);
                }

                $this->movementService->processMovement($order);

                return $order;
            });

            return $this->sendResponse(new MovementOrderResource($order->load(['items.product', 'seller', 'buyer', 'supplier', 'warehouse', 'toWarehouse', 'movementType', 'reason'])), 'Movement order processed successfully.', 201);
        } catch (\Exception $e) {
            return $this->sendError('Movement processing failed.', ['error' => $e->getMessage()], 422);
        }
    }

    public function show(MovementOrder $movementOrder): JsonResponse
    {
        return $this->sendResponse(new MovementOrderResource($movementOrder->load(['items.product', 'seller', 'buyer', 'supplier', 'warehouse', 'toWarehouse', 'movementType', 'reason'])), 'Movement order retrieved successfully.');
    }
}
