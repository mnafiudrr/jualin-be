<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    use HttpResponses;

    /**
     * Get all shops.
     */
    public function index()
    {
        $shops = auth()->user()->shops;

        if ($shops->isEmpty())
            return $this->errorResponse('No shops found', 'Not Found', 404);

        $data = $shops->transform(function (Shop $shop) {
            return (new ShopResource($shop));
        });

        return $this->successResponse($data, 'Shops retrieved successfully');
    }

    /**
     * Create a new shop.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
        ]);

        DB::beginTransaction();
        try {

            $shop = Shop::createNewShop($request->only('name', 'address'));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th, 'Internal Server Error', 500);
        }

        return $this->successResponse(new ShopResource($shop), 'Shop created successfully', 201);
    }

    /**
     * Get a shop user have access to.
     */
    public function show(string $shop_id)
    {
        // $shop = auth()->user()->shops()->where('id', $shop_id)->first();
        $shop = Shop::where('id', $shop_id)
                    ->whereHas('users', function ($query) {
                        $query->where('user_id', auth()->user()->id);
                    })
                    ->first();

        if (!$shop)
            return $this->errorResponse('Shop not found', 'Not Found', 404);

        return $this->successResponse(new ShopResource($shop), 'Shop retrieved successfully');
    }

    /**
     * Update a shop.
     */
    public function update(Request $request, string $shop_id)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
        ]);

        $shop = Shop::where('id', $shop_id)
                    ->whereHas('users', function ($query) {
                        $query->where('user_id', auth()->user()->id);
                    })
                    ->first();

        if (!$shop)
            return $this->errorResponse('Shop not found', 'Not Found', 404);

        try {
            $shop->update([
                'name' => $request->name,
                'address' => $request->address,
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse($th, 'Internal Server Error', 500);
        }

        return $this->successResponse(new ShopResource($shop), 'Shop updated successfully');
    }
}
