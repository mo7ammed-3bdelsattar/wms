<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Traits\ImageUploadTrait;

class CategoryController extends Controller
{
    use ApiResponse, ImageUploadTrait;

    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')->with('image')->get();
        return $this->successResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        if ($request->hasFile('image')) {
            $this->uploadSingleImage($request->file('image'), $category, 'categories');
        }

        return $this->createdResponse(new CategoryResource($category->load('image')), 'Category created successfully.');
    }

    public function show(string $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->errorResponse('Category not found.', 404);
        }
        return $this->successResponse(new CategoryResource($category->loadCount('products')->load('image')), 'Category retrieved successfully.');
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        if ($request->hasFile('image')) {
            $this->uploadSingleImage($request->file('image'), $category, 'categories');
        } elseif ($request->boolean('delete_image')) {
            $this->deleteImage($category->image);
        }

        return $this->successResponse(new CategoryResource($category->fresh('image')), 'Category updated successfully.');
    }

    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->errorResponse('Category not found.', 404);
        }
        $this->deleteImage($category->image);
        $category->delete();
        return $this->successResponse(null, 'Category deleted successfully.');
    }
}
