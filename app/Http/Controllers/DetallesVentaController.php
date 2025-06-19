<?php

namespace App\Http\Controllers;

use App\Models\detalle_venta;
use App\Models\productos;
use App\Models\ventas;
use Illuminate\Http\Request;

class DetallesVentaController 
{
    public function mostrarDetalleVentas()
    {
        $detalle_venta = detalle_venta::with(['venta.cliente', 'venta.usuario', 'producto'])->where('estatus', 1)->get();
        $productos = productos::all();
        $ventas = ventas::all();
        return view('paginas.detalleVentas', compact('detalle_venta','productos','ventas'));
    }
    public function crearDetalleVenta(Request $request){
        $request->validate([
            'id_venta' => 'required|exists:ventas,id_venta',
            'id_producto' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|integer|max:999',
            'precio_compra' => 'required|numeric|max:99999999.99',
            'estatus' => 'required|string|max:255',
        ]);

        detalle_venta::create([
            'id_venta' => $request->id_venta,
            'id_producto' => $request->id_producto,
            'cantidad' => $request->cantidad,
            'precio_compra' => $request->precio_compra,
            'estatus' => $request->estatus,
        ]);

        return redirect()->back()->with('success', 'Detalle_venta creada correctamente.');
    }
    public function actualizarDetalleVenta(Request $request, $id){
        $request->validate([
            'id_venta' => 'required|exists:ventas,id_venta',
            'id_producto' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|integer|max:999',
            'precio_compra' => 'required|numeric|max:99999999.99',
            'estatus' => 'required|string|max:255',
        ]);
        $detalle_venta = detalle_venta::findOrFail($id);

        $detalle_venta->update([
            'id_venta' => $request ->id_venta,
            'id_producto' => $request->id_producto,
            'cantidad' => $request->cantidad,
            'precio_compra' => $request->precio_compra,
            'estatus' => $request->estatus
        ]);
        return redirect()->back()->with('success', 'Detalle_venta actualizado correctamente');
    }
    public function eliminarDetalleVenta($iddetalle)
    {
        $detalle = detalle_venta::find($iddetalle);

        if(!$iddetalle){
         return redirect()->back()->with('error', 'Detalle no encontrado.');
        }
 
        $detalle->estatus=0;
        $detalle->save();
        return redirect()->back()->with('success', 'Detalle eliminado exitosamente.');
    }
}
