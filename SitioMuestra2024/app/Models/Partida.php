<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    //
    protected $table="partidas";
    protected $primaryKey="id";

    protected $fillable=[
        'codigo',
        'juego',
        'puntaje',
        'estado',
        'jugadorId'
    ];

    public function jugador(){
        return $this->belongsTo(Jugador::class);
    }


}
