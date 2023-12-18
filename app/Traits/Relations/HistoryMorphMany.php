<?php

namespace App\Traits\Relations;

use App\Models\History;

trait HistoryMorphMany
{
    public function histories()
    {
        return $this->morphMany(History::class, 'historyable')
            ->join('operations', 'operations.id', '=', 'histories.operation_id')
            ->select(
                'histories.created_at',
                'histories.user_id',
                'operations.title as operation_title',
                'histories.before',
                'histories.after'
            )
            ->orderBy('histories.created_at', 'desc');
    }
}