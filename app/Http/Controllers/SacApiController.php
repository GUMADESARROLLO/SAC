<?php

namespace App\Http\Controllers;

use App\Http\Middleware\TokenSac;
use App\Models\EstadoSac;
use App\Models\SacToken;
use App\Models\UsoApp;
use App\Models\Usuario;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * API de la app de escritorio "SAC Estados" (bandeja de Windows). Base: /api/sac.
 *
 *   POST /login   {username, password, dispositivo} -> {token, usuario{id,nombre,username}, estado, desde, ahora}
 *   GET  /estado                                     -> {estado, desde, ahora}
 *   POST /estado  {estado: on|afk|lunch|off}         -> {estado, desde, ahora}
 *   POST /latido                                     -> {estado, desde, ahora}
 *   POST /logout                                     -> {ok: true}
 *   POST /uso     {apps:[{proceso,nombre,segundos}], inactivo} -> {ok: true}
 *
 * Errores, siempre con el campo "mensaje": 401 credenciales o token inválidos, 403 no es SAC
 * o está inactivo, 422 estado no válido, 429 demasiados intentos de inicio de sesión.
 * Las rutas con token pasan por el middleware TokenSac. Los estados se guardan con el mismo
 * modelo que usa el navegador (EstadoSac), así que la web y la app ven siempre lo mismo.
 */
class SacApiController extends Controller
{
    const MAX_INTENTOS = 5;        // intentos fallidos de login por usuario e IP...
    const BLOQUEO_SEG = 60;        // ...en esta ventana

    /** Inicio de sesión: valida usuario, contraseña y rol SAC, y entrega un token para ese dispositivo. */
    public function login(Request $request)
    {
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');
        $dispositivo = mb_substr(trim((string) $request->input('dispositivo')), 0, 100);
        if ($username === '' || $password === '' || $dispositivo === '') {
            return response()->json(['mensaje' => 'Faltan el usuario, la contraseña o el nombre del equipo.'], 422);
        }

        $limite = app(RateLimiter::class);
        $clave = 'sac_login|' . mb_strtolower($username) . '|' . $request->ip();
        try {
            if ($limite->tooManyAttempts($clave, self::MAX_INTENTOS)) {
                $seg = max(1, (int) $limite->availableIn($clave));
                return response()->json(['mensaje' => "Demasiados intentos. Espera $seg segundos y vuelve a intentar."], 429);
            }
        } catch (\Throwable $e) { report($e); }   // sin caché disponible no se bloquea a nadie

        // activo = 'E' es un usuario eliminado: para la app no existe
        $usuario = Usuario::where('username', $username)->where('activo', '<>', 'E')->first();
        if (!$usuario || !Hash::check($password, (string) $usuario->password)) {
            try { $limite->hit($clave, self::BLOQUEO_SEG); } catch (\Throwable $e) { report($e); }
            return response()->json(['mensaje' => 'Usuario o contraseña incorrectos.'], 401);
        }
        $motivo = TokenSac::motivoSinAcceso($usuario);
        if ($motivo) return response()->json(['mensaje' => $motivo], 403);

        try { $limite->clear($clave); } catch (\Throwable $e) { report($e); }
        $token = SacToken::crear($usuario->id, $dispositivo);
        EstadoSac::latido($usuario->id);   // entrar cuenta como señal de vida; no cambia el estado

        return response()->json(array_merge([
            'token'   => $token,
            'usuario' => [
                'id'       => (int) $usuario->id,
                'nombre'   => self::nombrePropio($usuario->nombre),
                'username' => strtoupper((string) $usuario->username),
            ],
        ], $this->vigente($usuario->id)));
    }

    /** Estado vigente, sin contar como señal de vida. */
    public function estado(Request $request)
    {
        return response()->json($this->vigente($this->id($request)));
    }

    /**
     * Cambia el estado: on | afk | lunch | off. Responde el estado que quedó guardado.
     * motivo (opcional) marca los cambios que la app hace sola: "inactividad" (pasó a AFK o a
     * Desconectado por no usar teclado ni mouse) o "regreso" (volvió a En línea tras un AFK automático).
     */
    public function cambiar(Request $request)
    {
        $estado = $request->input('estado');
        if (!is_string($estado) || !in_array($estado, array_merge(EstadoSac::ESTADOS, ['off']), true)) {
            return response()->json(['mensaje' => 'El estado no es válido.'], 422);
        }
        $motivo = $request->input('motivo');
        if ($motivo !== null && !in_array($motivo, EstadoSac::MOTIVOS_AUTO, true)) {
            return response()->json(['mensaje' => 'El motivo no es válido.'], 422);
        }
        EstadoSac::cambiar($this->id($request), $estado, $motivo ?: 'manual');
        return response()->json($this->vigente($this->id($request)));
    }

    /** Señal de vida de la app (cada 60 s): mantiene abierto el tramo actual y devuelve el estado vigente. */
    public function latido(Request $request)
    {
        EstadoSac::latido($this->id($request));
        return response()->json($this->vigente($this->id($request)));
    }

    /** Cierra la sesión de la app: el usuario pasa a Desconectado y el token deja de servir. */
    public function logout(Request $request)
    {
        EstadoSac::cambiar($this->id($request), 'off', 'logout');
        SacToken::revocar($request->attributes->get('sac_token')->id);
        return response()->json(['ok' => true]);
    }

    /**
     * Uso de aplicaciones del último minuto (la app lo envía junto con el latido, en cualquier estado).
     * apps = segundos con cada programa en primer plano; inactivo = segundos sin teclado ni mouse.
     */
    public function uso(Request $request)
    {
        $filas = UsoApp::limpiar($request->input('apps'), $request->input('inactivo'));
        if ($filas === null) return response()->json(['mensaje' => 'El reporte de uso no es válido.'], 422);
        UsoApp::registrar($this->id($request), $filas);
        return response()->json(['ok' => true]);
    }

    private function id(Request $request)
    {
        return (int) $request->attributes->get('sac_usuario')->id;
    }

    /** {estado, desde, ahora} tras pasar a Desconectado a quien lleva demasiado sin señal. */
    private function vigente($userId)
    {
        EstadoSac::cerrarSinSenal();
        $act = EstadoSac::abierto($userId);
        // Por si el barrido general no corrió en esta petición (se limita a una vez cada 20 s)
        if ($act && strtotime($act->visto_en) < time() - EstadoSac::SIN_SENAL_MIN * 60) $act = null;
        return [
            'estado' => $act ? $act->estado : 'off',
            'desde'  => $act ? $act->inicio : null,
            'ahora'  => date('Y-m-d H:i:s'),
        ];
    }

    /** "SAC03 | LUCIA CASTRO" -> "Lucia Castro" (igual que en Equipo SAC). */
    private static function nombrePropio($nombre)
    {
        $t = trim((string) $nombre);
        if (mb_strpos($t, '|') !== false) { $p = array_filter(array_map('trim', explode('|', $t))); $t = end($p) ?: $t; }
        return mb_convert_case(mb_strtolower($t, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
}
