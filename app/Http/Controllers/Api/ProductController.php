<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use HttpResponses;

    /**
     * Get all products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $products = Product::search(
            $request->query('search')
        )->get();

        if ($products->isEmpty())
            return $this->errorResponse('No products found', 'Not Found', 404);

        $data = $this->collection($products);

        return $this->successResponse($data, 'Products retrieved successfully');
    }

    /**
     * Get a product by id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $productId)
    {
        $product = Product::with('unit', 'categories', 'shop')->find($productId);

        if (!$product)
            return $this->errorResponse('Product not found', 'Not Found', 404);

        $data = new ProductResource($product);

        return $this->successResponse($data, 'Product retrieved successfully');
    }

    /**
     * Create a new product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->merge(['shop_id' => session('shop_id')]);
        $request->validate(Product::$rules);

        try {
            $product = Product::create($request->all());

            if ($request->categories)
                $product->categories()->attach($request->categories);
        } catch (\Throwable $th) {
            return $this->errorResponse($th, 'Internal Server Error', 500);
        }

        return $this->successResponse(new ProductResource($product), 'Product created successfully', 201);
    }

    /**
     * Update a product by id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $productId)
    {
        $product = Product::find($productId);

        if (!$product)
            return $this->errorResponse('Product not found', 'Not Found', 404);

        $request->merge(['shop_id' => session('shop_id')]);
        $request->validate(Product::$rules);

        try {
            $product->update($request->all());
            $product->categories()->detach();
            if ($request->categories)
                $product->categories()->attach($request->categories);

        } catch (\Throwable $th) {
            return $this->errorResponse($th, 'Internal Server Error', 500);
        }

        return $this->successResponse(new ProductResource($product), 'Product updated successfully');
    }

    /**
     * Delete a product by id.
     *
     * @param  string  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $productId)
    {
        $product = Product::find($productId);

        if (!$product)
            return $this->errorResponse('Product not found', 'Not Found', 404);

        try {
            $product->delete();
        } catch (\Throwable $th) {
            return $this->errorResponse($th, 'Internal Server Error', 500);
        }

        return $this->successResponse(null, 'Product deleted successfully');
    }

    private function collection($products)
    {
        return $products->transform(function (Product $product) {
            return (new ProductResource($product));
        });
    }
}
