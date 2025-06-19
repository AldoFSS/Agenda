<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class zonas extends Model
{
    
    protected $table = 'zonas';
    protected $primaryKey = 'id_zona';
    public $timestamps = true;
    protected $fillable = [
        'nombre_zona',
        'descripcion_zona',
        'estatus'
    ];
}
