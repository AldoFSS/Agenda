<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategoria extends Model
{
    protected $table = 'subcategoria';
    protected $primaryKey = 'id_subcategoria';
    public $timestamps = true;
    protected $fillable = [
        'id_ctg',
        'nombre_subcategoria',
        'descripcion_subcategoria',
        'imagen_subcategoria',
        'estatus'
    ];
}
