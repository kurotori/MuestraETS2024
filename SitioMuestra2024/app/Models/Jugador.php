<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function partidasJugadas(){
        return $this->hasMany(Partida::class);
    }


}
