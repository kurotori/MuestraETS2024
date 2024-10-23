<?php

use App\Http\Controllers\JugadorController;
use App\Http\Controllers\PartidaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

Route::post('/partida/nueva',
[PartidaController::class,'crear']
);


Route::post('/prueba',
    [PartidaController::class,'crear']
);
