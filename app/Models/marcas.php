<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class marcas extends Model
{
    protected $table = 'marcas';
    protected $primaryKey = 'id_marca';
    public $timestamps = true;
    protected $fillable = [
        'nombre_marca',
        'descripcion_marca',
        'estatus'
    ];
}
