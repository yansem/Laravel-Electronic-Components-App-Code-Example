<?php

namespace App\Traits;

trait SecureDelete
{
    public function secureDelete(string ...$relations): bool
    {
        $hasRelation = false;
        foreach ($relations as $relation) {
            if ($this->$relation()->withTrashed()->count()) {
                $hasRelation = true;
                break;
            }
        }

        if ($hasRelation) {
            return false;
        } else {
            return $this->delete();
        }
    }
}