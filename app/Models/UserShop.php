<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserShop extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'shop_id',
        'role_id',
    ];

    /**
     * Get the role for the user shop.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
