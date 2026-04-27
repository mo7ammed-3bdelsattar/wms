<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Location::withCount('warehouses')->get();
        return $this->sendResponse(LocationResource::collection($locations), 'Locations retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:locations',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $location = Location::create($request->all());
        return $this->sendResponse(new LocationResource($location), 'Location created successfully.', 201);
    }

    public function show(Location $location): JsonResponse
    {
        return $this->sendResponse(new LocationResource($location->loadCount('warehouses')), 'Location retrieved successfully.');
    }

    public function update(Request $request, Location $location): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $location->update($request->all());
        return $this->sendResponse(new LocationResource($location), 'Location updated successfully.');
    }

    public function destroy(Location $location): JsonResponse
    {
        $location->delete();
        return $this->sendResponse([], 'Location deleted successfully.');
    }
}
