<?php

namespace App\Models;

use App\Traits\ShortIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory, ShortIdTrait;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'shop_id',
        'note',
    ];

    public static function getCart()
    {
        $shopId = session('shop_id');
        $userId = auth()->user()->id;
        return parent::with('products')->where('user_id', $userId)->where('shop_id', $shopId)->first();
    }

    /**
     * Get the products for the cart.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_products', 'cart_id', 'product_id')
            ->withPivot('quantity', 'note')
            ->withTimestamps();
    }

    /**
     * Store Product to cart products.
     * If cart not found, create new cart.
     * If product already in cart, update quantity and note.
     * If product not in cart, add product to cart.
     * 
     * @param array $data ['product_id', 'quantity', 'note']
     */
    public static function storeProduct($data)
    {
        $cart = self::getCart();

        $note = isset($data['note']) ? $data['note'] : '';
        
        $product = Product::find($data['product_id']);

        if ($product->is_using_stock) {
            if ($product->stock <= 0)
                return false;
            if ($data['quantity'] > $product->stock)
                $data['quantity'] = $product->stock;
        }

        if (!$cart)
            $cart = self::create([
                'user_id' => auth()->user()->id,
                'shop_id' => session('shop_id'),
            ]);

        if ($cart->products->contains($data['product_id']))
            $cart->products()->updateExistingPivot($data['product_id'], [
                'quantity' => $data['quantity'],
                'note' => $note,
            ]);
        else
            $cart->products()->attach($data['product_id'], [
                'quantity' => $data['quantity'],
                'note' => $note,
            ]);

        $cart->refresh();
        return $cart;
    }

    /**
     * Delete product in cart.
     * if product not in cart, return error.
     * if after delete product, cart is empty, delete cart.
     * 
     * @param array $data ['product_id']
     */
    public static function deleteProduct($data)
    {
        $cart = self::getCart();

        if (!$cart)
            return false;

        $cart->products()->detach($data['product_id']);

        $cart->refresh();

        if ($cart->products->isEmpty()){
            $cart->delete();
            return true;
        }
        return $cart;
    }
}
