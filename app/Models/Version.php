<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Version extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'versions';
    protected $fillable = [
        'version',
        'count',
        'description',
        'pcb_code_id',
        'element_id',
        'url_svn'
    ];


    public function pcb_code()
    {
        return $this->belongsTo(PcbCode::class);
    }

    public function element()
    {
        return $this->belongsTo(Element::class);
    }
}
