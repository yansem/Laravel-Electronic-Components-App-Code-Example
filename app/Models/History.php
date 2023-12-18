<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $perPage = 60;
    protected $table = 'histories';
    protected $fillable = [
        'operation_id',
        'log_code_id',
        'before',
        'after',
        'user_id',
        'historyable_id',
        'historyable_type',
        'created_at'
    ];
    const UPDATED_AT = null;

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function historyable()
    {
        return $this->morphTo()->withTrashed()->withoutGlobalScope('joined');
    }

    public function scopeWithFilters($query, array $params)
    {
        return $query
            ->when(isset($params['date']), function ($query) use ($params) {
                return $query->whereDate('histories.created_at', '=', Carbon::parse($params['date'])->format('Y-m-d'));
            })
            ->when(isset($params['entity']), function ($query) use ($params) {
                $query->where('histories.log_code_id', $params['entity']);
            })
            ->when(isset($params['user']), function ($query) use ($params) {
                $query->where('histories.user_id', $params['user']);
            })
            ->when(isset($params['designation']), function ($query) use ($params) {
                $query->where(function ($query) use ($params) {
                    $query->whereHasMorph('historyable', [
                        ComponentReference::class,
                        GroupReference::class,
                        CategoryReference::class,
                        ManufacturerReference::class,
                        PartStatusReference::class,
                        TempRangeReference::class,
                        LibraryRefReference::class,
                        FootprintReference::class
                    ], function ($query) use ($params) {
                        $query->where('title', 'LIKE', '%' . $params['designation'] . '%')->withTrashed();
                    })
                        ->orWhereHasMorph('historyable', Element::class, function ($query) use ($params) {
                            $query->whereId((int)$params['designation'])->withTrashed();
                        });
                });

            })
            ->when(isset($params['operation']), function ($query) use ($params) {
                $query->where('histories.operation_id', $params['operation']);
            });
    }
}
