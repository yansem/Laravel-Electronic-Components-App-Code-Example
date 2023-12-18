<?php

namespace App\Traits\Scopes\Filters;

trait TrashedFilter
{
    public function scopeTrashedFilter($query, string $trashed)
    {
        $query
            ->when($trashed === 'with', function ($query) {
                $query->withTrashed();
            })
            ->when($trashed === 'only', function ($query) {
                $query->onlyTrashed();
            });
    }
}