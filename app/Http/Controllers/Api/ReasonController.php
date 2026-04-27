<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReasonRequest;
use App\Http\Resources\ReasonResource;
use App\Models\Reason;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class ReasonController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $reasons = Reason::all();
        return $this->successResponse(ReasonResource::collection($reasons), 'Reasons retrieved successfully.');
    }

    public function store(ReasonRequest $request): JsonResponse
    {
        $reason = Reason::create($request->validated());
        return $this->createdResponse(new ReasonResource($reason), 'Reason created successfully.');
    }

    public function show(string $id): JsonResponse
    {
        $reason = Reason::find($id);
        if (!$reason) {
            return $this->errorResponse('Reason not found.', 404);
        }
        return $this->successResponse(new ReasonResource($reason), 'Reason retrieved successfully.');
    }

    public function update(ReasonRequest $request, string $id): JsonResponse
    {
        $reason = Reason::find($id);
        if (!$reason) {
            return $this->errorResponse('Reason not found.', 404);
        }
        $reason->update($request->validated());
        return $this->successResponse(new ReasonResource($reason), 'Reason updated successfully.');
    }

    public function destroy(string $id): JsonResponse
    {
        $reason = Reason::find($id);
        if (!$reason) {
            return $this->errorResponse('Reason not found.', 404);
        }
        $reason->delete();
        return $this->successResponse(null, 'Reason deleted successfully.');
    }
}
