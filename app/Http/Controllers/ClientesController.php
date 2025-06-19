<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use DB;
use GuzzleHttp\Client;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ClientesController 
{
    public function mostrarClientes($estatus)
    {
        $sql = cliente::select([
            'cliente.*',
            'estados.nombre_estado as Estado',
            'municipios.municipio as Municipio'
        ])
        ->join('estados','cliente.id_estado','=','estados.id_estado')
        ->join('municipios','cliente.id_municipio','=','municipios.id_municipio');

        if ($estatus === '0' || $estatus === '1') {
            $sql->where('cliente.estatus', $estatus);
        }

        $clientes = $sql->get();
        return response(['data' => $clientes]);
    }
    public function crearCliente(Request $request)
{
    $validated = $request->validate([
        'nombre_cliente' => 'required|string|max:255',
        'nombre_comercial' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
        'correo' => 'required|email|max:255',
        'rol' => 'required|string|max:255',
        'codigo_postal' => 'required|numeric',
        'colonia' => 'required|string|max:255',
        'calle' => 'required|string|max:255',
        'id_estado' => 'required|exists:estados,id_estado',
        'id_municipio' => 'required|exists:municipios,id_municipio',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    try {

        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombreArchivo = $archivo->getClientOriginalName();
            $carpeta = public_path('imgcliente');
            $rutaDestino = $carpeta . '/' . $nombreArchivo;

            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0755, true);
            }

            if (!file_exists($rutaDestino)) {
                $archivo->move($carpeta, $nombreArchivo);
            }

            $rutaImagen = 'imgcliente/' . $nombreArchivo;
        }


        Cliente::create([
            'nombre_cliente' => $validated['nombre_cliente'],
            'nombre_comercial' => $validated['nombre_comercial'],
            'telefono' => $validated['telefono'],
            'correo' => $validated['correo'],
            'rol' => $validated['rol'],
            'codigo_postal' => $validated['codigo_postal'],
            'colonia' => $validated['colonia'],
            'calle' => $validated['calle'],
            'id_estado' => $validated['id_estado'],
            'id_municipio' => $validated['id_municipio'],
            'imagen' => $rutaImagen, // Guarda: clientes/nombre.jpg
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'title' => 'Cliente creado',
                'message' => 'El cliente se registró correctamente.'
            ]);
        }

        return redirect()->route('clientes')->with('success', 'Cliente creado correctamente');
    } catch (\Exception $e) {
        Log::error($e);

        if ($request->ajax()) {
        if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
            return response()->json([
                'success' => false,
                'title' => 'Dato duplicado',
                'message' => 'El correo o nombre del cliente ya está en uso.'
            ], 409); 
        }

        return response()->json([
            'success' => false,
            'title' => 'Error',
            'message' => 'Ocurrió un error al crear el cliente: ' . $e->getMessage()
        ], 500);
    }

        return back()->withErrors('Ocurrió un error al crear el cliente: ' . $e->getMessage());
    }
}
    public function actualizarCliente(Request $request, $id)
{
    $cliente = Cliente::findOrFail($id);

    $request->validate([
        'nombre_cliente' => 'required|string|max:255',
        'nombre_comercial' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
        'correo' => 'required|email|max:255',
        'rol' => 'required|string|max:255',
        'codigo_postal' => 'required|numeric',
        'colonia' => 'required|string|max:255',
        'calle' => 'required|string|max:255',
        'id_estado' => 'required|exists:estados,id_estado',
        'id_municipio' => 'required|exists:municipios,id_municipio',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

        try {
            $datosActualizados = [
                'nombre_cliente' => $request->input('nombre_cliente'),
                'nombre_comercial' => $request->input('nombre_comercial'),
                'telefono' => $request->input('telefono'),
                'correo' => $request->input('correo'),
                'rol' => $request->input('rol'),
                'codigo_postal' => $request->input('codigo_postal'),
                'colonia' => $request->input('colonia'),
                'calle' => $request->input('calle'),
                'id_estado' => $request->input('id_estado'),
                'id_municipio' => $request->input('id_municipio'),
            ];

            
            if ($request->hasFile('imagen')) {
                $archivo = $request->file('imagen');
                $nombreArchivo = $archivo->getClientOriginalName();
                $carpeta = public_path('imgcliente');
                $rutaDestino = $carpeta . '/' . $nombreArchivo;
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }
                if (!file_exists($rutaDestino)) {
                    $archivo->move($carpeta, $nombreArchivo);
                }
                if ($cliente->imagen && $cliente->imagen !== ('imgcliente/' . $nombreArchivo))
                {
                    $rutaAnterior = public_path($cliente->imagen);
                    if (file_exists($rutaAnterior)) {
                        @unlink($rutaAnterior);
                    }
                }
                $datosActualizados['imagen'] = 'imgcliente/' . $nombreArchivo;
            }   


  
            $cliente->update($datosActualizados);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Cliente actualizado',
                    'message' => 'Los datos del cliente se actualizaron correctamente.'
                ]);
            }
            return redirect()->route('clientes')->with('success', 'Cliente editado correctamente');
        } catch (\Exception $e) {
            Log::error($e);
            if ($request->ajax()) {
        if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
            return response()->json([
                'success' => false,
                'title' => 'Dato duplicado',
                'message' => 'El correo o nombre del cliente ya está en uso.'
            ], 409); 
        }

        return response()->json([
            'success' => false,
            'title' => 'Error',
            'message' => 'Ocurrió un error al editar el cliente: ' . $e->getMessage()
        ], 500);
    }
            return back()->withErrors('Ocurrió un error al editar el cliente: ' . $e->getMessage());
        }
    }
    public function obtenerCliente($id)
    {
        $cliente = DB::table('cliente')
        ->join('estados', 'cliente.id_estado', '=', 'estados.id_estado')
        ->join('municipios', 'cliente.id_municipio', '=', 'municipios.id_municipio')
        ->select(
            'cliente.*',
        )
        ->where('cliente.id_cliente', $id)
        ->first();
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
        return response()->json($cliente, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function buscarCliente(){
        $clientes = cliente::select([
            'id_cliente',
            'nombre_cliente',
        ])
        ->where('estatus','=',1)->get();

        return response()->json(['data' => $clientes]);
    }
    public function obtenerProveedor(){
        $clientes = cliente::select([
            'id_cliente as id_proveedor',
            'nombre_cliente as nombre_proveedor',
        ])
        ->where('estatus','=',1)
        ->where('rol', '=','proveedor')
        ->get();

        return response()->json(['data' => $clientes]);
    }
    public function eliminarCliente($id)
    {
        $cliente  =  cliente::find($id);
        if (!$cliente ) {
            return response()->json(['
            success' => false, 
            'title'=> 'Error', 
            'message' => 'cliente no encontrado.'
        ]);
        }
        $cliente ->estatus = 0;
        $cliente ->save();
        return response()->json([
            'success' => true,  
            'title' => 'Cliente desactivado',
            'message' => 'El cliente fue desactivado correctamente.'
        ]);
    }
    public function restaurarCliente($id)
    {
        $cliente  = cliente::find($id);
        if (!$cliente ) {
            return response()->json(['success' => false, 'title'=> 'Error', 'message' => 'cliente no encontrado.']);
        }
        $cliente ->estatus = 1;
        $cliente ->save();
        return response()->json(['success' => true, 'title' => 'Cliente Activado', 'message' => 'cliente fue activado correctamente.']);
    }
}
