<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Vendedor;
use App\Models\UsuarioRutas;
use App\Models\Roles;

class UsuarioController extends Controller {
    public function __construct()
    {
        //$this->middleware('auth');
    }
    public function getUsuarios()
    {  
        $Usuarios   = Usuario::getUsuarios();
        $Roles      = Roles::getRoles();
        return view('Usuario.Home', compact('Usuarios','Roles'));
    }
    public function SaveUsuario(Request $request)
    {
        $response = Usuario::SaveUsuario($request);
        return response()->json($response);
    }
    public function AsignarRuta(Request $request)
    {
        $response = UsuarioRutas::AsignarRuta($request);
        return response()->json($response);
    }
    public function DeleteUsuario(Request $request)
    {
        $response = Usuario::DeleteUsuario($request);
        return response()->json($response);
    }
    public function RemoverRutaAsignada(Request $request)
    {
        $response = UsuarioRutas::Remover($request);
        return response()->json($response);
    }
    public function getRutas()
    {
        $Rutas = Vendedor::getVendedor();
        return response()->json($Rutas);
    }
}  