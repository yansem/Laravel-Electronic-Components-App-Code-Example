<?php

namespace App\Models;

use App\Traits\Boot\GlobalScopes\WithoutJoinedScope;
use App\Traits\Relations\HistoryMorphMany;
use App\Traits\Scopes\Filters\TrashedFilter;
use App\Traits\SecureDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempRangeReference extends Model
{
    use HasFactory, SoftDeletes, SecureDelete, TrashedFilter, HistoryMorphMany, WithoutJoinedScope;

    protected $perPage = 60;
    protected $table = 'temp_ranges_reference';
    protected $fillable = [
        'title',
        'min',
        'max',
        'description',
        'joined'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function elements()
    {
        return $this->hasMany(Element::class, 'temp_range_id');
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
            ->when(isset($params['description']), function ($query) use ($params) {
                $query->where('description', 'LIKE', '%' . $params['description'] . '%');
            })
            ->when(isset($params['min']), function ($query) use ($params) {
                $query->where('min', 'LIKE', '%' . $params['min'] . '%');
            })
            ->when(isset($params['max']), function ($query) use ($params) {
                $query->where('max', 'LIKE', '%' . $params['max'] . '%');
            })
            ->when(isset($params['trashed']), function ($query) use ($params) {
                $query->trashedFilter($params['trashed']);
            });
    }
}
