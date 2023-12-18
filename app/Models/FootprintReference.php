<?php

namespace App\Models;

use App\Traits\Boot\GlobalScopes\WithoutJoinedScope;
use App\Traits\Relations\HistoryMorphMany;
use App\Traits\Scopes\Filters\TrashedFilter;
use App\Traits\SecureDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FootprintReference extends Model
{
    use HasFactory, SoftDeletes, SecureDelete, TrashedFilter, HistoryMorphMany, WithoutJoinedScope;

    protected $perPage = 60;
    protected $table = 'footprints_reference';
    protected $fillable = ['title', 'joined'];

    public function elements1()
    {
        return $this->hasMany(Element::class, 'footprint_ref1_id');
    }

    public function elements2()
    {
        return $this->hasMany(Element::class, 'footprint_ref2_id');
    }

    public function elements3()
    {
        return $this->hasMany(Element::class, 'footprint_ref3_id');
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
