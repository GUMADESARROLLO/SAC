<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Vendedor;
use App\Models\UsuarioRutas;
use App\Models\Roles;
use Session;

class UsuarioController extends Controller {
    public function __construct()
    {
        // getRutas se deja abierta por si la consume otra aplicación; el resto exige sesión.
        $this->middleware('auth')->except('getRutas');
    }

    /** Solo Admin (1) y SAC_SUP (2) administran usuarios. */
    private function puedeAdministrar()
    {
        return in_array((string) Session::get('rol'), ['1', '2'], true);
    }

    public function getUsuarios()
    {
        $Usuarios = Usuario::getUsuarios();
        $Roles    = Roles::getRoles();

        // Rutas disponibles: las de los vendedores + cualquier ruta ya asignada que no esté en esa lista
        $Rutas = Vendedor::getVendedor()->pluck('VENDEDOR')->map(function ($r) { return strtoupper(trim($r)); });
        foreach ($Usuarios as $u) {
            foreach ($u->Detalles as $d) {
                $Rutas->push(strtoupper(trim($d->RUTA)));
            }
        }
        $Rutas = $Rutas->filter()->unique()->sort(SORT_NATURAL)->values();

        return view('Usuario.Home', compact('Usuarios', 'Roles', 'Rutas'));
    }

    /**
     * Equipo SAC: panel en vivo y reportes de estados.
     * Vista previa: solo se leen los nombres de los SAC; estados, tiempos y pedidos se simulan en el navegador.
     */
    public function getEquipoSac()
    {
        if (!$this->puedeAdministrar()) {
            return redirect()->route('Home');
        }
        $Sacs = Usuario::getUsuarios()
            ->filter(function ($u) { return in_array((string) $u->id_rol, ['2', '9'], true) && $u->activo !== 'N'; })
            ->map(function ($u) {
                $t = trim((string) $u->nombre);
                if (mb_strpos($t, '|') !== false) { $p = array_filter(array_map('trim', explode('|', $t))); $t = end($p) ?: $t; }
                return [
                    'id'       => $u->id,
                    'nombre'   => mb_convert_case(mb_strtolower($t, 'UTF-8'), MB_CASE_TITLE, 'UTF-8'),
                    'username' => strtoupper((string) $u->username),
                ];
            })->values();

        return view('EquipoSac.Home', compact('Sacs'));
    }

    public function SaveUsuario(Request $request)
    {
        if (!$this->puedeAdministrar()) {
            return response()->json(['ok' => false, 'mensaje' => 'No tienes permiso para administrar usuarios.'], 403);
        }
        return DB::transaction(function () use ($request) {
            $res = Usuario::SaveUsuario($request);
            if (!empty($res['ok']) && $request->has('rutas_enviadas')) {
                UsuarioRutas::Sincronizar($res['id'], (array) $request->input('rutas', []));
            }
            return response()->json($res, empty($res['ok']) ? 422 : 200);
        });
    }

    public function AsignarRuta(Request $request)
    {
        if (!$this->puedeAdministrar()) {
            return response()->json(['ok' => false], 403);
        }
        $response = UsuarioRutas::AsignarRuta($request);
        return response()->json($response);
    }

    public function DeleteUsuario(Request $request)
    {
        if (!$this->puedeAdministrar()) {
            return response()->json(['ok' => false, 'mensaje' => 'No tienes permiso para administrar usuarios.'], 403);
        }
        $res = DB::transaction(function () use ($request) { return Usuario::DeleteUsuario($request); });
        return response()->json($res, empty($res['ok']) ? 422 : 200);
    }

    public function RemoverRutaAsignada(Request $request)
    {
        if (!$this->puedeAdministrar()) {
            return response()->json(['ok' => false], 403);
        }
        $response = UsuarioRutas::Remover($request);
        return response()->json($response);
    }

    public function getRutas()
    {
        $Rutas = Vendedor::getVendedor();
        return response()->json($Rutas);
    }
}
