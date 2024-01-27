<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const OWNER = 'Owner';
    const STAFF = 'Staff';

    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];
}
