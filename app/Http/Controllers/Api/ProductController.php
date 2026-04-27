<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'warehouses']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('warehouse_id')) {
            $query->whereHas('warehouses', function ($q) use ($request) {
                $q->where('warehouses.id', $request->warehouse_id);
            });
        }

        $products = $query->paginate(15);
        return $this->sendResponse(ProductResource::collection($products)->response()->getData(true), 'Products retrieved successfully.');
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return $this->sendResponse(new ProductResource($product), 'Product created successfully.', 201);
    }

    public function show(Product $product): JsonResponse
    {
        return $this->sendResponse(new ProductResource($product->load(['category', 'warehouses'])), 'Product retrieved successfully.');
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
