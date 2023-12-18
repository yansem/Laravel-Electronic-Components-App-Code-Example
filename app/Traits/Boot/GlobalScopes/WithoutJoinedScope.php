<?php

namespace App\Traits\Boot\GlobalScopes;

use Illuminate\Database\Eloquent\Builder;

trait WithoutJoinedScope
{
    public static function bootWithoutJoinedScope()
    {
        static::addGlobalScope('joined', function (Builder $builder) {
            $builder->where((new static)->getTable() . '.joined', false);
        });
    }
}