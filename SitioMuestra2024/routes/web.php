<?php

use App\Http\Controllers\JugadorController;
use App\Http\Controllers\PartidaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
    [PartidaController::class,'index']
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

Route::get('/partida/ver/{partidaId}',
    [PartidaController::class,'verPartida']
);


Route::post('/prueba',
    //[PartidaController::class,'crear']
    [JugadorController::class,'verUltimoJuego']
);
