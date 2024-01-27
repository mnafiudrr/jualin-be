<?php

namespace App\Models;

use App\Traits\ShortIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
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
        'description',
        'parent_id',
        'shop_id',
    ];

    /**
     * Category rules
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'description' => 'string|max:255',
        'parent_id' => 'exists:categories,id',
        'shop_id' => 'required|exists:shops,id',
    ];

    /**
     * Get the Category by id.
     */
    public static function find($id, $columns = ['*'])
    {
        $shopId = session('shop_id');
        return parent::where('id', $id)->where('shop_id', $shopId)->first($columns);
    }

    /**
     * Get the parent for the category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the children for the category.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the shop for the category.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, ProductCategory::class, 'category_id', 'product_id')
            ->withTimestamps();
    }

    /**
     * Search categories by name.
     */
    public function scopeSearch($query, $search)
    {
        if ($search)
            return $query->where('shop_id', session('shop_id'))
                ->where('name', 'like', '%' . $search . '%');

        return $query;
    }
}
