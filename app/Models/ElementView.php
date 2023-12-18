<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElementView extends Element
{
    use HasFactory;

/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'elements_view';
}
