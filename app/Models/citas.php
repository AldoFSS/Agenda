<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class citas extends Model
{
    protected $table = 'citas';
    protected $primaryKey = 'id_cita';
    public $timestamps = true;

    protected $fillable = [
        'id_cli',
        'id_usr', 
        'fecha_cita', 
        'hora_inicio', 
        'motivo','estatus', 
        'hora_fin',
        'google_event_id'
    ];
    
}
