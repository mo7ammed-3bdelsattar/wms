<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ImageUploadTrait;

class AuthController extends Controller
{
    use ApiResponse, ImageUploadTrait;

    /**
     * Login user and create token.
     */
    public function login(AuthRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = Auth::user();
        $token = $user->createToken('auth_token', ['*']);
        $plaintext = $token->plainTextToken;
        $token->accessToken->update([
            'expires_at' => now()->addMonths(6),
        ]);

        return $this->successResponse([
            'user' => new UserResource($user),
            'access_token' => $plaintext,
            'token_type' => 'Bearer',
        ], 'User logged in successfully.');
    }

    /**
     * Logout user (Revoke the token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'User logged out successfully.');
    }

    /**
     * Get the authenticated User.
     */
    public function profile(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user()->load('image')), 'User profile retrieved successfully.');
    }

    /**
     * Update user profile including image.
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        $user->save();

        if ($request->hasFile('image')) {
            $this->uploadSingleImage($request->file('image'), $user, 'avatars');
        } elseif ($request->boolean('delete_image')) {
            $this->deleteImage($user->image);
        }

        return $this->successResponse(new UserResource($user->fresh(['image'])), 'Profile updated successfully.');
    }
}
