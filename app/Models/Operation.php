<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    const ADD_ID = 1;
    const ADD_STOCK_ID = 2;
    const UPDATE_ID = 3;
    const UPDATE_STOCK_ID = 4;
    const HIDE_ID = 5;
    const RESTORE_ID = 6;
    const JOIN_ID = 7;

    protected $table = 'operations';
    protected $fillable = ['title'];
    public $timestamps = false;
}
