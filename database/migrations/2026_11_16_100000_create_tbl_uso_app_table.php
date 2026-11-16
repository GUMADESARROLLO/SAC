<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Uso de aplicaciones que reporta la app de escritorio "GumaMeter" (pestaña Aplicaciones de Equipo SAC).
 *
 * tbl_uso_app: segundos acumulados por usuario, día y programa. Solo se guarda el nombre del
 * programa (p. ej. chrome.exe / "Google Chrome"); nunca títulos de ventana, páginas ni capturas.
 *  - proceso = '(inactivo)' -> tiempo sin teclado ni mouse (no es un programa).
 *  - fecha   -> la pone PHP (America/Managua) al recibir el reporte.
 * tbl_app_categoria: categoría que un supervisor le asigna a cada programa (productivo | neutral | improductivo).
 */
class CreateTblUsoAppTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_uso_app', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->date('fecha');
            $table->string('proceso', 120);
            $table->string('nombre', 150);
            $table->unsignedInteger('segundos')->default(0);
            $table->unique(['user_id', 'fecha', 'proceso']);
            $table->index(['fecha']);
        });

        Schema::create('tbl_app_categoria', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('proceso', 120)->primary();
            $table->string('categoria', 20);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_app_categoria');
        Schema::dropIfExists('tbl_uso_app');
    }
}
