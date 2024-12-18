<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Models\Partida;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function verTodas()
    {
        $partidas=Partida::with('jugador')->get();//all();
        return response()->json([
            'mensaje'=>'OK',
            'partidas'=>$partidas
        ]);
    }

    public function puntajes()
    {
        /*
        $partidas=Partida::with('jugador')->where('estado','cerrada')->get();

        $puntajes = $partidas->map(
            function($partida){
                return collect($partida->toArray())
                ->only(['id','puntaje','jugador']);
            }
        );
        */
        $puntajes = DB::table('partidas')
                        ->select('juego','puntaje')
                        ->where('estado','cerrada')
                        ->selectSub(
                            function($consulta){
                                $consulta->from('jugadores')
                                ->select('apodo')
                                ->whereColumn('jugadores.id','partidas.jugadorId');
                            }
                        ,'apodo')->get();


        return response()->json([
            'mensaje'=>'OK',
            'puntajes'=>$puntajes
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function crear(Request $solicitud)
    {

        $validacion=$solicitud->validate(
            [
                'juego'=>'nullable|in:MEGAMANIA,DRAGONFIRE,ICECLIMBER,GALAGA,retorno',
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

            if (! isset($validacion['juego'])) {
                $validacion['juego'] = $this->elegirJuego();
            }

            $validacion['codigo']=$this->crearCodigo();
            $validacion['jugadorId'] = $id;

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
        $hexChars = '0123456789ABCDEF';

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

    public function verPartida(Request $solicitud){
        /*$validacion=$solicitud->validate(
            [
                'partidaId'=>'required|max:4'
            ]
        );*/

        $id=$solicitud->partidaId;
        if (strlen($id) >4 || strlen($id) < 4) {
            return response()->
            json(
                [
                    'estado'=>'ERROR',
                    'mensaje' => 'Identificador de jugador no válido'
                ],
                400
            );
        }

        $partida = Partida::where('codigo',$id)->where('estado','abierta')->first();
        //var_dump($partida->isEmpty());
        if ($partida) {

            $top3 = Partida::where('juego',$partida->juego)
                ->orderBy('puntaje','desc')
                ->limit(3);
            // Return the item as JSON response
            return response()->json([
                "estado"=>"OK",
                "partida"=>$partida
            ]);
        } else {
            // Return a not found response
            return response()->json([
                'estado'=> 'ERROR',
                'mensaje'=>'Partida No Encontrada'
            ]);
        }

    }

    /**
     * Permite buscar una partida en el sistema
     */
    public function buscarPartida(Request $solicitud){


        $id=$solicitud->partidaId;
        if (strlen($id) >4 || strlen($id) < 4) {
            return response()->
            json(
                [
                    'estado'=>'ERROR',
                    'mensaje' => 'Identificador de jugador no válido'
                ],
                400
            );
        }

        $partida = Partida::where('codigo',$id)->with('jugador')->first();

        if ($partida) {

            $top3 = Partida::where('juego',$partida->juego)
                ->where('estado','cerrada')
                ->orderBy('puntaje','desc')
                ->limit(3)->get();

            /*if ( array_search($partida,$top3,true) ) {
                $esTop = true;
            }
            else{
                $esTop = false;
            }*/

            // Return the item as JSON response
            return response()->json([
                "estado"=>"OK",
                "partida"=>$partida,
                "top3"=>$top3->find($partida) //$esTop
            ]);
        } else {
            // Return a not found response
            return response()->json([
                'estado'=> 'ERROR',
                'mensaje'=>'Partida No Encontrada'
            ]);
        }

    }

    public function finalizarPartida(Request $solicitud){


        $jugadorId = substr($solicitud->jugadorId,3);
        $partidaId = $solicitud->partidaId;
        $puntaje = $solicitud->puntaje;
        //return response()->json(["partida"=>"$partidaId","jugador"=>"$jugadorId"]);


        $partida = Partida::where('codigo',$partidaId)->where('jugadorId',$jugadorId)->where('estado','jugando')->first();

        if ($partida) {

            $partida->estado = "cerrada";
            $partida->puntaje = $puntaje;

            $partida->save();

            $top3 = Partida::where('juego',$partida->juego)
                ->orderBy('puntaje','desc')
                ->limit(3);

            return response()->json(
                [
                    "estado"=>"OK",
                ]
            );
        } else {
            return response()->json([
                'estado'=> 'ERROR',
                'mensaje'=>'Partida No Encontrada'
            ]);
        }

    }


    public function iniciarPartida(Request $solicitud){


        $jugadorId = substr($solicitud->jugadorId,3);
        $partidaId = $solicitud->partidaId;
        //return response()->json(["partida"=>"$partidaId","jugador"=>"$jugadorId"]);


        $partida = Partida::where('codigo',$partidaId)->where('jugadorId',$jugadorId)->where('estado','abierta')->first();

        if ($partida) {

            $partida->estado = "jugando";
            $partida->save();

            return response()->json(
                [
                    "estado"=>"OK"
                ]
            );
        } else {
            return response()->json([
                'estado'=> 'ERROR',
                'mensaje'=>'Partida No Encontrada'
            ]);
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
