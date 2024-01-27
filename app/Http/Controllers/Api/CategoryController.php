<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HttpResponses;

    /**
     * Get all categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::search(
            request()->query('search')
        )->get();

        if ($categories->isEmpty())
            return $this->errorResponse('No categories found', 'Not Found', 404);

        return $this->successResponse($this->collection($categories), 'Categories retrieved successfully');
    }

    /**
     * Get a category by id.
     *
     * @param  string  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $categoryId)
    {
        $category = Category::with('parent', 'children', 'shop')->find($categoryId);

        if (!$category)
            return $this->errorResponse('Category not found', 'Not Found', 404);

        return $this->successResponse(new CategoryResource($category), 'Category retrieved successfully');
    }

    /**
     * Create a new category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->merge(['shop_id' => session('shop_id')]);
        $request->validate(Category::$rules);

        try {
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'shop_id' => $request->shop_id,
            ]);
            return $this->successResponse(new CategoryResource($category), 'Category created successfully', 201);
        } catch (\Throwable $th) {
            return $this->errorResponse($th, 'Internal Server Error', 500);
        }
    }

    /**
     * Update a category by id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $categoryId)
    {
        $request->merge(['shop_id' => session('shop_id')]);
        $request->validate(Category::$rules);

        $category = Category::find($categoryId);

        if (!$category)
            return $this->errorResponse('Category not found', 'Not Found', 404);

        try {
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse($th, 'Internal Server Error', 500);
        }

        return $this->successResponse(new CategoryResource($category), 'Category updated successfully');
    }

    /**
     * Delete a category by id.
     *
     * @param  string  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category)
            return $this->errorResponse('Category not found', 'Not Found', 404);

        try {
            $category->delete();
        } catch (\Throwable $th) {
            return $this->errorResponse($th, 'Internal Server Error', 500);
        }

        return $this->successResponse(null, 'Category deleted successfully');
    }


    private function collection($categories)
    {
        return $categories->transform(function (Category $category) {
            return (new CategoryResource($category));
        });
    }
}
