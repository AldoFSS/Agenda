<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class productos extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = true;
    protected $fillable = [
        'nombre_producto',
        'stock',
        'precio_unitario',
        'precio_venta',
        'IVA_producto',
        'id_catg',
        'id_subcatg',
        'id_marc',
        'id_proveedor',
        'imagen_producto',
        'codigo',
        'estatus'
    ];
}
