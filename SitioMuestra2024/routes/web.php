<?php

use App\Http\Controllers\JugadorController;
use App\Http\Controllers\PartidaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/estado',
    function(){
        return response()->json([
            "estado"=>"OK",
            "servidor"=>env('APP_NAME'),
            "torneo"=>"Gran Torneo de VideoJuegos - ETS de Melo - Muestra 2024"
        ]);
    }
);

Route::get('/jugadores',
    [JugadorController::class,'verTodos']
);

Route::get('/jugador/ver/{jugadorID}',
    [JugadorController::class,'verJugador']
);

Route::post('/jugador/nuevo',
    [JugadorController::class,'crear']
);

Route::get('/partidas',
    [PartidaController::class,'verTodas']
);

Route::get('/puntajes',
    [PartidaController::class,'puntajes']
);

/*
Route::get('/jugador/ver/{jugadorId}/partidas',
    [JugadorController::]
);
*/

Route::post('/partida/nueva',
    [PartidaController::class,'crear']
);

Route::get('/partida/iniciar/{partidaId}/{jugadorId}',
    [PartidaController::class,'iniciarPartida']
);

Route::get('/partida/finalizar/{partidaId}/{jugadorId}/{puntaje}',
    [PartidaController::class,'finalizarPartida']
);

Route::get('/partida/ver/{partidaId}',
    [PartidaController::class,'verPartida']
);


Route::post('/prueba',
    //[PartidaController::class,'crear']
    [JugadorController::class,'verUltimoJuego']
);

Route::post('/probando',
    function(Request $solicitud){
        return response()->json(["dato"=>$solicitud->idJugador]);
    }
);


Route::redirect('/', '/muestra/registro.html', 301);
