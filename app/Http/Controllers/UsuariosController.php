<?php

namespace App\Http\Controllers;

use App\Models\usuarios;
use DB;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UsuariosController
{
    public function mostrarUsuarios($estatus)
    {
        if ($estatus == 1 || $estatus == 0) {
            $usuarios = usuarios::where('estatus', $estatus)->get(); 
        } else {
            $usuarios = usuarios::all();
        }
        return response(['data' => $usuarios]);
    }
    public function contadores()
    {
        $contadores = DB::select("
            SELECT 
                (SELECT COUNT(*) FROM usuarios) AS total_usuarios,
                (SELECT COUNT(*) FROM cliente) AS total_clientes,
                (SELECT COUNT(*) FROM productos) AS total_productos
        ");

        return response()->json($contadores[0]);
    }
   public function crearUsuario(Request $request) 
   {
    $validated = $request->validate([
        'nombre_usuario' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
        'correo' => 'required|email|max:255',
        'rol' => 'required|string|max:50',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'contraseña' => 'required|string|max:255',
    ]);

    try {

        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombreArchivo = $archivo->getClientOriginalName();
            $carpeta = public_path('imgusuario');
            $rutaDestino = $carpeta . '/' . $nombreArchivo;

            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0755, true);
            }

            if (!file_exists($rutaDestino)) {
                $archivo->move($carpeta, $nombreArchivo);
            }

            $rutaImagen = 'imgusuario/' . $nombreArchivo;
        }
        $usuario = usuarios::create([
            'nombre_usuario' => $validated['nombre_usuario'],
            'telefono' => $validated['telefono'],
            'correo' => $validated['correo'],
            'rol' => $validated['rol'],
            'imagen' => $rutaImagen,
            'contraseña' => sha1($validated['contraseña'])
        ]);

        if($request->ajax()){
            return response()->json([
                'success' => true,
                'title' => 'Usuario creado',
                'message' => 'El usuario se registró correctamente.'
            ]);
        }
        return redirect()->back()->with('success', 'Usuario creado correctamente');

        }catch (\Exception $e) {
    if ($request->ajax()) {
        if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
            return response()->json([
                'success' => false,
                'title' => 'Dato duplicado',
                'message' => 'El correo o nombre de usuario ya está en uso.'
            ], 409); 
        }else{
            return response()->json([
            'success' => false,
            'title' => 'Error',
            'message' => 'Ocurrió un error al crear el usuario: ' . $e->getMessage()
        ], 500);
        }
    }
    return back()->withErrors('Ocurrió un error: ' . $e->getMessage());
}
    }
    public function actualizarUsuario(Request $request, $id)
    {
        $Usuario = usuarios::findOrFail($id);
        $request->validate([
            'nombre_usuario' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rol' => 'required|string|max:50',
        ]);

        try{
            $datosActualizados = [
                'nombre_usuario' => $request->nombre_usuario,
                'telefono' => $request->telefono,
                'correo'=>$request->correo,
                'rol'=>$request->rol,
            ];
            if ($request->hasFile('imagen')) {
                $archivo = $request->file('imagen');
                $nombreArchivo = $archivo->getClientOriginalName();
                $carpeta = public_path('imgusuario');
                $rutaDestino = $carpeta . '/' . $nombreArchivo;
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }
                if (!file_exists($rutaDestino)) {
                    $archivo->move($carpeta, $nombreArchivo);
                }
                if ($Usuario->imagen && $Usuario->imagen !== ('imgusuario/' . $nombreArchivo))
                {
                    $rutaAnterior = public_path($Usuario->imagen);
                    if (file_exists($rutaAnterior)) {
                        @unlink($rutaAnterior);
                    }
                }
                $datosActualizados['imagen'] = 'imgusuario/' . $nombreArchivo;
            }   
            $Usuario->update($datosActualizados);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Usuario actualizado',
                    'message' => 'Los datos del usuario se actualizaron correctamente.'
                ]);
            }
            return redirect()->back()->with('success', 'Usuario actualizado correctamente');
        }catch (QueryException $e) {
            log::error($e);
            if ($request->ajax()) {
        if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
            return response()->json([
                'success' => false,
                'title' => 'Dato duplicado',
                'message' => 'El correo o nombre de usuario ya está en uso.'
            ], 409); 
        }else{
            return response()->json([
            'success' => false,
            'title' => 'Error',
            'message' => 'Ocurrió un error al editar el usuario: ' . $e->getMessage()
        ], 500);
        }
        }
             return back()->withErrors('Ocurrió un error al editar el usuario: ' . $e->getMessage());
        }
        
    }
    public function eliminarUsuario($idUsuario)
    {
        $usuario = usuarios::find($idUsuario);
        if (!$usuario) {
            return response()->json([
                'success' => false, 
                'title'=> 'Error', 
                'message' => 'usuario no encontrado.'
            ]);
        }
        $usuario->estatus = 0;
        $usuario->save();
        return response()->json(['success' => true,  'title' => 'Usuario desactivado','message' => 'El usuario fue desactivado correctamente.']);
    }

    public function restaurarUsuario($idUsuario)
    {
        $usuario = usuarios::find($idUsuario);
        if (!$usuario) {
            return response()->json(['success' => false, 'title'=> 'Error', 'message' => 'usuario no encontrado.']);
        }
        $usuario->estatus = 1;
        $usuario->save();
         return response()->json(['success' => true, 'title' => 'Usuario Activado', 'message' => 'usuario fue activado correctamente.']);
    }

    public function obtenerUsuario($id){
        $usuario = DB::table('usuarios')
        ->where('usuarios.id_usuario', $id)
        ->first();
        if(!$usuario){
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario, 200, [], JSON_UNESCAPED_UNICODE);
    }
     public function login(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string',
            'contraseña' => 'required|string',
        ]);

        $usuario = usuarios::where('nombre_usuario', $request->nombre_usuario)->first();

        if (!$usuario || sha1($request->contraseña) !== $usuario->contraseña) {
            return response()->json([
                'success' => false,
                'title' => 'Credenciales inválidas',
                'message' => 'Usuario o contraseña incorrectos.',
            ]);
        }

        Auth::login($usuario); // Inicia sesión

        return response()->json([
            'success' => true,
            'title' => 'Bienvenido',
            'message' => 'Ingreso exitoso.',
            'usuario' => [
                'nombre_usuario' => $usuario->nombre_usuario
            ]
        ]);
    }
    public function logout(Request $request)
{
   Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'success' => true,
        'title' => 'Sesión cerrada',
        'message' => 'Has cerrado sesión correctamente.'
    ]);
}

    public function Usuarios(){
        $usuarios = usuarios::select([
            'id_usuario',
            'nombre_usuario',
        ])
        ->where('estatus','=',1)->get();

        return response()->json(['data' => $usuarios]);
    }
}
