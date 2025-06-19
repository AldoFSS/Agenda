<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriasController
{
    public function MostrarCatalogo($estatus)
    {
        if ($estatus == 1 || $estatus == 0) {
            $categoria = Categoria::where('estatus', $estatus)->get(); 
        } else {
            $categoria = Categoria::all();
        }
        return response(['data' => $categoria]);
    }
    public function CrearCategoria(Request $request)
    {
        $validated = $request->validate([
            'nombre_categoria' => 'required|string|max:255',
            'descripcion'=>'required|string|max:255',
            'imagen_categoria' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        try{
            if ($request->hasFile('imagen_categoria')) {
                $archivo = $request->file('imagen_categoria');
                $nombreArchivo = $archivo->getClientOriginalName();
                $carpeta = public_path('imgcategoria');
                $rutaDestino = $carpeta . '/' . $nombreArchivo;

                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }

                if (!file_exists($rutaDestino)) {
                    $archivo->move($carpeta, $nombreArchivo);
                }

                $rutaImagen = 'imgcategoria/' . $nombreArchivo;
            }
            $categoria = Categoria::create([
                'nombre_categoria' => $validated['nombre_categoria'],
                'descripcion' => $validated['descripcion'],
                'imagen_categoria' => $rutaImagen,
            ]);
            if($request->ajax()){
                return response()->json([
                    'success' => true,
                    'title' => 'Categoria creada',
                    'message' => 'La categoria se registro correctamente'
                ]);
            }
            return redirect()->route('categorias')->with('success', 'Categoria creado correctamente');
        }catch(\Exception $e) {
            log::error($e); 
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title'=>'Error',
                    'message' => 'Ocurrió un error al crear el la categoria: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors('Ocurrió un error al crear el cliente: ' . $e->getMessage());
        }
           
    }
    public function ModificarCategoria(Request $request, $id){

        $categoria = Categoria::findOrFail($id);
        $request->validate([
            'nombre_categoria' => 'required|string|max:20',
            'descripcion_categoria'=>'required|string|max:255',
            'imagen_categoria' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $datosModificados =[
            'nombre_categoria' => $request->nombre_categoria,
            'descripcion' => $request->descripcion_categoria
        ];
        try{
            if ($request->hasFile('imagen_categoria')) {
                $archivo = $request->file('imagen_categoria');
                $nombreArchivo = $archivo->getClientOriginalName();
                $carpeta = public_path('imgcategoria');
                $rutaDestino = $carpeta . '/' . $nombreArchivo;
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }
                if (!file_exists($rutaDestino)) {
                    $archivo->move($carpeta, $nombreArchivo);
                }
                if ($categoria->imagen_subcategoria && $categoria->imagen_subcategoria !== ('imgcategoria/' . $nombreArchivo))
                {
                    $rutaAnterior = public_path($categoria->imagen_producto);
                    if (file_exists($rutaAnterior)) {
                        @unlink($rutaAnterior);
                    }
                }
                $datosModificados['imagen_categoria'] = 'imgcategoria/' . $nombreArchivo;
            }
            $categoria->update($datosModificados);
            if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'title' => 'Categoria actualizada',
                'message' => 'Los datos de la categoria se actualizaron correctamente'
            ]);
        }
        return redirect()->back()->with('success', 'Categoria actualizado correctamente');
        }catch(QueryException $e) {
            log::error($e);
             if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Ocurrió un error al editar la categoria: ' . $e->getMessage()
                ], 500);
            }
             return back()->withErrors('Ocurrió un error al editar el usuario: ' . $e->getMessage());
        }
        
        
    }
    
    public function obtenerCategorias(){
         $Categorias = Categoria::select([
            'id_categoria',
            'nombre_categoria',
        ])
        ->where('estatus','=',1)->get();

        return response()->json(['data' => $Categorias]);
    }
    public function buscarCategoria($id){
        $usuario = DB::table('categoria')
        ->where('categoria.id_categoria', $id)
        ->first();
        if(!$usuario){
            return response()->json(['error' => 'Categoria no encontrado'], 404);
        }
        return response()->json($usuario, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function eliminarCategoria($id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria  ) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'Categoria no encontrado.']);
        }
        $categoria ->estatus = 0;
        $categoria ->save();
        return response()->json(['success' => true, 'title'=>'Categoria desactivado', 'message' => 'La categoria fue desactivado correctamente.']);
    }

    public function restaurarCategoria($id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria  ) {
            return response()->json(['success' => false, 'title'=> 'Error', 'message' => 'Categoria no encontrado.']);
        }
        $categoria ->estatus = 1;
        $categoria ->save();
        return response()->json(['success' => true, 'title'=>'Categoria activada', 'message' => 'La categoria fue activado correctamente.']);
    }
}
