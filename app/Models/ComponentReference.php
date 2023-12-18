<?php

namespace App\Models;

use App\Interfaces\WithoutJoined;
use App\Traits\Boot\GlobalScopes\WithoutJoinedScope;
use App\Traits\Relations\HistoryMorphMany;
use App\Traits\Scopes\Filters\TrashedFilter;
use App\Traits\SecureDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentReference extends Model implements WithoutJoined
{
    use HasFactory, SoftDeletes, SecureDelete, TrashedFilter, HistoryMorphMany, WithoutJoinedScope;

    protected $perPage = 60;
    protected $fillable = ['title', 'joined'];
    protected $table = 'components_reference';

    public function groups()
    {
        return $this->hasMany(GroupReference::class, 'component_ref_id');
    }

    public function elements()
    {
        return $this->hasMany(Element::class, 'component_ref_id');
    }

    public function categories()
    {
        return $this->hasMany(CategoryReference::class, 'component_ref_id');
    }

    public function scopeWithFilters($query, array $params)
    {
        return $query
            ->when(isset($params['id']), function ($query) use ($params) {
                return $query->where('id', '=', $params['id']);
            })
            ->when(isset($params['title']), function ($query) use ($params) {
                $query->where('title', 'LIKE', '%' . $params['title'] . '%');
            })
            ->when(isset($params['trashed']), function ($query) use ($params) {
                $query->trashedFilter($params['trashed']);
            });
    }
}
