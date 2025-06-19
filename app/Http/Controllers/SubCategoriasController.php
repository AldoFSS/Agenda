<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\SubCategoria;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class SubCategoriasController
{
    public function MostrarSubCategorias($estatus)
    {
        $sql = SubCategoria::select([
            'subcategoria.*',
            'categoria.nombre_categoria as Categoria'
        ])
        -> join('categoria', 'subcategoria.id_ctg', '=','categoria.id_categoria');

        if($estatus == '0'|| $estatus == '1'){
            $sql->where('subcategoria.estatus',$estatus);
        }
        $categorias = $sql->get();
        return response(['data' => $categorias]);
    }
    public function CrearSubCategoria(Request $request)
    {
        $validated = $request->validate([
            'nombre_subcategoria' => 'required|string|max:20',
            'descripcion_subcategoria' =>'required|string|max:255',
            'id_categoria' =>'required|exists:categoria,id_categoria' ,
            'imagen_subcategoria' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        try{
            if ($request->hasFile('imagen_subcategoria')) {
                $archivo = $request->file('imagen_subcategoria');
                $nombreArchivo = $archivo->getClientOriginalName();
                $carpeta = public_path('imgsubcategoria');
                $rutaDestino = $carpeta . '/' . $nombreArchivo;

                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }

                if (!file_exists($rutaDestino)) {
                    $archivo->move($carpeta, $nombreArchivo);
                }

                $rutaImagen = 'imgsubcategoria/' . $nombreArchivo;
            }
            $subcategoria = SubCategoria::create([
                'nombre_subcategoria' => $validated['nombre_subcategoria'],
                'descripcion_subcategoria' => $validated['descripcion_subcategoria'],
                'id_ctg' => $validated['id_categoria'],
                'imagen_subcategoria' => $rutaImagen,
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Subcategoria creado',
                    'message' => 'La subcategoria se registro correctamente'
                ]);
            }
            return redirect()->route('subcategorias')->with('success', 'SubCategoria creado correctamente');
        }catch(\Exception $e) {
            log::error($e);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'title' => 'Error',
                'message' => 'Ocurrió un error al crear la subcategoria: ' . $e->getMessage()
            ], 500);
        }

        return back()->withErrors('Ocurrió un error al crear la subcategoria: ' . $e->getMessage());
    }
           
    }
    public function ModificarSubCategoria(Request $request, $id){

        $subcategoria = SubCategoria::findOrFail($id);

        $request->validate([
            'nombre_subcategoria' => 'required|string|max:20',
            'descripcion_subcategoria'=>'required|string|max:255',
            'id_categoria' =>'required|exists:categoria,id_categoria' ,
            'imagen_subcategoria' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        try{
            $datosModificados =[
                'nombre_subcategoria' => $request->nombre_subcategoria,
                'descripcion_subcategoria' => $request->descripcion_subcategoria,
                'id_ctg' => $request->id_categoria
            ];
            if ($request->hasFile('imagen_subcategoria')) {
                $archivo = $request->file('imagen_subcategoria');
                $nombreArchivo = $archivo->getClientOriginalName();
                $carpeta = public_path('imgsubcategoria');
                $rutaDestino = $carpeta . '/' . $nombreArchivo;
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }
                if (!file_exists($rutaDestino)) {
                    $archivo->move($carpeta, $nombreArchivo);
                }
                if ($subcategoria->imagen_subcategoria && $subcategoria->imagen_subcategoria !== ('imgsubcategoria/' . $nombreArchivo))
                {
                    $rutaAnterior = public_path($subcategoria->imagen_producto);
                    if (file_exists($rutaAnterior)) {
                        @unlink($rutaAnterior);
                    }
                }
                $datosModificados['imagen_subcategoria'] = 'imgsubcategoria/' . $nombreArchivo;
            }
            $subcategoria->update($datosModificados);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Subcategoria actualizada',
                    'message' => 'Los datos de la subcategoria se actualizaron correctamente'
                ]);
            }
            return redirect()->back()->with('success', 'SubCategoria actualizado correctamente');
        }catch(\Exception $e){
            log::error($e);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Ocurrió un error al editar la subcategoria: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors('Ocurrió un error al editar la subcategoria: ' . $e->getMessage());
        }
        
    }
    public function eliminarSubcategoria($id)
    {
        $Subcategoria  =  SubCategoria::find($id);
        if (!$Subcategoria ) {
            return response()->json(['success' => false,'title' => 'Error', 'message' => 'Subcategoria no encontrado.']);
        }
        $Subcategoria ->estatus = 0;
        $Subcategoria ->save();
        return response()->json(['success' => true, 'title'=> 'Subcategoria desactivado', 'message' => 'La subcategoria fue desactivado correctamente.']);
    }

    public function restaurarSubcategoria($id)
    {
        $Subcategoria  = SubCategoria::find($id);
        if (!$Subcategoria ) {
            return response()->json(['success' => false, 'title'=>'Error', 'message' => 'Subcategoria no encontrado.']);
        }
        $Subcategoria ->estatus = 1;
        $Subcategoria ->save();
        return response()->json(['success' => true,'title'=>'Subcategoria Activado', 'message' => 'Subcategoria fue activado correctamente.']);
    }
    public function obtenerSubcategorias($idcategoria){
         $subcategoria = SubCategoria::select([
            'id_subcategoria',
            'nombre_subcategoria',
        ])
        ->where('estatus','=',1)
        ->where('id_ctg',$idcategoria)
        ->get();

        return response()->json(['data' => $subcategoria]);
    }
    public function buscarSubcategoria($id)
    {
        $subcategoria = DB::table('subcategoria')
        ->join('categoria', 'subcategoria.id_ctg', '=','categoria.id_categoria')
        ->select(
            'subcategoria.*',
        )
        ->where('subcategoria.id_subcategoria', $id)
        ->first();
        if(!$subcategoria){
             return response()->json(['error' => 'Subcategoria no encontrado'], 404);
        }
        return response()->json($subcategoria, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
