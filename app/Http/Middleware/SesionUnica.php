<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Una sola sesión activa por usuario.
 *
 * Al iniciar sesión se guarda en caché un token por usuario (ver LoginController).
 * Si el mismo usuario entra en otra máquina, el token cambia y la sesión anterior
 * se cierra en su siguiente petición con un aviso en el login.
 */
class SesionUnica
{
    const SESION = 'sac_sesion';
    const MENSAJE = 'Tu sesión se cerró porque este usuario inició sesión en otro equipo.';

    public static function clave($idUsuario)
    {
        return 'sac_sesion_activa_' . $idUsuario;
    }

    // Marca la sesión actual como la única activa del usuario (se llama al iniciar sesión)
    public static function registrar($request, $idUsuario)
    {
        $token = Str::random(40);
        $request->session()->put(self::SESION, $token);
        try { Cache::put(self::clave($idUsuario), $token, now()->addDay()); } catch (\Throwable $e) {}
    }

    // Al cerrar sesión se libera el token, solo si sigue siendo el de esta sesión
    public static function liberar($request, $idUsuario)
    {
        try {
            $mio = $request->session()->get(self::SESION);
            if ($mio && Cache::get(self::clave($idUsuario)) === $mio) Cache::forget(self::clave($idUsuario));
        } catch (\Throwable $e) {}
    }

    public function handle($request, Closure $next)
    {
        if (!Auth::check()) return $next($request);

        $id = Auth::id();
        try {
            $activo = Cache::get(self::clave($id));
        } catch (\Throwable $e) {
            return $next($request); // sin caché disponible no se bloquea a nadie
        }

        $mio = $request->session()->get(self::SESION);
        if (!$mio) {
            // Sesión abierta antes de activar esta regla: la primera en llegar se queda
            if ($activo) return $this->cerrar($request);
            self::registrar($request, $id);
            return $next($request);
        }

        if ($activo && $activo !== $mio) return $this->cerrar($request);

        // Mantiene vivo el token mientras la sesión se usa
        try { Cache::put(self::clave($id), $mio, now()->addDay()); } catch (\Throwable $e) {}
        return $next($request);
    }

    private function cerrar($request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['sesion_cerrada' => true, 'mensaje' => self::MENSAJE], 401);
        }
        return redirect()->route('login', ['sesion' => 'otro'])->with('sesion_cerrada', self::MENSAJE);
    }
}
