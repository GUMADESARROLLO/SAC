<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Estados del equipo SAC guardados en tbl_estado_log (ver la migración).
 * Cada fila es un tramo En línea / AFK / Descanso; Desconectado es el hueco entre tramos.
 * Todas las horas se calculan en PHP (America/Managua): MySQL está en UTC.
 */
class EstadoSac
{
    const TABLA = 'tbl_estado_log';
    const ESTADOS = ['on', 'afk', 'lunch'];          // 'off' = sin tramo abierto
    const MOTIVOS_AUTO = ['inactividad', 'regreso'];  // cambios que la app de escritorio hace sola
    const SIN_SENAL_MIN = 3;                          // minutos sin señal para pasar a Desconectado
    // Solo los usuarios de tipo SAC (rol 9) miden tiempo: ven el selector y aparecen en Equipo SAC.
    // Admin y SAC_SUP solo consultan los reportes.
    const ROLES_MIDEN = ['9'];
    const ROLES_EQUIPO = ['9'];

    private static function ahora() { return date('Y-m-d H:i:s'); }
    private static function tabla() { return DB::table(self::TABLA); }
    private static function limiteSinSenal() { return date('Y-m-d H:i:s', time() - self::SIN_SENAL_MIN * 60); }

    public static function mide($rol) { return in_array((string) $rol, self::ROLES_MIDEN, true); }

    /** Tramo abierto del usuario (su estado actual) o null si está Desconectado. */
    public static function abierto($userId)
    {
        return self::tabla()->where('user_id', $userId)->whereNull('fin')->orderByDesc('inicio')->first();
    }

    /** Cambia el estado: cierra el tramo abierto (con el motivo $cierre) y, si no es 'off', abre uno nuevo. */
    public static function cambiar($userId, $estado, $cierre = 'manual')
    {
        $ahora = self::ahora();
        DB::transaction(function () use ($userId, $estado, $cierre, $ahora) {
            // Si su tramo ya estaba sin señal (p. ej. la máquina estuvo suspendida), se cierra en la última señal
            self::tabla()->where('user_id', $userId)->whereNull('fin')->where('visto_en', '<', self::limiteSinSenal())
                ->update(['fin' => DB::raw('visto_en'), 'cierre' => 'sin_senal']);
            $act = self::abierto($userId);
            if ($act && $act->estado === $estado) {
                self::tabla()->where('id', $act->id)->update(['visto_en' => $ahora]);
                return;
            }
            self::tabla()->where('user_id', $userId)->whereNull('fin')->update(['fin' => $ahora, 'cierre' => $cierre]);
            if (in_array($estado, self::ESTADOS, true)) {
                self::tabla()->insert(['user_id' => $userId, 'estado' => $estado, 'inicio' => $ahora, 'visto_en' => $ahora]);
            }
        });
    }

    /** Señal de vida de la app de escritorio (POST /api/sac/latido): mantiene abierto el tramo actual (si no lleva ya demasiado sin señal). */
    public static function latido($userId)
    {
        self::tabla()->where('user_id', $userId)->whereNull('fin')->where('visto_en', '<', self::limiteSinSenal())
            ->update(['fin' => DB::raw('visto_en'), 'cierre' => 'sin_senal']);
        self::tabla()->where('user_id', $userId)->whereNull('fin')->update(['visto_en' => self::ahora()]);
    }

    /**
     * Pasa a Desconectado a quien lleva más de SIN_SENAL_MIN minutos sin señal
     * (apagó la máquina, cerró el navegador o perdió la red). El tramo se cierra a la
     * hora de la última señal para no inflar el tiempo. No depende del estado: un AFK
     * con el navegador abierto sigue enviando señal y puede durar horas.
     */
    public static function cerrarSinSenal()
    {
        try {
            if (!Cache::add('sac_estado_barrido', 1, 20)) return; // como mucho una vez cada 20 s
        } catch (\Throwable $e) {}
        self::tabla()->whereNull('fin')->where('visto_en', '<', self::limiteSinSenal())
            ->update(['fin' => DB::raw('visto_en'), 'cierre' => 'sin_senal']);
    }

    /** Tramos que tocan el rango [desde 00:00, hasta 23:59:59]. */
    public static function tramos($desde, $hasta, ?array $userIds = null)
    {
        $ini = $desde . ' 00:00:00';
        $fin = date('Y-m-d', strtotime($hasta . ' +1 day')) . ' 00:00:00';
        $q = self::tabla()
            ->select('user_id', 'estado', 'inicio', 'fin', 'cierre')
            ->where('inicio', '<', $fin)
            ->where(function ($w) use ($ini) { $w->whereNull('fin')->orWhere('fin', '>', $ini); })
            ->orderBy('inicio');
        if ($userIds !== null) $q->whereIn('user_id', $userIds);
        return $q->get();
    }

    /** SAC activos que aparecen en Equipo SAC: [id, nombre (Nombre Propio), username]. */
    public static function equipo()
    {
        return Usuario::where('activo', 'S')->whereIn('id_rol', self::ROLES_EQUIPO)->orderBy('nombre')->get()
            ->map(function ($u) {
                $t = trim((string) $u->nombre);
                if (mb_strpos($t, '|') !== false) { $p = array_filter(array_map('trim', explode('|', $t))); $t = end($p) ?: $t; }
                return [
                    'id'       => (int) $u->id,
                    'nombre'   => mb_convert_case(mb_strtolower($t, 'UTF-8'), MB_CASE_TITLE, 'UTF-8'),
                    'username' => strtoupper((string) $u->username),
                ];
            })->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)->values();
    }

    /**
     * Minutos por estado de cada usuario y día: $r[userId]['AAAA-MM-DD'] = ['on'=>, 'afk'=>, 'lunch'=>, 'curso'=>bool].
     * Los tramos se recortan a cada día; los abiertos cuentan hasta ahora.
     */
    public static function minutosPorDia($desde, $hasta, array $userIds)
    {
        $r = [];
        $ahora = time();
        foreach (self::tramos($desde, $hasta, $userIds) as $t) {
            $a = strtotime($t->inicio);
            $b = $t->fin ? strtotime($t->fin) : $ahora;
            $d = strtotime(date('Y-m-d', $a));
            while ($d < $b) {
                $d1 = strtotime('+1 day', $d);
                $ia = max($a, $d); $ib = min($b, $d1);
                $k = date('Y-m-d', $d);
                if ($ib > $ia && $k >= $desde && $k <= $hasta) {
                    if (!isset($r[$t->user_id][$k])) $r[$t->user_id][$k] = ['on' => 0, 'afk' => 0, 'lunch' => 0, 'curso' => false];
                    $r[$t->user_id][$k][$t->estado] += ($ib - $ia) / 60;
                    if (!$t->fin && $b <= $d1) $r[$t->user_id][$k]['curso'] = true;
                }
                $d = $d1;
            }
        }
        return $r;
    }

    /**
     * Pedidos procesados o cancelados en el rango (por la fecha en que se procesaron).
     * tbl_order: created_at = recibido, updated_at = último cambio de estado (procesado),
     * updated_by = usuario que cambió el estado, status 1 = procesado / 2 = cancelado.
     * created_at y updated_at son texto en hora local; order_total viene como "1,234.56 NIO".
     */
    public static function pedidosProcesados($desde, $hasta, $userId = null)
    {
        $q = DB::connection('mysql_pedido')->table('tbl_order')
            ->select('id', 'code', 'email', 'phone', 'name', 'created_at', 'updated_at', 'order_total', 'status', 'updated_by')
            ->whereIn('status', ['1', '2'])
            ->whereNotNull('updated_by')->where('updated_by', '<>', 0)
            ->whereBetween('updated_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->orderByDesc('updated_at');
        if ($userId) $q->where('updated_by', $userId);
        $filas = $q->limit(20000)->get();

        // Nombre de quien procesó (usuarios del SAC)
        $ids = $filas->pluck('updated_by')->unique()->values()->all();
        $usuarios = Usuario::whereIn('id', $ids)->get()->keyBy('id')->map(function ($u) {
            $t = trim((string) $u->nombre);
            if (mb_strpos($t, '|') !== false) { $p = array_filter(array_map('trim', explode('|', $t))); $t = end($p) ?: $t; }
            return ['nombre' => mb_convert_case(mb_strtolower($t, 'UTF-8'), MB_CASE_TITLE, 'UTF-8'), 'username' => strtoupper((string) $u->username), 'rol' => (string) $u->id_rol];
        });

        return $filas->map(function ($f) use ($usuarios) {
            $rec = strtotime((string) $f->created_at);
            $pro = strtotime((string) $f->updated_at);
            $u = $usuarios[$f->updated_by] ?? null;
            return [
                'id'        => (int) $f->id,
                'code'      => $f->code,
                'cliente'   => trim((string) $f->email),
                'nombre'    => trim((string) $f->phone),
                'ruta'      => $f->name,
                'recibido'  => $rec ? date('Y-m-d H:i:s', $rec) : null,
                'procesado' => $pro ? date('Y-m-d H:i:s', $pro) : null,
                'resp'      => ($rec && $pro && $pro >= $rec) ? round(($pro - $rec) / 60, 1) : null,
                'monto'     => (float) preg_replace('/[^0-9.\-]/', '', (string) $f->order_total),
                'estado'    => (string) $f->status === '2' ? 'cancelado' : 'procesado',
                'sac_id'    => (int) $f->updated_by,
                'sac'       => $u ? $u['nombre'] : 'Usuario #' . $f->updated_by,
                'sac_user'  => $u ? $u['username'] : '',
                'sac_rol'   => $u ? $u['rol'] : '',
            ];
        })->values();
    }

    /** Estado actual y "Mi día" del usuario. */
    public static function miDia($userId)
    {
        $hoy = date('Y-m-d');
        $act = self::abierto($userId);
        return [
            'ahora'  => self::ahora(),
            'estado' => $act ? $act->estado : 'off',
            'desde'  => $act ? $act->inicio : null,
            'tramos' => self::tramos($hoy, $hoy, [$userId])->map(function ($t) {
                return ['estado' => $t->estado, 'inicio' => $t->inicio, 'fin' => $t->fin];
            })->values(),
        ];
    }
}
