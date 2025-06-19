<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class estados extends Model
{
    
    protected $table = 'estados';
    protected $primaryKey = 'id_estado';
    public $timestamps = true;
    protected $fillable = [
        'nombre_estado',
        'estatus'
    ];
}
