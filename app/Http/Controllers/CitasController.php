<?php

namespace App\Http\Controllers;

use App\Models\citas;
use App\Models\cliente;
use App\Models\usuarios;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class CitasController 
{
    private function getClient()
    {
    $client = new Google_Client();
    $client->setApplicationName('agenda_comercial');
    $client->setScopes('https://www.googleapis.com/auth/calendar');
    $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
    $client->setAccessType('offline');

    $tokenPath = storage_path('app/google-calendar/token.json');

    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        } else {
            throw new \Exception('Token expirado, autenticación necesaria');
        }
    }
    return $client;
}


    public function mostrarCitas()
    {
        try{
            $client = $this->getClient();
            $citas = citas::where('estatus',1)->get() ;  
            $clientes = cliente::where('estatus',1)->get();
            $usuarios = usuarios::where('estatus',1)->get();

            return view('paginas.citas', compact('citas', 'clientes', 'usuarios'));
        }catch(\Exception $e){
            return redirect()->action([self::class, 'redirectToGoogle']);
        }
    }
    public function crearCita(Request $request)

    {
    $request->validate([
        'id_cliente' => 'required|exists:cliente,id_cliente',
        'id_usuario' => 'required|exists:usuarios,id_usuario',
        'fecha_cita'=>'required|date',
        'hora_inicio' => 'required',
        'hora_fin' => 'required',
        'motivo' => 'required|string|max:255',
    ]);

    try {
        $cita = citas::create([
            'id_cli' => $request->id_cliente,
            'id_usr' => $request->id_usuario,
            'fecha_cita' => $request->fecha_cita,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'motivo' => $request->motivo,
        ]);

        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event([
            'summary' => $request->motivo,
            'description' => "Cliente ID: {$request->id_cliente}, Usuario ID: {$request->id_usuario}",
            'start' => new Google_Service_Calendar_EventDateTime([
                'dateTime' => $request->fecha_cita . 'T' . $request->hora_inicio . ':00',
                'timeZone' => 'America/Mexico_City',
            ]),
            'end' => new Google_Service_Calendar_EventDateTime([
                'dateTime' => $request->fecha_cita . 'T' . $request->hora_fin . ':00',
                'timeZone' => 'America/Mexico_City',
            ]),
        ]);

        $calendarId = 'primary';
        $googleEvent = $service->events->insert($calendarId, $event);

        $cita->google_event_id = $googleEvent->getId();
        $cita->save();

        if ($request->ajax()){
            return response()->json(['success' => true, 'title'=>'Cita Creada', 'message' => 'Cita creada correctamente y sincronizada con Google Calendar.']);
        }
        return redirect()->back()->with('success', 'Cita creada correctamente y sincronizada con Google Calendar.');

    } catch (\Exception $e) {
        \Log::error($e);
        if ($request->ajax()) {
            return response()->json(['success' => false, 'title'=>'Error', 'message' => 'No se creo la Cita.']);
        }
        return back()->withErrors('Error al crear la cita: ' . $e->getMessage());
    }
}
   public function actualizarFecha(Request $request, $id) 
{
    try {
        $data = $request->json()->all();
        $cita = citas::findOrFail($id);
        $cita->update([
            'fecha_cita' => $data['fecha_cita'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
        ]);

        if ($cita->google_event_id) {
            $client = $this->getClient();
            $service = new Google_Service_Calendar($client);

            $calendarId = 'primary';
            $event = $service->events->get($calendarId, $cita->google_event_id);

            $event->setStart(new Google_Service_Calendar_EventDateTime([
                'dateTime' => $data['fecha_cita'] . 'T' . $data['hora_inicio'] . ':00',
                'timeZone' => 'America/Mexico_City',
            ]));
            $event->setEnd(new Google_Service_Calendar_EventDateTime([
                'dateTime' => $data['fecha_cita'] . 'T' . $data['hora_fin'] . ':00',
                'timeZone' => 'America/Mexico_City',
            ]));

            $service->events->update($calendarId, $cita->google_event_id, $event);
        }

        return response()->json(['success'=> true, 'title'=>'Cita Creada','message' => 'Cita actualizada correctamente y sincronizada con Google Calendar.']);

    } catch (\Exception $e) {
        return response()->json([
            'success'=> false, 
            'title'=>'Error',
            'message' => 'No se pudo actualizar la cita',
        ], 500);
    }
}

    public function actualizarCita(Request $request, $id)
{
    $cita = citas::findOrFail($id);
    
    $request->validate([
        'id_cliente' => 'required|exists:cliente,id_cliente',
        'id_usuario' => 'required|exists:usuarios,id_usuario',
        'fecha_cita' => 'required|date',
        'hora_inicio' => 'required',
        'hora_fin' => 'required',
        'motivo' => 'required|string|max:255',
    ]);
    
    try {
        $cita->update([
            'id_cli' => $request->id_cliente,
            'id_usr' => $request->id_usuario,
            'fecha_cita' => $request->fecha_cita,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'motivo' => $request->motivo,
        ]);
        if ($cita->google_event_id) {
            $client = $this->getClient();
            $service = new  Google_Service_Calendar($client);

            $calendarId = 'primary';
            $event = $service->events->get($calendarId, $cita->google_event_id);
            $event->setSummary($request->motivo);
            $event->setDescription("Cliente ID: {$request->id_cliente}, Usuario ID: {$request->id_usuario}");
            $event->setStart(new Google_Service_Calendar_EventDateTime([
                'dateTime' => $request->fecha_cita . 'T' . $request->hora_inicio . ':00',
                'timeZone' => 'America/Mexico_City',
            ]));
            $event->setEnd(new Google_Service_Calendar_EventDateTime([
                'dateTime' => $request->fecha_cita . 'T' . $request->hora_fin . ':00',
                'timeZone' => 'America/Mexico_City',
            ]));
            $updatedEvent = $service->events->update($calendarId, $cita->google_event_id, $event);
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'title'=> 'Cita actualizada',
                'message' => 'Cita actualizada correctamente y sincronizada con Google Calendar.',
            ]);
        }
        
        return redirect()->back()->with('success', 'Cita actualizada correctamente y sincronizada con Google Calendar.');
        
    } catch (\Exception $e) {
        \Log::error($e);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'title' => 'Error',
                'message' => 'Ocurrió un error al actualizar la cita: ' . $e->getMessage()
            ], 500);
        }
        
        return back()->withErrors('Ocurrió un error al actualizar la cita: ' . $e->getMessage());
    }
}

    public function eliminarCita($idcita)
{
    $cita = citas::find($idcita);
    
    if (!$cita) {
        return response()->json(['success' => false,'title' => 'Error','mensaje' => 'Cita no encontrada.'], 404);
    }

    try {

        if ($cita->google_event_id) {
            $client = $this->getClient();
            $service = new Google_Service_Calendar($client);
            $calendarId = 'primary';
            
            $service->events->delete($calendarId, $cita->google_event_id);
        }

        $cita->estatus = 0;
        $cita->save();

        return response()->json(['success'=>true,'title'=>'Cita eliminada','message' => 'Cita eliminada exitosamente y removida de Google Calendar.']);

    } catch (\Exception $e) {
        \Log::error($e);
        return response()->json([
            'success'=>false,
            'title'=>'Error',
            'message' => 'Error al eliminar la cita.',
        ], 500);
    }
}

    public function obtenerEventos()
    {
        $citas = citas::where('estatus',1)->get();

        $datos = $citas->map(function ($cita) {
            return [
                'id_ct' => $cita->id_cita,
                'id_cli'=>$cita->id_cli,
                'id_usr'=>$cita->id_usr,
                'title' => $cita->motivo,
                'start' => $cita->fecha_cita . 'T' . $cita->hora_inicio,
                'end' => $cita->fecha_cita . 'T' . $cita->hora_fin,
                'color' => '#2324ff'
            ];
        });
        return response()->json($datos);
    }
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return redirect($client->createAuthUrl());
    }
    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setAccessType('offline');

        $token = $client->fetchAccessTokenWithAuthCode($request->code);
        file_put_contents(storage_path('app/google-calendar/token.json'), json_encode($token));

        return redirect()->route('citas')->with('success', 'Autenticación con Google Calendar completada.');
    }


}
