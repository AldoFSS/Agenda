<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class municipios extends Model
{
    
    protected $table = 'municipios';
    protected $primaryKey = 'id_municipio';
    public $timestamps = true;
    protected $fillable = [
        'municipio',
        'id_estd',
        'estatus'
    ];
}
