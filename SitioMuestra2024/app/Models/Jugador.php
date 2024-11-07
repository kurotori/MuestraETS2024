<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jugador extends Model
{
    //
    protected $table="jugadores";
    protected $primaryKey="id";

    protected $fillable=[
        'nombre',
        'apellido',
        'apodo',
        'contacto'
    ];

    public function partidasJugadas(): HasMany
    {
        //$partidas=Partida
        return $this->hasMany(Partida::class,'jugadorId');
    }

}
