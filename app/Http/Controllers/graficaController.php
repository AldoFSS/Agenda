<?php

namespace App\Http\Controllers;

use App\Models\productos;
use App\Models\usuarios;
use App\Models\ventas;
use DB;
use Illuminate\Http\Request;
use function Laravel\Prompts\table;

class graficaController
{
    public function mostrarGrafica($tipoGrafica, $fecha_inicio, $fecha_final)
    {
        switch($tipoGrafica)
        {
            case 'ventas':
                $grafica = DB::table('detalle_venta')
                ->join('ventas','detalle_venta.id_vnt','=','ventas.id_venta')
                ->select(DB::raw('DATE(ventas.fecha_venta) as fecha'), DB::raw('SUM(detalle_venta.cantidad) as total_vendidos'))
                ->whereBetween('ventas.fecha_venta',[$fecha_inicio, $fecha_final])
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();
                break;
            case 'productos':
                $grafica = DB::table('detalle_venta')
                ->join('ventas','detalle_venta.id_vnt','=','ventas.id_venta')
                ->join('productos','detalle_venta.id_prd','=', 'productos.id_producto')
                ->select('productos.nombre_producto', DB::raw('SUM(detalle_venta.cantidad) as cantidad_total'))
                ->whereBetween('ventas.fecha_venta',[$fecha_inicio, $fecha_final])
                ->groupBy('productos.nombre_producto')
                ->orderByDesc('cantidad_total')
                ->get();
                break;
            case 'usuarios':
                $grafica = DB::table('ventas')
                ->join('usuarios','ventas.id_usr','=','usuarios.id_usuario')
                ->select('usuarios.nombre_usuario', DB::raw('SUM(ventas.total) as Total'))
                ->whereBetween('ventas.fecha_venta',[$fecha_inicio, $fecha_final])
                ->groupBy('usuarios.nombre_usuario')
                ->get();
                break;
            case 'clientes':
                $grafica = DB::table('ventas')
                ->join('cliente','ventas.id_cli','=','cliente.id_cliente')
                ->select('cliente.nombre_cliente', DB::raw('SUM(ventas.total) as Total'))
                ->whereBetween('ventas.fecha_venta',[$fecha_inicio, $fecha_final])
                ->groupBy('cliente.nombre_cliente')
                ->get();
                break;
            default:
                return response()->json([
                'success' => false,
                'message' => 'Tipo de gráfica no válido.'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'data' => $grafica
        ]);
    }
    public function reporteGrafica($tipoGrafica, $fecha_inicio, $fecha_final, $id){
        switch($tipoGrafica)
        {
           case 'ventas':
            $query = DB::table('detalle_venta')
                ->join('ventas', 'detalle_venta.id_vnt', '=', 'ventas.id_venta')
                ->select(DB::raw('DATE(ventas.fecha_venta) as fecha'), DB::raw('SUM(detalle_venta.cantidad) as total_vendidos'))
                ->whereBetween('ventas.fecha_venta', [$fecha_inicio, $fecha_final]);

            // Si se seleccionó un vendedor
            if ($id !== null) {
                $query->where('ventas.id_usuario', $id);
            }

            $grafica = $query
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();
            break;
            case 'productos':
            $query = DB::table('detalle_venta')
                ->join('ventas', 'detalle_venta.id_vnt', '=', 'ventas.id_venta')
                ->join('productos', 'detalle_venta.id_prd', '=', 'productos.id_producto')
                ->select(
                    DB::raw('DATE(ventas.fecha_venta) as fecha'),
                    'productos.nombre_producto',
                    DB::raw('SUM(detalle_venta.cantidad) as cantidad_total')
                )
                ->whereBetween('ventas.fecha_venta', [$fecha_inicio, $fecha_final]);

            if ($id !== null) {
                $query->where('productos.id_producto', $id);
            }

            $grafica = $query
                ->groupBy('fecha', 'productos.nombre_producto')
                ->orderBy('fecha')
                ->get();
            break;
            default:
                return response()->json([
                'success' => false,
                'message' => 'Tipo de gráfica no válido.'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'data' => $grafica
        ]);
    }
    public function mostrarOpciones($tipo)
{
    switch ($tipo) {
        case 'usuario':
            $usuarios = usuarios::select('id_usuario as id', 'nombre_usuario as nombre')->get();
            return response()->json($usuarios);
        
        case 'producto':
            $productos = productos::select('id_producto as id', 'nombre_producto as nombre')->get();
            return response()->json($productos);
        
        default:
            return response()->json([
                'success' => false,
                'message' => 'Tipo de opción no válido.'
            ], 400); // código 400 indica error del cliente
    }
}

}
