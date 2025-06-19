<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\user as Authenticatable;
class usuarios extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = true;

    protected $fillable = [
        'nombre_usuario',
        'telefono',
        'correo',
        'contraseña',
        'rol',
        'imagen',
        'estatus'
    ];
    protected $hidden = [
        'contraseña',
    ];
    public function getAuthPassword(){
        return $this->contraseña;
    }
}