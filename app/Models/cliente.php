<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'id_cliente';
    public $timestamps = true;

    protected $fillable = [
        'nombre_cliente',
        'nombre_comercial',
        'telefono',
        'correo',
        'codigo_postal',
        'colonia',
        'calle',
        'id_estado',
        'id_municipio',
        'rol',
        'imagen',
        'estatus',
    ];
}
