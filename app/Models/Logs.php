<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;

    const PER_PAGE = 60;
    protected $table = 'logs';
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function code()
    {
        return $this->belongsTo(LogCodes::class, 'code_id');
    }
}
