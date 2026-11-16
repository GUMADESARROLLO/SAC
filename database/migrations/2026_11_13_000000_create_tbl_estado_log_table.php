<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Estados del equipo SAC (En línea / AFK / Descanso).
 *
 * Cada fila es un tramo: desde que el usuario marca un estado hasta que lo cambia.
 * "Desconectado" no se guarda como fila: es el hueco entre tramos.
 *  - fin = NULL       -> el tramo sigue abierto (estado actual del usuario).
 *  - visto_en         -> última señal de vida del navegador (cada minuto).
 *  - cierre           -> cómo terminó: manual (cambió de estado), sin_senal (sin señal
 *                        por varios minutos: apagó la máquina o cerró el navegador) o logout.
 * Las horas las pone PHP (America/Managua); no usar NOW() de MySQL, que está en UTC.
 */
class CreateTblEstadoLogTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_estado_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('estado', 10);              // on | afk | lunch
            $table->dateTime('inicio');
            $table->dateTime('fin')->nullable();
            $table->dateTime('visto_en');
            $table->string('cierre', 12)->nullable();  // manual | sin_senal | logout | inactividad | regreso
            $table->index(['user_id', 'fin']);
            $table->index(['inicio']);
            $table->index(['fin', 'visto_en']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_estado_log');
    }
}
