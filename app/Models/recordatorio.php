<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class recordatorio extends Model
{
    protected $table = 'recordatorios';
    protected $primaryKey = 'id_Recordatorio';
    public $timestamps = false;

    protected $fillable = ['id_cita', 'Fecha_Alerta', 'mensaje'];

  
}
