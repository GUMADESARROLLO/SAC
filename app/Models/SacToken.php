<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Tokens de la app de escritorio "GumaMeter" guardados en tbl_sac_token (ver la migración).
 * Sesión única: cada usuario tiene un solo token vigente; al entrar en otro equipo, el anterior deja
 * de servir (igual que SesionUnica en el navegador, pero sin relación con ella).
 * En la tabla solo se guarda el SHA-256 del token.
 */
class SacToken
{
    const TABLA = 'tbl_sac_token';
    const VENCE_DIAS = 30;   // días sin uso para que el token deje de servir

    private static function ahora() { return date('Y-m-d H:i:s'); }
    private static function tabla() { return DB::table(self::TABLA); }
    private static function limite() { return date('Y-m-d H:i:s', time() - self::VENCE_DIAS * 86400); }
    private static function huella($token) { return hash('sha256', (string) $token); }

    private static function claveDesplazado($huella) { return 'sac_token_desplazado_' . $huella; }

    /** Crea el token del usuario para ese equipo, revoca los que tuviera (sesión única) y lo devuelve en claro. */
    public static function crear($userId, $dispositivo)
    {
        $token = Str::random(64);
        $ahora = self::ahora();
        DB::transaction(function () use ($userId, $dispositivo, $token, $ahora) {
            // Los tokens de otros equipos se recuerdan un día para poder decirle a esa app por qué se cerró
            foreach (self::tabla()->where('user_id', $userId)->where('dispositivo', '<>', $dispositivo)->pluck('token') as $huella) {
                try { Cache::put(self::claveDesplazado($huella), 1, now()->addDay()); } catch (\Throwable $e) {}
            }
            self::tabla()->where('user_id', $userId)->delete();
            self::tabla()->where('usado_en', '<', self::limite())->delete();   // limpieza de vencidos
            self::tabla()->insert([
                'user_id' => $userId, 'token' => self::huella($token), 'dispositivo' => $dispositivo,
                'creado_en' => $ahora, 'usado_en' => $ahora,
            ]);
        });
        return $token;
    }

    /** Fila del token si existe y no ha vencido; null en otro caso. */
    public static function buscar($token)
    {
        if (!is_string($token) || $token === '') return null;
        return self::tabla()->where('token', self::huella($token))->where('usado_en', '>=', self::limite())->first();
    }

    /** True si ese token dejó de servir porque el usuario inició sesión en otro equipo. */
    public static function desplazado($token)
    {
        if (!is_string($token) || $token === '') return false;
        try { return (bool) Cache::get(self::claveDesplazado(self::huella($token))); } catch (\Throwable $e) { return false; }
    }

    /** Anota que el token se acaba de usar. */
    public static function tocar($id)
    {
        self::tabla()->where('id', $id)->update(['usado_en' => self::ahora()]);
    }

    public static function revocar($id)
    {
        self::tabla()->where('id', $id)->delete();
    }

    /** Revoca todos los tokens del usuario (p. ej. si lo desactivan o le quitan el rol SAC). */
    public static function revocarDeUsuario($userId)
    {
        self::tabla()->where('user_id', $userId)->delete();
    }
}
