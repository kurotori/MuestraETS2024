<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function jugador():BelongsTo
    {
        return $this->belongsTo(Jugador::class,'jugadorId');
    }


}
