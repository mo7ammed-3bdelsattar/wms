<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Traits\ImageUploadTrait;

class ProductController extends Controller
{
    use ApiResponse, ImageUploadTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Product::filter()->with(['category', 'images']);

        $products = $query->paginate(15);
        return $this->successResponse($this->resourceCollection($products, ProductResource::class), 'Products retrieved successfully.');
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        
        if ($request->hasFile('images')) {
            $this->uploadMultipleImages($request->file('images'), $product, 'products');
        }

        return $this->createdResponse(new ProductResource($product->load('images')), 'Product created successfully.');
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found.', 404);
        }
        return $this->successResponse(new ProductResource($product->load(['category', 'images'])), 'Product retrieved successfully.');
    }

    public function update(ProductRequest $request, string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found.', 404);
        }
        $product->update($request->validated());

        if ($request->hasFile('images')) {
            $this->uploadMultipleImages($request->file('images'), $product, 'products');
        }

        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $imageId) {
                $image = $product->images()->find($imageId);
                if ($image) {
                    $this->deleteImage($image);
                }
            }
        }

        return $this->successResponse(new ProductResource($product->fresh(['images'])), 'Product updated successfully.');
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found.', 404);
        }
        foreach ($product->images as $image) {
            $this->deleteImage($image);
        }
        $product->delete();
        return $this->successResponse(null, 'Product deleted successfully.');
    }
}
