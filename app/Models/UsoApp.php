<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * Uso de aplicaciones del equipo SAC (tbl_uso_app y tbl_app_categoria; ver la migración).
 * Lo reporta la app de escritorio cada minuto mientras el usuario está En línea o AFK.
 * Solo se guarda el nombre del programa y los segundos; nunca títulos de ventana ni capturas.
 */
class UsoApp
{
    const TABLA = 'tbl_uso_app';
    const TABLA_CAT = 'tbl_app_categoria';
    const INACTIVO = '(inactivo)';                       // tiempo sin teclado ni mouse
    const CATEGORIAS = ['productivo', 'neutral', 'improductivo'];
    const MAX_SEG = 600;                                 // tope de segundos por reporte (la app envía cada 60 s)
    const MAX_APPS = 40;                                 // tope de programas por reporte

    /**
     * Valida y limpia lo que envía la app. Devuelve [proceso => [nombre, segundos]] o null si no es válido.
     * $apps = [['proceso' => 'chrome.exe', 'nombre' => 'Google Chrome', 'segundos' => 52], ...]
     */
    public static function limpiar($apps, $inactivo)
    {
        if (!is_array($apps) || count($apps) > self::MAX_APPS) return null;
        if (!is_int($inactivo) || $inactivo < 0) return null;
        $filas = [];
        $total = $inactivo;
        foreach ($apps as $a) {
            if (!is_array($a) || !isset($a['proceso'], $a['segundos']) || !is_string($a['proceso']) || !is_int($a['segundos'])) return null;
            $proceso = mb_substr(mb_strtolower(trim($a['proceso'])), 0, 120);
            if ($proceso === '' || $proceso === self::INACTIVO || $a['segundos'] < 1) return null;
            $nombre = isset($a['nombre']) && is_string($a['nombre']) ? mb_substr(trim($a['nombre']), 0, 150) : '';
            if (isset($filas[$proceso])) $filas[$proceso][1] += $a['segundos'];
            else $filas[$proceso] = [$nombre !== '' ? $nombre : $proceso, $a['segundos']];
            $total += $a['segundos'];
        }
        if ($total > self::MAX_SEG) return null;
        if ($inactivo > 0) $filas[self::INACTIVO] = ['Sin actividad', $inactivo];
        return $filas;
    }

    /** Suma al acumulado del día (hora de Managua) lo que devolvió limpiar(). */
    public static function registrar($userId, array $filas)
    {
        if (!$filas) return;
        $fecha = date('Y-m-d');
        DB::transaction(function () use ($userId, $filas, $fecha) {
            foreach ($filas as $proceso => $f) {
                DB::statement(
                    'INSERT INTO ' . self::TABLA . ' (user_id, fecha, proceso, nombre, segundos) VALUES (?, ?, ?, ?, ?) '
                    . 'ON DUPLICATE KEY UPDATE segundos = segundos + VALUES(segundos), nombre = VALUES(nombre)',
                    [$userId, $fecha, $proceso, $f[0], $f[1]]
                );
            }
        });
    }

    /** Segundos por usuario y programa en el rango [desde, hasta] (fechas AAAA-MM-DD). */
    public static function reporte($desde, $hasta, array $userIds)
    {
        return DB::table(self::TABLA)
            ->select('user_id', 'proceso', DB::raw('MAX(nombre) AS nombre'), DB::raw('SUM(segundos) AS segundos'))
            ->whereBetween('fecha', [$desde, $hasta])
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id', 'proceso')
            ->get()
            ->map(function ($f) {
                return ['user_id' => (int) $f->user_id, 'proceso' => $f->proceso, 'nombre' => $f->nombre, 'segundos' => (int) $f->segundos];
            })->values();
    }

    /** Segundos activos y sin actividad de cada usuario por día (para la carga de trabajo). */
    public static function porDia($desde, $hasta, array $userIds)
    {
        return DB::table(self::TABLA)
            ->select('user_id', 'fecha',
                DB::raw("SUM(CASE WHEN proceso = '" . self::INACTIVO . "' THEN 0 ELSE segundos END) AS activo"),
                DB::raw("SUM(CASE WHEN proceso = '" . self::INACTIVO . "' THEN segundos ELSE 0 END) AS inactivo"))
            ->whereBetween('fecha', [$desde, $hasta])
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id', 'fecha')
            ->get()
            ->map(function ($f) {
                return ['user_id' => (int) $f->user_id, 'fecha' => substr((string) $f->fecha, 0, 10), 'activo' => (int) $f->activo, 'inactivo' => (int) $f->inactivo];
            })->values();
    }

    /** [proceso => categoria] de los programas ya clasificados. */
    public static function categorias()
    {
        return DB::table(self::TABLA_CAT)->pluck('categoria', 'proceso');
    }

    public static function categorizar($proceso, $categoria)
    {
        DB::statement(
            'INSERT INTO ' . self::TABLA_CAT . ' (proceso, categoria) VALUES (?, ?) ON DUPLICATE KEY UPDATE categoria = VALUES(categoria)',
            [$proceso, $categoria]
        );
    }
}
