<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function index(): JsonResponse
    {
        $warehouses = Warehouse::with('location')->get();
        return $this->sendResponse(WarehouseResource::collection($warehouses), 'Warehouses retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $warehouse = Warehouse::create($request->all());
        return $this->sendResponse(new WarehouseResource($warehouse), 'Warehouse created successfully.', 201);
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->sendResponse(new WarehouseResource($warehouse->load('location')), 'Warehouse retrieved successfully.');
    }

    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $warehouse->update($request->all());
        return $this->sendResponse(new WarehouseResource($warehouse), 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $warehouse->delete();
        return $this->sendResponse([], 'Warehouse deleted successfully.');
    }
}
