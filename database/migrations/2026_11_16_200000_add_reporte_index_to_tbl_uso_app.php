<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Índice para los reportes de la pestaña Aplicaciones (UsoApp::reporte y UsoApp::porDia).
 *
 * Ambos filtran por rango de fechas y agrupan por usuario; con este índice MySQL resuelve la
 * consulta sin leer las filas de la tabla. Medido con 300 usuarios y 120 días (720 000 filas):
 * "por día" de 31 días baja de ~1.7 s a ~0.55 s y el reporte de 31 días de ~1.4 s a ~1.1 s.
 * Reemplaza al índice simple por fecha, que queda cubierto por este.
 */
class AddReporteIndexToTblUsoApp extends Migration
{
    public function up()
    {
        Schema::table('tbl_uso_app', function (Blueprint $table) {
            $table->index(['fecha', 'user_id', 'proceso', 'segundos', 'nombre'], 'tbl_uso_app_reporte_index');
        });
        Schema::table('tbl_uso_app', function (Blueprint $table) {
            $table->dropIndex('tbl_uso_app_fecha_index');
        });
    }

    public function down()
    {
        Schema::table('tbl_uso_app', function (Blueprint $table) {
            $table->index(['fecha']);
        });
        Schema::table('tbl_uso_app', function (Blueprint $table) {
            $table->dropIndex('tbl_uso_app_reporte_index');
        });
    }
}
