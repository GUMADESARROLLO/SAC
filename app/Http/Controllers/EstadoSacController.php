<?php

namespace App\Http\Controllers;

use App\Models\EstadoSac;
use App\Models\Usuario;
use App\Models\UsoApp;
use App\Exports\ResumenEstadosXlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

/** Estados del equipo SAC: revisión de sesión del navegador y datos para la página Equipo SAC. */
class EstadoSacController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Revisión de sesión que el navegador hace cada minuto. No hace nada por sí misma: el middleware
     * SesionUnica ya respondió 401 si este usuario inició sesión en otro equipo.
     * No cuenta como señal de vida del estado SAC: el estado y su latido son solo de la app de
     * escritorio "SAC Estados" (SacApiController, /api/sac).
     */
    public function ping()
    {
        return response()->noContent();
    }

    /** Tramos del equipo para la página Equipo SAC (solo Admin y SAC Supervisor). */
    public function equipo(Request $request)
    {
        if (!in_array((string) Session::get('rol'), ['1', '2'], true)) {
            return response()->json(['mensaje' => 'No tienes permiso para ver el equipo.'], 403);
        }
        $fecha = function ($v, $defecto) {
            $d = \DateTime::createFromFormat('!Y-m-d', (string) $v);
            return ($d && $d->format('Y-m-d') === $v) ? $v : $defecto;
        };
        $hoy = date('Y-m-d');
        $desde = $fecha($request->query('desde'), $hoy);
        $hasta = $fecha($request->query('hasta'), $hoy);
        if ($desde > $hasta) { [$desde, $hasta] = [$hasta, $desde]; }
        if ((strtotime($hasta) - strtotime($desde)) / 86400 > 92) {
            return response()->json(['mensaje' => 'El rango máximo es de 3 meses.'], 422);
        }
        // Solo los SAC del equipo (usuarios SAC activos); opcionalmente uno solo
        $ids = EstadoSac::equipo()->pluck('id')->all();
        $uno = (int) $request->query('sac');
        if ($uno) $ids = in_array($uno, $ids, true) ? [$uno] : [];

        EstadoSac::cerrarSinSenal();
        return response()->json([
            'ahora'  => date('Y-m-d H:i:s'),
            'desde'  => $desde,
            'hasta'  => $hasta,
            'tramos' => EstadoSac::tramos($desde, $hasta, $ids),
        ]);
    }
    /** Pedidos procesados del periodo para la pestaña Pedidos de Equipo SAC. */
    public function pedidos(Request $request)
    {
        if (!in_array((string) Session::get('rol'), ['1', '2'], true)) {
            return response()->json(['mensaje' => 'No tienes permiso para ver el equipo.'], 403);
        }
        $fecha = function ($v, $defecto) {
            $d = \DateTime::createFromFormat('!Y-m-d', (string) $v);
            return ($d && $d->format('Y-m-d') === $v) ? $v : $defecto;
        };
        $hoy = date('Y-m-d');
        $desde = $fecha($request->query('desde'), $hoy);
        $hasta = $fecha($request->query('hasta'), $hoy);
        if ($desde > $hasta) { [$desde, $hasta] = [$hasta, $desde]; }
        if ((strtotime($hasta) - strtotime($desde)) / 86400 > 92) {
            return response()->json(['mensaje' => 'El rango máximo es de 3 meses.'], 422);
        }
        return response()->json([
            'ahora'   => date('Y-m-d H:i:s'),
            'desde'   => $desde,
            'hasta'   => $hasta,
            'pedidos' => EstadoSac::pedidosProcesados($desde, $hasta, (int) $request->query('sac') ?: null),
        ]);
    }

    /** Resumen de Equipo SAC en Excel (mismo formato que la pestaña Resumen). */
    public function resumenExcel(Request $request)
    {
        if (!in_array((string) Session::get('rol'), ['1', '2'], true)) abort(403);
        $fecha = function ($v, $defecto) {
            $d = \DateTime::createFromFormat('!Y-m-d', (string) $v);
            return ($d && $d->format('Y-m-d') === $v) ? $v : $defecto;
        };
        $lunes = date('Y-m-d', strtotime('monday this week'));
        $desde = $fecha($request->query('desde'), $lunes);
        $hasta = $fecha($request->query('hasta'), date('Y-m-d', strtotime($lunes . ' +6 days')));
        if ($desde > $hasta) { [$desde, $hasta] = [$hasta, $desde]; }
        if ((strtotime($hasta) - strtotime($desde)) / 86400 > 30) $hasta = date('Y-m-d', strtotime($desde . ' +30 days'));

        $equipo = EstadoSac::equipo();
        $uno = (int) $request->query('sac');
        if ($uno) $equipo = $equipo->where('id', $uno)->values();

        EstadoSac::cerrarSinSenal();
        $min = EstadoSac::minutosPorDia($desde, $hasta, $equipo->pluck('id')->all());
        $quien = trim((string) Session::get('name_session'));
        if (mb_strpos($quien, '|') !== false) { $p = array_filter(array_map('trim', explode('|', $quien))); $quien = end($p) ?: $quien; }
        $quien = $quien ? 'por ' . mb_convert_case(mb_strtolower($quien, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') : '';
        $libro = (new ResumenEstadosXlsx($equipo, $min, $desde, $hasta, $quien))->libro();

        $nombre = 'Equipo SAC - Tiempo por estado ' . $desde . ($desde === $hasta ? '' : ' a ' . $hasta) . '.xlsx';
        return response()->streamDownload(function () use ($libro) {
            (new Xlsx($libro))->save('php://output');
        }, $nombre, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'Cache-Control' => 'no-store']);
    }

    /** Uso de aplicaciones del equipo en el rango (pestaña Aplicaciones; solo Admin y SAC Supervisor). */
    public function aplicaciones(Request $request)
    {
        if (!in_array((string) Session::get('rol'), ['1', '2'], true)) {
            return response()->json(['mensaje' => 'No tienes permiso para ver el equipo.'], 403);
        }
        $fecha = function ($v, $defecto) {
            $d = \DateTime::createFromFormat('!Y-m-d', (string) $v);
            return ($d && $d->format('Y-m-d') === $v) ? $v : $defecto;
        };
        $hoy = date('Y-m-d');
        $desde = $fecha($request->query('desde'), $hoy);
        $hasta = $fecha($request->query('hasta'), $hoy);
        if ($desde > $hasta) { [$desde, $hasta] = [$hasta, $desde]; }
        if ((strtotime($hasta) - strtotime($desde)) / 86400 > 92) {
            return response()->json(['mensaje' => 'El rango máximo es de 3 meses.'], 422);
        }
        return response()->json([
            'ahora'      => date('Y-m-d H:i:s'),
            'inactivo'   => UsoApp::INACTIVO,
            'filas'      => UsoApp::reporte($desde, $hasta, EstadoSac::equipo()->pluck('id')->all()),
            'categorias' => UsoApp::categorias(),
        ]);
    }

    /** Asigna la categoría de un programa: trabajo | comunicacion | otros. */
    public function appCategoria(Request $request)
    {
        if (!in_array((string) Session::get('rol'), ['1', '2'], true)) {
            return response()->json(['mensaje' => 'No tienes permiso para clasificar aplicaciones.'], 403);
        }
        $proceso = mb_substr(mb_strtolower(trim((string) $request->input('proceso'))), 0, 120);
        $categoria = (string) $request->input('categoria');
        if ($proceso === '' || $proceso === UsoApp::INACTIVO || !in_array($categoria, UsoApp::CATEGORIAS, true)) {
            return response()->json(['mensaje' => 'La categoría no es válida.'], 422);
        }
        UsoApp::categorizar($proceso, $categoria);
        return response()->json(['ok' => true]);
    }
}
