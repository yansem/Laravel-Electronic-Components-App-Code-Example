<?php

namespace App\Models;

use App\Interfaces\SelectWithRelations;
use App\Traits\Relations\HistoryMorphMany;
use App\Traits\Scopes\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Element extends Model implements SelectWithRelations
{
    use HasFactory, SoftDeletes, TrashedFilter, HistoryMorphMany;

    protected $perPage = 60;
    protected $table = 'elements';

    protected $fillable = [
        'component_ref_id',
        'group_ref_id',
        'category_ref_id',
        'part_number',
        'component_name',
        'part_status_id',
        'library_ref_id',
        'temp_range_id',
        'manufacturer_id',
        'footprint_ref1_id',
        'footprint_ref2_id',
        'footprint_ref3_id',
        'description',
        'help_url',
        'comment',
        'stock_barcode',
        'stock_id',
        'stock_count',
        'stock_link',
        'stock_title',
        'stock_count_type',
        'stock_part_count_type',
        'stock_part_count',
        'part_count',
        'count'
    ];


    public function component()
    {
        return $this->belongsTo(ComponentReference::class, 'component_ref_id');
    }

    public function group()
    {
        return $this->belongsTo(GroupReference::class, 'group_ref_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoryReference::class, 'category_ref_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(ManufacturerReference::class, 'manufacturer_id');
    }

    public function partStatus()
    {
        return $this->belongsTo(PartStatusReference::class, 'part_status_id');
    }

    public function tempRange()
    {
        return $this->belongsTo(TempRangeReference::class, 'temp_range_id');
    }

    public function libraryRef()
    {
        return $this->belongsTo(LibraryRefReference::class, 'library_ref_id');
    }

    public function footprintRef1()
    {
        return $this->belongsTo(FootprintReference::class, 'footprint_ref1_id');
    }

    public function footprintRef2()
    {
        return $this->belongsTo(FootprintReference::class, 'footprint_ref2_id');
    }

    public function footprintRef3()
    {
        return $this->belongsTo(FootprintReference::class, 'footprint_ref3_id');
    }

    public function scopeSelectWithRelations($query, array $ids = null)
    {
        return $query
            ->leftJoin('components_reference', 'components_reference.id', '=', 'elements.component_ref_id')
            ->leftJoin('groups_reference', 'groups_reference.id', '=', 'elements.group_ref_id')
            ->leftJoin('categories_reference', 'categories_reference.id', '=', 'elements.category_ref_id')
            ->leftJoin('part_statuses_reference', 'part_statuses_reference.id', '=', 'elements.part_status_id')
            ->leftJoin('manufacturers_reference', 'manufacturers_reference.id', '=', 'elements.manufacturer_id')
            ->leftJoin('library_ref_reference', 'library_ref_reference.id', '=', 'elements.library_ref_id')
            ->leftJoin('temp_ranges_reference', 'temp_ranges_reference.id', '=', 'elements.temp_range_id')
            ->leftJoin('footprints_reference as fp1', 'fp1.id', '=', 'elements.footprint_ref1_id')
            ->leftJoin('footprints_reference as fp2', 'fp2.id', '=', 'elements.footprint_ref2_id')
            ->leftJoin('footprints_reference as fp3', 'fp3.id', '=', 'elements.footprint_ref3_id')
            ->select(
                'elements.id',
                'components_reference.title as component_title',
                'elements.component_ref_id',
                'groups_reference.title as group_title',
                'elements.group_ref_id',
                'categories_reference.title as category_title',
                'elements.category_ref_id',
                'manufacturers_reference.title as manufacturer_title',
                'elements.manufacturer_id',
                'part_statuses_reference.title as part_status_title',
                'elements.part_status_id',
                'elements.part_number',
                'elements.component_name',
                'library_ref_reference.title as library_ref_title',
                'elements.library_ref_id',
                'fp1.title as footprint1_title',
                'elements.footprint_ref1_id',
                'fp2.title as footprint2_title',
                'elements.footprint_ref2_id',
                'fp3.title as footprint3_title',
                'elements.footprint_ref3_id',
                'temp_ranges_reference.description as temp_range_description',
                'elements.temp_range_id',
                'elements.comment',
                'elements.description',
                'elements.help_url',
                'elements.part_count',
                'elements.stock_barcode',
                'elements.count',
                'elements.stock_title',
                'elements.stock_id',
                'elements.stock_count',
                'elements.stock_count_type',
                'elements.stock_part_count',
                'elements.stock_part_count_type',
                'elements.stock_link',
                'elements.created_at',
                'elements.updated_at',
                'elements.deleted_at'
            )
            ->when($ids, function ($query) use ($ids) {
                $query->whereIn('elements.id', $ids);
            });
    }

    public function scopeWithFilters($query, array $params)
    {
        return $query
            ->when(isset($params['id']), function ($query) use ($params) {
                return $query->where('elements.id', '=', $params['id']);
            })
            ->when(isset($params['component']), function ($query) use ($params) {
                $query->where('elements.component_ref_id', '=', $params['component']);
            })
            ->when(isset($params['group']), function ($query) use ($params) {
                $query->where('elements.group_ref_id', '=', $params['group']);
            })
            ->when(isset($params['category']), function ($query) use ($params) {
                $query->where('elements.category_ref_id', '=', $params['category']);
            })
            ->when(isset($params['manufacturer']), function ($query) use ($params) {
                $query->where('elements.manufacturer_id', '=', $params['manufacturer']);
            })
            ->when(isset($params['temp_range']), function ($query) use ($params) {
                $query->where('elements.temp_range_id', '=', $params['temp_range']);
            })
            ->when(isset($params['library']), function ($query) use ($params) {
                $query->where('elements.library_ref_id', '=', $params['library']);
            })
            ->when(isset($params['footprint1']), function ($query) use ($params) {
                $query->where('elements.footprint_ref1_id', '=', $params['footprint1']);
            })
            ->when(isset($params['footprint2']), function ($query) use ($params) {
                $query->where('elements.footprint_ref2_id', '=', $params['footprint2']);
            })
            ->when(isset($params['footprint3']), function ($query) use ($params) {
                $query->where('elements.footprint_ref3_id', '=', $params['footprint3']);
            })
            ->when(isset($params['stock_title']), function ($query) use ($params) {
                $query->where(DB::raw('lower(elements.stock_title)'), 'LIKE', "%" . mb_strtolower($params['stock_title']) . "%");
            })
            ->when(isset($params['part_number']), function ($query) use ($params) {
                $query->where(DB::raw('lower(elements.part_number)'), 'LIKE', "%" . mb_strtolower($params['part_number']) . "%");
            })
            ->when(isset($params['component_name']), function ($query) use ($params) {
                $query->where(DB::raw('lower(elements.component_name)'), 'LIKE', "%" . mb_strtolower($params['component_name']) . "%");
            })
            ->when(isset($params['comment']), function ($query) use ($params) {
                $query->where(DB::raw('lower(elements.comment)'), 'LIKE', "%" . mb_strtolower($params['comment']) . "%");
            })
            ->when(isset($params['part_status']), function ($query) use ($params) {
                $query->where('elements.part_status_id', '=', $params['part_status']);
            })
            ->when(isset($params['trashed']), function ($query) use ($params) {
                $query->trashedFilter($params['trashed']);
            });
    }
}
