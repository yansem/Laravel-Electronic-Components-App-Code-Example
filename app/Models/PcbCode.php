<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PcbCode extends Model
{
    use HasFactory, SoftDeletes;

    public const URL_SVN = 'https://svn.orlan.in/svn/main/pcb_projects/';
    public const URL_WIKI = 'https://wiki.orlan.in/doku.php/брэо:пп:коды_плат:';

    protected $table = 'pcb_codes';
    protected $fillable = [
        'code',
        'description',
        'url_svn',
        'url_wiki'
    ];

    public function versions()
    {
        return $this->hasMany(Version::class)->withTrashed();
    }
}
