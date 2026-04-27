<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerRequest;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class PartnerController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Partner::query();
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $partners = $query->get();
        return $this->successResponse(PartnerResource::collection($partners), 'Partners retrieved successfully.');
    }

    public function store(PartnerRequest $request): JsonResponse
    {
        $partner = Partner::create($request->validated());
        return $this->createdResponse(new PartnerResource($partner), 'Partner created successfully.');
    }

    public function show(string $id): JsonResponse
    {
        $partner = Partner::find($id);
        if (!$partner) {
            return $this->errorResponse('Partner not found.', 404);
        }
        return $this->successResponse(new PartnerResource($partner), 'Partner retrieved successfully.');
    }

    public function update(PartnerRequest $request, string $id): JsonResponse
    {
        $partner = Partner::find($id);
        if (!$partner) {
            return $this->errorResponse('Partner not found.', 404);
        }
        $partner->update($request->validated());
        return $this->successResponse(new PartnerResource($partner), 'Partner updated successfully.');
    }

    public function destroy(string $id): JsonResponse
    {
        $partner = Partner::find($id);
        if (!$partner) {
            return $this->errorResponse('Partner not found.', 404);
        }
        $partner->delete();
        return $this->successResponse(null, 'Partner deleted successfully.');
    }
}
