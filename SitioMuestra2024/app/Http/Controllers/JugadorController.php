<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use Illuminate\Http\Request;

class JugadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function crear(Request $solicitud)
    {
        $validacion=$solicitud->validate(
            [
                'nombre'=>'required|string|max:255',
                'apellido'=>'required|string|max:255',
                'apodo'=>'required|string|max:3|min:3',
                'contacto'=>'required|string|max:255',
            ]
        );

        $jugador = Jugador::create($validacion);



        return response()->json([
            'estado'=>'OK',
            'mensaje'=>'Jugador Creado',
            'jugador'=>$jugador->apodo.$jugador->id
        ]);
    }

    /**
     * Permite ver un listado de todos los jugadores
     */
    public function verTodos(){
        $jugadores=Jugador::all();
        return response()->json([
            'mensaje'=>'OK',
            'jugadores'=>$jugadores
        ]);
    }



    public function verJugador(Request $solicitud){


        $jugadorID=$solicitud->jugadorID;

        if (strlen($jugadorID) > 7) {
            return response()->
            json(
                [
                    'estado'=>'OK',
                    'mensaje' => 'Identificador de jugador no válido'
                ],
                400
            );
        }

        $id=substr($jugadorID,3);
        $apodo=substr($jugadorID,0,3);
        //return $id;

        $jugador=Jugador::where('id',$id)->where('apodo',$apodo)->first();

        if ($jugador) {
            // Return the item as JSON response
            return response()->json([
                "mensaje"=>"OK",
                "jugador"=>$jugador
            ]);
        } else {
            // Return a not found response
            return response()->json([
                'estado'=> 'ERROR',
                'mensaje'=>'Jugador No Encontrado'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Jugador $jugador)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jugador $jugador)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jugador $jugador)
    {
        //
    }
}
