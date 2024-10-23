<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('codigo',6);

            $table->enum('juego',
                ['MEGAMANIA', 'DRAGONFIRE','ICECLIMBER','GALAGA'])
                ->nullable();

            $table->enum('estado',
                ['abierta','jugando','cerrada'])
                ->default('abierta');

            $table->integer('puntaje')->default(0);

            $table->foreignId('jugadorId')->constrained('jugadores')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
