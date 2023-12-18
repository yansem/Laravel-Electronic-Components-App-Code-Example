<?php

namespace App\Models;

use App\Interfaces\SelectWithRelations;
use App\Interfaces\WithoutJoined;
use App\Traits\Boot\GlobalScopes\WithoutJoinedScope;
use App\Traits\Relations\HistoryMorphMany;
use App\Traits\Scopes\Filters\TrashedFilter;
use App\Traits\SecureDelete;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryReference extends Model implements SelectWithRelations, WithoutJoined
{
    use HasFactory, SoftDeletes, SecureDelete, TrashedFilter, HistoryMorphMany, WithoutJoinedScope;

    protected $perPage = 60;
    protected $table = 'categories_reference';
    protected $fillable = [
        'component_ref_id',
        'group_ref_id',
        'title',
        'joined'
    ];

    public function group()
    {
        return $this->belongsTo(GroupReference::class, 'group_ref_id');
    }

    public function component()
    {
        return $this->belongsTo(ComponentReference::class, 'component_ref_id');
    }

    public function elements()
    {
        return $this->hasMany(Element::class, 'category_ref_id');
    }

    public function scopeSelectWithRelations($query, array $ids = null)
    {
        return $query
            ->leftJoin('components_reference', 'components_reference.id', '=', 'categories_reference.component_ref_id')
            ->leftJoin('groups_reference', 'groups_reference.id', '=', 'categories_reference.group_ref_id')
            ->select(
                'categories_reference.id',
                'categories_reference.title',
                'categories_reference.component_ref_id',
                'components_reference.title as component_title',
                'categories_reference.group_ref_id',
                'groups_reference.title as group_title',
                'categories_reference.created_at',
                'categories_reference.updated_at',
                'categories_reference.deleted_at'
            )
            ->when($ids, function ($query) use ($ids) {
                $query->whereIn('categories_reference.id', $ids);
            });
    }

    public function scopeWithFilters($query, array $params)
    {
        return $query
            ->when(isset($params['id']), function ($query) use ($params) {
                return $query->where('categories_reference.id', '=', $params['id']);
            })
            ->when(isset($params['title']), function ($query) use ($params) {
                $query->where('categories_reference.title', 'LIKE', '%' . $params['title'] . '%');
            })
            ->when(isset($params['component']), function ($query) use ($params) {
                $query->where('categories_reference.component_ref_id', $params['component']);
            })
            ->when(isset($params['group']), function ($query) use ($params) {
                $query->where('categories_reference.group_ref_id', $params['group']);
            })
            ->when(isset($params['trashed']), function ($query) use ($params) {
                $query->trashedFilter($params['trashed']);
            });
    }
}
