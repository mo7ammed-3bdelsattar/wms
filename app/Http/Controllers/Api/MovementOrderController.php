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
use App\Traits\ApiResponse;

class MovementOrderController extends Controller
{
    use ApiResponse;

    protected $movementService;

    public function __construct(MovementService $movementService)
    {
        $this->movementService = $movementService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = MovementOrder::with(['items.product', 'seller', 'buyer', 'supplier', 'movementType', 'reason']);

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
        return $this->successResponse(MovementOrderResource::collection($orders)->response()->getData(true), 'Movement orders retrieved successfully.');
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

            return $this->createdResponse(new MovementOrderResource($order->load(['items.product', 'seller', 'buyer', 'supplier', 'movementType', 'reason'])), 'Movement order processed successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Movement processing failed. ' . $e->getMessage(), 422);
        }
    }

    public function show(string $id): JsonResponse
    {
        $movementOrder = MovementOrder::find($id);
        if (!$movementOrder) {
            return $this->errorResponse('Movement order not found.', 404);
        }
        return $this->successResponse(new MovementOrderResource($movementOrder->load(['items.product', 'seller', 'buyer', 'supplier', 'movementType', 'reason'])), 'Movement order retrieved successfully.');
    }
}
