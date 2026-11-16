<?php

namespace App\Http\Middleware;

use App\Models\EstadoSac;
use App\Models\SacToken;
use App\Models\Usuario;
use Closure;

/**
 * Autenticación de la app de escritorio "GumaMeter" (rutas /api/sac).
 *
 * Lee el token de "Authorization: Bearer ..." y deja al usuario en
 * $request->attributes ('sac_usuario' y 'sac_token'). No usa la sesión del
 * navegador, así que no interfiere con SesionUnica.
 *  - 401: falta el token, no existe, venció o el usuario inició sesión en otro equipo (sesión única).
 *  - 403: el usuario ya no es SAC o está inactivo (se revocan sus tokens).
 */
class TokenSac
{
    const MSG_TOKEN = 'Tu sesión no es válida o venció. Vuelve a iniciar sesión.';
    const MSG_OTRO_EQUIPO = 'Tu sesión se cerró porque este usuario inició sesión en otro equipo.';
    const MSG_NO_SAC = 'Esta aplicación es solo para usuarios de SAC.';
    const MSG_INACTIVO = 'Tu usuario está inactivo. Habla con tu supervisor.';

    public function handle($request, Closure $next)
    {
        $token = self::tokenDe($request);
        $fila = SacToken::buscar($token);
        if (!$fila) {
            $mensaje = SacToken::desplazado($token) ? self::MSG_OTRO_EQUIPO : self::MSG_TOKEN;
            return response()->json(['mensaje' => $mensaje], 401);
        }

        $usuario = Usuario::find($fila->user_id);
        $motivo = self::motivoSinAcceso($usuario);
        if ($motivo) {
            SacToken::revocarDeUsuario($fila->user_id);
            return response()->json(['mensaje' => $motivo], 403);
        }

        SacToken::tocar($fila->id);
        $request->attributes->set('sac_usuario', $usuario);
        $request->attributes->set('sac_token', $fila);
        return $next($request);
    }

    /**
     * Token de "Authorization: Bearer ...".
     * Con Apache + mod_php y el .htaccess de la raíz (que no reenvía esa cabecera), PHP no la
     * recibe en $_SERVER y bearerToken() viene vacío; por eso se lee también directo de Apache.
     */
    private static function tokenDe($request)
    {
        $token = $request->bearerToken();
        if (!$token && function_exists('getallheaders')) {
            foreach ((array) getallheaders() as $nombre => $valor) {
                if (strcasecmp($nombre, 'Authorization') === 0 && stripos($valor, 'Bearer ') === 0) {
                    $token = trim(substr($valor, 7));
                }
            }
        }
        return $token;
    }

    /** Mensaje de por qué el usuario no puede usar la app, o null si puede. */
    public static function motivoSinAcceso($usuario)
    {
        if (!$usuario || $usuario->activo !== 'S') return self::MSG_INACTIVO;
        if (!EstadoSac::mide($usuario->id_rol)) return self::MSG_NO_SAC;
        return null;
    }
}
