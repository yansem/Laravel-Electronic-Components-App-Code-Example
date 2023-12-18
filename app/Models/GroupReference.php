<?php

namespace App\Models;

use App\Interfaces\SelectWithRelations;
use App\Interfaces\WithoutJoined;
use App\Traits\Boot\GlobalScopes\WithoutJoinedScope;
use App\Traits\Relations\HistoryMorphMany;
use App\Traits\Scopes\Filters\TrashedFilter;
use App\Traits\SecureDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupReference extends Model implements SelectWithRelations, WithoutJoined
{
    use HasFactory, SoftDeletes, SecureDelete, TrashedFilter, HistoryMorphMany, WithoutJoinedScope;

    protected $perPage = 60;
    protected $table = 'groups_reference';
    protected $fillable = ['component_ref_id', 'title', 'joined'];

    public function categories()
    {
        return $this->hasMany(CategoryReference::class, 'group_ref_id');
    }

    public function component()
    {
        return $this->belongsTo(ComponentReference::class, 'component_ref_id');
    }

    public function elements()
    {
        return $this->hasMany(Element::class, 'group_ref_id');
    }

    public function scopeSelectWithRelations($query, array $ids = null)
    {
        return $query
            ->leftJoin('components_reference', 'components_reference.id', '=', 'groups_reference.component_ref_id')
            ->select(
                'groups_reference.id',
                'groups_reference.title',
                'components_reference.title as component_title',
                'groups_reference.component_ref_id',
                'groups_reference.created_at',
                'groups_reference.updated_at',
                'groups_reference.deleted_at'
            )
            ->when($ids, function ($query) use ($ids) {
                $query->whereIn('groups_reference.id', $ids);
            });
    }

    public function scopeWithFilters($query, array $params)
    {
        return $query
            ->when(isset($params['id']), function ($query) use ($params) {
                return $query->where('groups_reference.id', '=', $params['id']);
            })
            ->when(isset($params['title']), function ($query) use ($params) {
                $query->where('groups_reference.title', 'LIKE', '%' . $params['title'] . '%');
            })
            ->when(isset($params['component']), function ($query) use ($params) {
                $query->where('groups_reference.component_ref_id', $params['component']);
            })
            ->when(isset($params['trashed']), function ($query) use ($params) {
                $query->trashedFilter($params['trashed']);
            });
    }
}
