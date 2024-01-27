<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'note' => $this->note,
            'products' => $this->products->transform(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $product->pivot->quantity,
                    'is_using_stock' => !!$product->is_using_stock,
                    'stock' => $product->is_using_stock ? $product->stock : null,
                    'price' => $product->price,
                    'unit' => [
                        'name' => $product->unit->name,
                        'symbol' => $product->unit->symbol,
                        'quantity' => $product->unit->quantity,
                        'description' => $product->unit->description,
                    ],
                ];
            })
        ];
    }
}