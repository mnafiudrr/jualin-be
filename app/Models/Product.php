<?php

namespace App\Models;

use App\Traits\ShortIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, ShortIdTrait;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_show_in_transaction',
        'is_using_stock',
        'unit_value',
        'unit_id',
        'stock',
        'stock_min',
        'barcode',
        'base_price',
        'price',
        'discount',
        'image',
        'rack',
        'description',
        'shop_id',
    ];

    /**
     * Product rules
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'is_show_in_transaction' => 'required|boolean',
        'is_using_stock' => 'required|boolean',
        'unit_value' => 'required|numeric',
        'unit_id' => 'required|exists:units,id',
        'stock' => 'required_if:is_using_stock,true|numeric',
        'stock_min' => 'numeric',
        'barcode' => 'string|max:255',
        'base_price' => 'required|numeric',
        'price' => 'required|numeric',
        'discount' => 'numeric',
        'image' => 'string|max:255',
        'rack' => 'string|max:255',
        'description' => 'string|max:255',
        'shop_id' => 'required|exists:shops,id',
    ];

    /**
     * Get the product by id.
     */
    public static function find($id, $columns = ['*'])
    {
        $shopId = session('shop_id');
        return parent::where('id', $id)->where('shop_id', $shopId)->first($columns);
    }

    /**
     * Get the unit for the product.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the shop for the product.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Get the categories for the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, ProductCategory::class, 'product_id', 'category_id')
            ->withTimestamps();
    }

    /**
     * Search product by name or barcode
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('shop_id', session('shop_id'))
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%");
            });
    }
}
