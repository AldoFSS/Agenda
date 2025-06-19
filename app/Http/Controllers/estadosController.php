<?php

namespace App\Http\Controllers;

use App\Models\estados;
use Illuminate\Http\Request;

class estadosController
{
    public function MostrarEstados($estatus){
        if($estatus == 1 || $estatus == 0){
             $estados = estados::where('estatus', $estatus)->get();
        }else{
            $estados = estados::all();
        }
         return response(['data' => $estados]);
    }
    public function BuscarEstados(){
        $estados = estados::select([
            'id_estado',
            'nombre_estado'
        ])
        ->where('estatus','=', '1')->get();

        return response()->json(['data' => $estados]);
    }
    public function eliminarEstado($id)
    {
        $estado = estados::find($id);
        if (!$estado  ) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'estado no encontrado.']);
        }
        $estado ->estatus = 0;
        $estado ->save();
        return response()->json(['success' => true,'title'=>'Estado desactivado', 'message' => 'El estado fue desactivado exitosamente.']);
    }

    public function restaurarEstado($id)
    {
        $estado = estados::find($id);
        if (!$estado  ) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'estado no encontrado.']);
        }
        $estado ->estatus = 1;
        $estado ->save();
        return response()->json(['success' => true,'title'=>'Producto Activado', 'message' => 'El estado fue activado correctamente.']);
    }
}
