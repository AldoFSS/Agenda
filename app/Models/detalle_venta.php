<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class detalle_venta extends Model
{
    protected $table = 'detalle_Venta';
    protected $primaryKey = 'id_detalleVenta';
    public $timestamps = true;

    protected $fillable = ['id_vnt', 'id_prd', 'cantidad', 'subtotal','IVA','total', 'estatus'];

    
    
}
