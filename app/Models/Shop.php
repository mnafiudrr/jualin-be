<?php

namespace App\Models;

use App\Traits\ShortIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
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
        'address',
        'owner_id',
    ];

    /**
     * Get the owner for the shop.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the users for the shop.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, UserShop::class, 'shop_id', 'user_id')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public static function createNewShop($request)
    {
        $shop = parent::create([
            'name' => $request['name'],
            'address' => $request['address'],
            'owner_id' => auth()->user()->id,
        ]);

        $roles = Role::where('name', Role::OWNER)->first();
        $shop->users()->attach(auth()->user()->id, ['role_id' => $roles->id]);

        return $shop;
    }
}
