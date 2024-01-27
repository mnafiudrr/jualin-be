<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_show_in_transaction' => !!$this->is_show_in_transaction,
            'is_using_stock' => !!$this->is_using_stock,
            'unit_value' => $this->unit_value,
            'unit' => [
                'id' => $this->unit->id,
                'name' => $this->unit->name,
                'symbol' => $this->unit->symbol,
                'quantity' => $this->unit->quantity,
                'description' => $this->unit->description,
            ],
            'stock' => $this->stock ?: 0,
            'stock_min' => $this->stock_min ?: 0,
            'barcode' => $this->barcode,
            'base_price' => $this->base_price,
            'price' => $this->price,
            'discount' => $this->discount,
            'image' => $this->image,
            'rack' => $this->rack,
            'description' => $this->description,
            'shop' => [
                'id' => $this->shop->id,
                'name' => $this->shop->name,
                'address' => $this->shop->address,
            ],
            'categories' => $this->categories->transform(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                ];
            })
        ];
    }
}