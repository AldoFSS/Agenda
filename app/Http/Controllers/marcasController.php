<?php

namespace App\Http\Controllers;

use App\Models\marcas;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class marcasController
{
    public function MostrarMarcas($estatus)
    {
        if($estatus == 1 || $estatus == 0){
            $marcas = marcas::where('estatus', $estatus)->get();
        }else{
            $marcas = marcas::all();
        }
        return response(['data' => $marcas]);
    }
    public function crearMarca(Request $request)
    {
        $validated = $request->validate([
            'nombre_marca'=>'required|string|max:255',
            'descripcion_marca'=>'required|string|max:255'
        ]);
        try{
            $marca = marcas::create([
                'nombre_marca' => $validated['nombre_marca'],
                'descripcion_marca' => $validated['descripcion_marca']
            ]);
            if($request->ajax()){
                return response()->json([
                    'success' => true,
                    'title' => 'Marca creada',
                    'message' => 'La marca se registro correctamente'
                ]);
            }
            return redirect()->route('marcas')->with('success','Marca creado correctamente');
        }catch(\Exception $e) {
            log::error($e); 
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Ocurrió un error al crear la marca: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors('Ocurrió un error al crear el cliente: ' . $e->getMessage());
        }

    }
    public function modificarMarca(Request $request, $id)
    {
        $marca = marcas::findOrFail($id);
        $request->validate([
            'nombre_marca'=>'required|string|max:255',
            'descripcion_marca'=>'required|string|max:255'
        ]);

        try{
            $marca->update([
                'nombre_marca'=> $request->nombre_marca,
                'descripcion_marca' => $request->descripcion_marca
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Marca actualizada',
                    'message' => 'Los datos de la marca se actualizaron correctamente'
                ]);
            }
            return redirect()->back()->with('success', 'Marca actualizado correctamente');
        }catch(QueryException $e) {
            log::error($e);
             if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Ocurrió un error al editar la marca: ' . $e->getMessage()
                ], 500);
            }
             return back()->withErrors('Ocurrió un error al editar el usuario: ' . $e->getMessage());
        }

    }
    public function obtenerMarcas(){
         $marcas = marcas::select([
            'id_marca',
            'nombre_marca',
        ])
        ->where('estatus','=',1)->get();

        return response()->json(['data' => $marcas]);
    }
    public function buscarMarca($id){
        $marca = DB::table('marcas')
        ->where('marcas.id_marca', $id)
        ->first();

        if(!$marca){
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return response()->json($marca, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function eliminarMarca($id)
    {
        $marca = marcas::find($id);
        if (!$marca ) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'Marca no encontrada.']);
        }
        $marca ->estatus = 0;
        $marca ->save();
        return response()->json(['success' => true, 'title'=>'Marca desactivada', 'message' => 'La marca fue desactivado correctamente.']);
    }
    public function restaurarMarca($id)
    {
        $marca = marcas::find($id);
        if (!$marca ) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'marca no encontrada.']);
        }
        $marca ->estatus = 1;
        $marca ->save();
        return response()->json(['success' => true, 'title'=>'Marca activada', 'message' => 'La marca fue activado correctamente.']);
    }
}
