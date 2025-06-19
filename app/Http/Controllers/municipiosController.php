<?php

namespace App\Http\Controllers;
use App\Models\municipios;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class municipiosController
{
    public function MostrarMunicipios($estatus){
        $sql = municipios::select([
            'municipios.*',
            'estados.nombre_estado as Estado'
        ])
        ->join('estados', 'municipios.id_estd', '=', 'estados.id_estado');
        if($estatus == '0' || $estatus == '1'){
            $sql->where('municipios.estatus', $estatus);
        }
        $municipios = $sql->get();

        return response(['data' => $municipios]);
    }
    public function CrearMunicipio(Request $request)
    {
        $request->validate([
            'nombre_municipio'=> 'required|string|max:255',
            'id_estado' =>  'required|exists:estados,id_estado'
        ]);
        try{

            $municipio = municipios::create([
                'municipio' => $request->nombre_municipio,
                'id_estd' => $request->id_estado
            ]);
                if ($request->ajax()) {
                    return response()->json([
                    'success' => true,
                    'title' => 'Municipio creado',
                    'message' => 'El municipio se registro correctamente'
                ]);
            }
            return redirect()->route('municipios')->with('success','Municipio creado correctamente');
        }catch (\Exception $e) {
            log::error($e); 
            if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'title' => 'Error',
                'message' => 'Ocurrió un error al crear el municipio: ' . $e->getMessage()
            ], 500);
        }

        return back()->withErrors('Ocurrió un error al crear el cliente: ' . $e->getMessage());
    }
        
    }
    public function EditarMunicipio(Request $request, $id)
    {
        $municipio = municipios::findOrFail($id);
        $request->validate([
            'municipio'=> 'required|string|max:255',
            'id_estado' =>  'required|exists:estados,id_estado'
        ]);
        try{
            $municipio->update([
                'municipio' => $request->municipio,
                'id_estd' => $request->id_estado
            ]);
            if($request->ajax()){
                return response()->json([
                    'success' => true,
                    'title' => 'Municipio actualizado',
                    'message' => 'Los datos del municipio se actualizaron correctamente'
                ]);
            }
            return redirect()->back()->with('success','Municipio actualizado correctamente');
        }catch(\Exception $e){
            log::error($e);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Ocurrió un error al editar el municipio: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors('Ocurrió un error al editar el cliente: ' . $e->getMessage());
        }
        
    }
    public function BuscarMunicipio($id_estado)
    {
        $municipios = municipios::select('id_municipio', 'municipio as nombre_municipio') // alias para usar en JS
        ->where('id_estd', $id_estado) // corregido
        ->get();

        return response()->json($municipios);
    }
    public function obtenerMunicipio($id)
    {
        $municipio = DB::table('municipios')
            ->join('estados', 'municipios.id_estd', '=', 'estados.id_estado')
            ->where('municipios.id_municipio', $id)
            ->first();
        if (!$municipio) {
            return response()->json(['error' => 'Municipio no encontrado'], 404);
        }
        return response()->json($municipio, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function eliminarMunicipio($id)
    {
        $municipio  =  municipios::find($id);
        if (!$municipio ) {
            return response()->json(['success' => false, 'title'=>'Error', 'message' => 'municipio no encontrado.']);
        }
        $municipio ->estatus = 0;
        $municipio ->save();
        return response()->json(['success' => true, 'title'=> 'Municipio desactivado', 'message' => 'El municipio fue desactivado correctamente.']);
    }
    public function restaurarMunicipio($id)
    {
        $municipio  = municipios::find($id);
        if (!$municipio ) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'municipio no encontrado.']);
        }
        $municipio ->estatus = 1;
        $municipio ->save();
        return response()->json(['success' => true, 'title'=>'Municipio Activado', 'message' => 'municipio fue activado correctamente.']);
    }
}
