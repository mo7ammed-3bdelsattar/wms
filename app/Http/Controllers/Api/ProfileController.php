<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    use ApiResponse, ImageUploadTrait;
    public function show(): JsonResponse
    {
        return $this->successResponse(new UserResource(request()->user()), 'User profile retrieved successfully.');
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->hasFile('avatar')) {
            $this->uploadSingleImage($request->file('avatar'), $user, 'users');
        }

        $user->save();

        return $this->successResponse(new UserResource($user->fresh()), 'Profile updated successfully.');
    }

    public function deleteAvatar()
    {
        $user = request()->user();
        if ($user->image) {
            $this->deleteImage($user->image);
        } else {
            return $this->errorResponse('No avatar found.', 404);
        }
        return $this->successResponse([],"Avatar Deleted Successfully");
    }
}
