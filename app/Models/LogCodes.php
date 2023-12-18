<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LogCodes extends Model
{
    use HasFactory, SoftDeletes;

    const ELEMENTS_ID = 1;
    const COMPONENTS_ID = 2;
    const GROUPS_ID = 3;
    const CATEGORIES_ID = 4;
    const MANUFACTURERS_ID = 5;
    const TEMP_RANGES_ID = 6;
    const PART_STATUSES_ID = 7;
    const LIBRARY_REF_ID = 8;
    const FOOTPRINTS_ID = 9;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_codes';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function logs()
    {
        return $this->hasMany(Logs::class, 'code_id');
    }
}
