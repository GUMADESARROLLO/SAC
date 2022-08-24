<?php
namespace App\Http\Controllers;
use App\Models\ArticulosUMK;
use App\Models\Clientes;
use App\Models\Vendedor;

class HomeController extends Controller {
    public function __construct()
    {
        //$this->middleware('auth');
    }    

    public function getHome()
    {  
        $Productos      = ArticulosUMK::getArticulos();
        $Clientes       = Clientes::getClientes();
        $Vendedor       = Vendedor::getVendedor();
        return view('Principal.Home', compact('Productos','Clientes'));         
    }
}