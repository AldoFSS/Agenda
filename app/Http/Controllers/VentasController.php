<?php

namespace App\Http\Controllers;
use App\Models\detalle_venta;
use App\Models\productos;
use App\Models\ventas;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VentasController 
{
    public function mostrarVentas($estatus)
    {
        
        $sql = ventas::select([
            'ventas.*',
            'cliente.nombre_cliente as Cliente',
            'usuarios.nombre_usuario as Usuario'
        ])
        ->join('cliente','ventas.id_cli','=','cliente.id_cliente')
        ->join('usuarios','ventas.id_usr','=','usuarios.id_usuario');

        if($estatus == 0 || $estatus ==1){
            $sql->where('ventas.estatus', $estatus);
        }
        $ventas = $sql->get();
        return response(['data' => $ventas]);
    }
    public function obtenerDetalles($id)
    {
        $detalles = detalle_venta::where('id_vnt', $id)
            ->join('productos', 'detalle_venta.id_prd', '=', 'productos.id_producto')
            ->select(
                'detalle_venta.id_prd',
                 'productos.nombre_producto', 
                 'detalle_venta.cantidad', 
                 'productos.precio_venta', 
                 'detalle_venta.subtotal',
                 'detalle_venta.IVA',
                 'detalle_venta.total'
            )
            ->get();
        return response()->json($detalles);
    }
    public function crearVenta(Request $request)
    {
    $request->validate([
        'id_cliente' => 'required|exists:cliente,id_cliente',
        'id_usuario' => 'required|exists:usuarios,id_usuario',
        'fecha_venta' => 'required|date',
        'productos' => 'required|array',
        'productos.*.id_producto' => 'required|exists:productos,id_producto',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio_venta' => 'required|numeric',
        'productos.*.subtotal' => 'required|numeric',
        'productos.*.IVA' => 'required|numeric',
        'productos.*.total' => 'required|numeric',
    ]);
    try{
        $totalVenta = array_sum(array_column($request->productos, 'total'));

        $venta = ventas::create([
            'id_cli' => $request->id_cliente,
            'id_usr' => $request->id_usuario,
            'fecha_venta' => $request->fecha_venta,
            'total' => $totalVenta,
        ]);

        foreach ($request->productos as $producto) {
            detalle_venta::create([
                'id_vnt' => $venta->id_venta,
                'id_prd' => $producto['id_producto'],
                'cantidad' => $producto['cantidad'],
                'subtotal' => $producto['subtotal'],
                'IVA' => $producto['IVA'],
                'total' => $producto['total'],
            ]);
            $productoDB = productos::find($producto['id_producto']);
            if ($productoDB) {
                $productoDB->stock -= $producto['cantidad'];
                $productoDB->save();
            }
        }
        if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Venta creada',
                    'message' => 'La venta se registró correctamente.'
                ]);
            }
        return redirect()->back()->with('success', 'Venta creada correctamente');
    }catch (\Exception $e) {
            log::error($e);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'title' => 'Error',
                'message' => 'Ocurrió un error al crear la venta: ' . $e->getMessage()
            ], 500);
        }

        return back()->withErrors('Ocurrió un error al crear la Venta: ' . $e->getMessage());
    }
}
public function actualizarVenta(Request $request, $id)
{
    $request->validate([
        'id_cliente' => 'required|exists:cliente,id_cliente',
        'id_usuario' => 'required|exists:usuarios,id_usuario',
        'fecha_venta' => 'required|date',
        'productos' => 'required|array',
        'productos.*.id_producto' => 'required|exists:productos,id_producto',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio_venta' => 'required|numeric',
        'productos.*.subtotal' => 'required|numeric',
        'productos.*.IVA' => 'required|numeric',
        'productos.*.total' => 'required|numeric',
    ]);
    $venta = ventas::findOrFail($id);
    try{
        $venta->update([
            'id_cli' => $request->id_cliente,
            'id_usr' => $request->id_usuario,
            'fecha_venta' => $request->fecha_venta,
        ]);

    $productosActuales = detalle_venta::where('id_vnt', $id)->get()->keyBy('id_prd');

    $totalVenta = 0;
    $productosRecibidos = [];

    foreach ($request->productos as $producto) {
        $idProducto = $producto['id_producto'];
        $cantidadNueva = $producto['cantidad'];
        $subtotal = $producto['subtotal'];
        $IVA = $producto['IVA'];
        $total = $producto['total'];

        $totalVenta += $total;
        $productosRecibidos[] = $idProducto;
        $productoDB = productos::find($idProducto);

        if ($productosActuales->has($idProducto)) {
            $detalle = $productosActuales[$idProducto];
            $cantidadAnterior = $detalle->cantidad;
            $detalle->update([
                'cantidad' => $cantidadNueva,
                'subtotal' => $subtotal,
                'IVA' => $IVA,
                'total' => $total,
            ]);
            if ($productoDB) {
                $productoDB->stock += $cantidadAnterior - $cantidadNueva;
                $productoDB->save();
            }
            } else {
                detalle_venta::create([
                    'id_vnt' => $venta->id_venta,
                    'id_prd' => $idProducto,
                    'cantidad' => $cantidadNueva,
                    'subtotal' => $subtotal,
                    'IVA' => $IVA,
                    'total' => $total,
                ]);
                if ($productoDB) {
                    $productoDB->stock -= $cantidadNueva;
                    $productoDB->save();
                }
            }
        }

        // Eliminar productos que ya no están
        foreach ($productosActuales as $idProducto => $detalle) {
            if (!in_array($idProducto, $productosRecibidos)) {
                $productoDB = productos::find($idProducto);
                if ($productoDB) {
                    $productoDB->stock += $detalle->cantidad;
                    $productoDB->save();
                }
                $detalle->delete();
            }
        }

        $venta->update(['total' => $totalVenta]);
        if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Venta actualizada',
                    'message' => 'Los datos de la venta se actualizaron correctamente.'
                ]);
            }

        return redirect()->back()->with('success', 'Venta actualizada correctamente');

    }catch (\Exception $e) {
            log::error($e);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'title' => 'Error',
                'message' => 'Ocurrió un error al crear el cliente: ' . $e->getMessage()
            ], 500);
        }

        return back()->withErrors('Ocurrió un error al crear el cliente: ' . $e->getMessage());
    }
    }
    public function eliminarVenta($idVenta)
    {
        $venta = ventas::find($idVenta);

        if (!$venta) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'Venta no encontrada.']);
        }

        $detalles = detalle_venta::where('id_vnt', $venta->id_venta)->get();
        foreach ($detalles as $detalle) {
            $producto = productos::find($detalle->id_prd);
            if ($producto) {
                $producto->stock += $detalle->cantidad;
                $producto->save();
            }
        }

        $venta->estatus = 0;
        $venta->save();

        return response()->json(['success' => true, 'title'=> 'Venta desactivada', 'message' => 'La venta fue desactivado correctamente.']);
    }

    public function restaurarVenta($idVenta)
    {
        $venta = ventas::find($idVenta);
        if (!$venta) {
            return response()->json(['success' => false,'title'=>'Error', 'message' => 'Venta no encontrada.']);
        }

        $detalles = detalle_venta::where('id_vnt', $venta->id_venta)->get();

        // Verificar stock antes de restaurar
        foreach ($detalles as $detalle) {
            $producto = productos::find($detalle->id_prd);
            if ($producto && $producto->stock < $detalle->cantidad) {
                return response()->json(['success' => false,'title'=>'Error', 'message' => 'No hay suficiente stock para restaurar la venta.']);
            }
        }

        // Descontar stock
        foreach ($detalles as $detalle) {
            $producto = productos::find($detalle->id_prd);
            if ($producto) {
                $producto->stock -= $detalle->cantidad;
                $producto->save();
            }
        }

        $venta->estatus = 1;
        $venta->save();

        return response()->json(['success' => true,'title'=>'Venta activado','message' => 'La Venta fue activado exitosamente.']);
    }


    public function mostrarGrafica()
    {
        $ventasPorProducto = DB::table('detalle_venta')
        ->join('productos', 'detalle_venta.id_prd', '=', 'productos.id_producto')
        ->select('productos.nombre_producto', DB::raw('SUM(detalle_venta.cantidad) as cantidad_total'))
        ->groupBy('productos.nombre_producto')
        ->get();

    $ventasPorMes = DB::table('detalle_venta')
        ->join('ventas', 'detalle_venta.id_vnt', '=', 'ventas.id_venta')
        ->select(
            DB::raw('MONTH(ventas.fecha_venta) as mes'),
            DB::raw('SUM(detalle_venta.total) as total_mensual')
        )
        ->groupBy(DB::raw('MONTH(ventas.fecha_venta)'))
        ->orderBy(DB::raw('MONTH(ventas.fecha_venta)'))
        ->get();
        return view('paginas.grafico', compact('ventasPorProducto','ventasPorMes'));
    }
    public function obtenerVentaConDetalles($id)
    {
        $venta = DB::table('ventas')
        ->select(
            'ventas.*',
            'cliente.nombre_cliente as Cliente',
            'usuarios.nombre_usuario as Usuario'
        )
        ->join('cliente', 'ventas.id_cli', '=', 'cliente.id_cliente')
        ->join('usuarios', 'ventas.id_usr', '=', 'usuarios.id_usuario')
        ->where('ventas.id_venta', '=', $id)
        ->first();

        $detalles = DB::table('detalle_venta')
        ->join('productos', 'detalle_venta.id_prd', '=', 'productos.id_producto')
        ->select(
            'productos.id_producto',
            'productos.nombre_producto',
            'detalle_venta.cantidad',
            'productos.precio_venta',
            'detalle_venta.subtotal',
            'detalle_venta.IVA',
            'detalle_venta.total'
        )
        ->where('detalle_venta.id_vnt', '=', $id)
        ->get();

        return response()->json([
            'venta' => $venta,
            'detalles' => $detalles
        ]);
    }
}
