<?php

namespace App\Http\Controllers;
use App\Models\productos;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductosController 
{
    public function MostrarProductos($estatus)
    {
        $sql = productos::select([
            'productos.*',
            'categoria.nombre_Categoria as categoria',
            'subcategoria.nombre_subcategoria as subcategoria',
            'marcas.nombre_marca as marca',
            'cliente.nombre_comercial as proveedor',
            'cliente.id_cliente'
        ])
        ->join('categoria','productos.id_catg', '=','categoria.id_categoria')
        ->join('subcategoria','productos.id_subcatg', '=','subcategoria.id_subcategoria')
        ->join('marcas','productos.id_marc', '=','marcas.id_marca')
        ->join('cliente','productos.id_proveedor', '=','cliente.id_cliente');

        if($estatus === '0' || $estatus === '1'){
            $sql->where('productos.estatus', $estatus);            
        }

        $productos = $sql->get();
        return response(['data' => $productos]);
    }
    public function crearProducto(Request $request){
        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'stock' => 'required|integer|max:999',
            'precio_unitario' => 'required|numeric|max:99999999.99',
            'precio_venta' => 'required|numeric|max:99999999.99',
            'IVA_producto' => 'required|numeric|max:99999999.99',
            'id_categoria'=>'required|exists:categoria,id_categoria',
            'id_subcategoria'=>'required|exists:subcategoria,id_subcategoria',
            'id_proveedor'=>'required|exists:cliente,id_cliente',
            'id_marca'=>'required|exists:marcas,id_marca',
            'imagen_producto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'codigo' => 'required|string|max:255'
        ]);
        try{

            if ($request->hasFile('imagen_producto')) {
                $archivo = $request->file('imagen_producto');
                $nombreArchivo = $archivo->getClientOriginalName();
                $carpeta = public_path('imgproducto');
                $rutaDestino = $carpeta . '/' . $nombreArchivo;

                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }

                if (!file_exists($rutaDestino)) {
                    $archivo->move($carpeta, $nombreArchivo);
                }

                $rutaImagen = 'imgproducto/' . $nombreArchivo;
            }
            $producto = productos::create([
                'nombre_producto' => $request->nombre_producto,
                'stock' => $request->stock,
                'precio_unitario' => $request->precio_unitario,
                'precio_venta' => $request->precio_venta,
                'IVA_producto'=> $request->IVA_producto,
                'id_catg'=> $request->id_categoria,
                'id_subcatg'=>$request->id_subcategoria,
                'id_proveedor'=>$request->id_proveedor,
                'id_marc'=>$request->id_marca,
                'imagen_producto'=>$rutaImagen,
                'codigo' => $request->codigo
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Producto creado',
                    'message' => 'El producto se registró correctamente.'
                ]);
            }

            return redirect()->route('productos')->with('success','Producto creado correctamente');
        }catch (\Exception $e) {
            log::error($e);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'title'=> 'Error',
                'message' => 'Ocurrió un error al crear el producto: ' . $e->getMessage()
            ], 500);
        }

        return back()->withErrors('Ocurrió un error al crear el producto: ' . $e->getMessage());
    }

    }
    public function actualizarProducto(Request $request, $id)
    {
        $Producto = productos::findOrFail($id);
        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'stock' => 'required|integer|max:999',
            'precio_unitario' => 'required|numeric|max:99999999.99',
            'precio_venta' => 'required|numeric|max:99999999.99',
            'IVA_producto' => 'required|numeric|max:99999999.99',
            'id_categoria'=>'required|exists:categoria,id_categoria',
            'id_subcategoria'=>'required|exists:subcategoria,id_subcategoria',
            'id_marca'=>'required|exists:marcas,id_marca',
            'id_proveedor'=>'required|exists:cliente,id_cliente',
            'imagen_producto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'codigo' => 'required|string|max:255'
        ]);
        try{
            $datosModificados= [
                'nombre_producto' => $request->nombre_producto,
                'stock' => $request->stock,
                'precio_unitario' => $request->precio_unitario,
                'precio_venta' => $request->precio_venta,
                'IVA_producto'=> $request->IVA_producto,
                'id_catg'=> $request->id_categoria,
                'id_subcatg'=>$request->id_subcategoria,
                'id_marc'=>$request->id_marca,
                'codigo'=> $request->codigo,
                'id_proveedor'=> $request->id_proveedor
            ];
            if ($request->hasFile('imagen_producto')) {
                $archivo = $request->file('imagen_producto');
                $nombreArchivo = $archivo->getClientOriginalName();
                $carpeta = public_path('imgproducto');
                $rutaDestino = $carpeta . '/' . $nombreArchivo;
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }
                if (!file_exists($rutaDestino)) {
                    $archivo->move($carpeta, $nombreArchivo);
                }
                if ($Producto->imagen_producto && $Producto->imagen_producto !== ('imgproducto/' . $nombreArchivo))
                {
                    $rutaAnterior = public_path($Producto->imagen_producto);
                    if (file_exists($rutaAnterior)) {
                        @unlink($rutaAnterior);
                    }
                }
                $datosModificados['imagen_producto'] = 'imgproducto/' . $nombreArchivo;
            }   
            $Producto->update($datosModificados);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Producto Actualizado',
                    'message' => 'Los datos del producto se actualizo correctamente'
                ]);
            }
            return redirect()->back()->with('success', 'Producto actualizado correctamente');
        }catch(\Exception $e){
            log::error($e); 
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Ocurrió un error al editar el cliente: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors('Ocurrió un error al editar el cliente: ' . $e->getMessage());
        }
        
    }
    public function buscarProducto($id){
        $producto = DB::table('productos')
        ->join('categoria','productos.id_catg', '=','categoria.id_categoria')
        ->join('subcategoria','productos.id_subcatg', '=','subcategoria.id_subcategoria')
        ->join('marcas','productos.id_marc', '=','marcas.id_marca')
        ->join('cliente','productos.id_proveedor', '=','cliente.id_cliente')
        ->select([
            'productos.*',
        ])
        ->where('productos.id_producto', $id)
        ->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        return response()->json($producto, 200, [], JSON_UNESCAPED_UNICODE);
        
    }
    public function eliminarProducto($idUsuario)
    {
        $producto = productos::find($idUsuario);
        if (!$producto) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'producto no encontrado.']);
        }
        $producto->estatus = 0;
        $producto->save();
        return response()->json(['success' => true,'title'=>'Producto desactivado', 'message' => 'El producto fue desactivado correctamente.']);
    }

    public function restaurarProducto($idUsuario)
    {
        $producto = productos::find($idUsuario);
        if (!$producto) {
            return response()->json(['success' => false, 'title'=>'Error', 'message' => 'producto no encontrado.']);
        }
        $producto->estatus = 1;
        $producto->save();
        return response()->json(['success' => true,'title'=>'Producto activado', 'message' => 'producto fue activado correctamente.']);
    }
    public function obtenerProductos()
    {
        return response()->json(productos::select('id_producto', 'nombre_producto','precio_venta', 'stock')->get());
    }
}
