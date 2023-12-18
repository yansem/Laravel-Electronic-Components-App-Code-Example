<?php

namespace App\Interfaces;

interface SelectWithRelations
{
    public function scopeSelectWithRelations($query, array $ids);
}