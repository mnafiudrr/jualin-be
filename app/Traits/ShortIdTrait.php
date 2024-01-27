<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ShortIdTrait
{
    /**
     * Boot function to generate short id before saving the model
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::random(8);
            $model->keyType = 'string';
        });
    }

    /**
     * Generate a unique short id
     *
     * @return string
     */
    public static function generateShortId()
    {
        $id = Str::random(8);
        if (self::checkIdExist($id))
            return self::generateShortId();
        return $id;
    }

    /**
     * Get the model by short id
     *
     * @param string $id
     * @return Model
     */
    private static function checkIdExist($id)
    {
        $model = self::where('id', $id)->first();

        if ($model)
            return true;

        return false;
    }
}