<?php

namespace App\Http\Controllers;

use App\Models\zonas;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class zonasController
{
    public function MostrarZonas($estatus){
        if ($estatus == 1 || $estatus == 0) {
            $zonas = zonas::where('estatus', $estatus)->get(); // <- Falta get()
        } else {
            $zonas = zonas::all();
        }
        return response(['data' => $zonas]);
    }
    public function crearZona(Request $request)
    {
        $request->validate([
            'nombre_zona'=>'required|string|max:255',
            'descripcion_zona' => 'required|string|max:255'
        ]);
        try{
            $zona =  zonas::create([
                'nombre_zona' => $request->nombre_zona,
                'descripcion_zona' => $request->descripcion_zona
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Zona creada',
                    'message' => 'La zona se registro correctamente'
                ]);
            }
            return redirect()->route('zonas')->with('success','Zona creado correctamente');
        }catch(\Exception $e) {
            Log::error($e);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Ocurrió un error al crear la zona: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors('Ocurrió un error al crear el cliente: ' . $e->getMessage());
        }
       
    }
    public function modificarZona(Request $request, $id)
    {
        $zona = zonas::findOrFail($id);

        $request->validate([
            'nombre_zona'=>'required|string|max:255',
            'descripcion_zona' => 'required|string|max:255'
        ]);
        try{
            $zona->update([
                'nombre_zona' => $request->nombre_zona,
                'descripcion_zona' => $request->descripcion_zona
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Zona actualizada',
                    'message' => 'Los datos de la zona se actualizaron correctamente'
                ]);
            }
            return redirect()->back()->with('success', 'Zona actualizado correctamente');
        }catch (QueryException $e) {
            log::error($e);
             if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Ocurrió un error al editar la Zona: ' . $e->getMessage()
                ], 500);
            }
             return back()->withErrors('Ocurrió un error al editar la Zona: ' . $e->getMessage());
        }
    
    }
    public function obtenerZona($id){
        $zonas = DB::table('zonas')
        ->where('zonas.id_zona', $id)
        ->first();
        if(!$zonas){
            return response()->json(['error' => 'Zona no encontrado'], 404);
        }
        return response()->json($zonas, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function eliminarZona($id)
    {
        $zona = zonas::find($id);
        if (!$zona ) {
            return response()->json(['success' => false, 'title'=>'Error', 'message' => 'Zona no encontrado.']);
        }
        $zona ->estatus = 0;
        $zona ->save();
        return response()->json(['success' => true,'title'=>'Zona desactivado', 'message' => 'La zona fue desactivado correctamente.']);
    }
    public function restaurarZona($id)
    {
        $zona  = zonas::find($id);
        if (!$zona ) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'Zona no encontrado.']);
        }
        $zona ->estatus = 1;
        $zona ->save();
        return response()->json(['success' => true, 'title'=>'Zona desactivada','message' => 'la zona fue activado exitosamente.']);
    }
}
