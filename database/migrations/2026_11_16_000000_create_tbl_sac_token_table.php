<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tokens de la app de escritorio "GumaMeter" (API /api/sac).
 *
 * Un solo token vigente por usuario (sesión única de la app). No tiene relación con la sesión del navegador,
 * así que no choca con la regla de sesión única (middleware SesionUnica).
 *  - token    -> SHA-256 del token; el token en claro solo lo tiene la app.
 *  - usado_en -> última petición hecha con el token; sin uso por 30 días, vence.
 * Las horas las pone PHP (America/Managua); no usar NOW() de MySQL, que está en UTC.
 */
class CreateTblSacTokenTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_sac_token', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('token', 64)->unique();
            $table->string('dispositivo', 100);
            $table->dateTime('creado_en');
            $table->dateTime('usado_en');
            $table->index(['user_id', 'dispositivo']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_sac_token');
    }
}
