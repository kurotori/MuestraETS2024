<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Models\Partida;
use Illuminate\Http\Request;
use stdClass;

class PartidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partidas=Partida::all();
        return response()->json([
            'mensaje'=>'OK',
            'partidas'=>$partidas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function crear(Request $solicitud)
    {

        $validacion=$solicitud->validate(
            [
                //'juego'=>'required|in:MEGAMANIA, DRAGONFIRE,ICECLIMBER,GALAGA',
                //'puntaje'=>'required|integer|min:0',
                //'estado'=>'nullable|in:abierta,jugando,cerrada',
                'jugadorId'=>'required|max:7'
            ]
        );

        $jugadorID = $validacion['jugadorId'];
        $id=substr($jugadorID,3);
        $apodo=substr($jugadorID,0,3);

        $jugador=Jugador::where('id',$id)->where('apodo',$apodo)->first();

        if ($jugador) {
            $juego=$this->elegirJuego();
            $codigo=$this->crearCodigo();

            $validacion['jugadorId'] = $id;
            $validacion['codigo']=$codigo;
            $validacion['juego']=$juego;

            $partida = Partida::create($validacion);

            return response()->json([
                'mensaje'=>'Partida Creada',
                'partida'=>$partida
            ]);
        }
        else{
            return response()->json([
                'mensaje'=>'ERROR',
                'dato'=>"El jugador $jugadorID no existe"
            ]);
        }



    }

    /**
     * Genera un código único para la partida
     */
    private function crearCodigo(){
        $hexChars = '0123456789abcdef';

        $resultado = '';
        $valido = false;

        while (! $valido) {
            for ($i = 0; $i < 4; $i++) {

                $resultado .= $hexChars[rand(0, strlen($hexChars) - 1)];
            }

            $partida = Partida::where('codigo',$resultado)->first();
            if($partida){
                $resultado='';
            }
            else{
                $valido=true;
            }
        }


        return $resultado;

    }

    /**
     * Selecciona un juego para la nueva partida
     */
    private function elegirJuego(){
        $juegos = array(
            "MEGAMANIA","GALAGA","ICECLIMBER","DRAGONFIRE"
        );

        $ultimaPartida = Partida::all()->last();

        if ($ultimaPartida) {
            $juegoAnt=array_search($ultimaPartida->juego, $juegos);

            if ($juegoAnt==3) {
                return $juegos[0];
            } else {
                return $juegos[$juegoAnt+1];
            }


        } else {
            return $juegos[rand(0,3)];
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Partida $partida)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partida $partida)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partida $partida)
    {
        //
    }
}
