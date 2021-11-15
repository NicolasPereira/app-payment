<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait hasUuid
{
    public static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }
}
