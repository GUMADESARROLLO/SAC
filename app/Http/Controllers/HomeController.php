<?php
namespace App\Http\Controllers;
use App\Models\Inventario;
use App\Models\Clientes;
use App\Models\Vendedor;
use App\Models\Liquidacion_a_6meses;
use App\Models\Liquidacion_a_12meses;
use App\Models\ClientesHistorico3M;
use App\Models\ClientesMora;
use App\Models\ClientesHistoricoFactura;
use App\Models\NivelPrecio;
use App\Models\Bodega;
use App\Models\ArticuloFavoritos;
use Illuminate\Http\Request;
use App\Models\Lotes;

class HomeController extends Controller {
    public function __construct()
    {
        //$this->middleware('auth');
    }    

    public function getHome()
    {  
        return view('Principal.Home');         
    }

    public function getArticuloFavorito()
    {  
        return view('Principal.ArticuloFavorito');         
    }

    public function getData()
    {
        $dtaHome[] = array(
            'Inventario'    => Inventario::getArticulosFavoritos(),
            'Liq6Meses'     => Liquidacion_a_6meses::getArticulos(),
            'Liq12Meses'    => Liquidacion_a_12meses::getArticulos(),
            'Clientes'      => Clientes::getClientes(),
            'Vendedor'      => Vendedor::getVendedor(),            
        );
        
        return response()->json($dtaHome);
    }
    public function getArticulosFavoritos()
    {
        $dtaHome[] = array(
            'ArticulosFav'      => Inventario::getArticulosFavoritos(),
            'Inventario'        => Inventario::getArticulosSinFav(),
        );
        
        return response()->json($dtaHome);
    }
    public function AddFavs(Request $request)
    {
        $response = ArticuloFavoritos::AddFavs($request);
        return response()->json($response);
    }
    public function getDataCliente($idCliente)
    {

        $dtaCliente[] = array(
            'InfoCliente'                   => Clientes::where('CLIENTE', $idCliente)->get(),
            'Historico3M'                   => ClientesHistorico3M::where('CLIENTE', $idCliente)->get(),
            'ClienteMora'                   => ClientesMora::where('CLIENTE', $idCliente)->get(),
            'ClientesHistoricoFactura'      => ClientesHistoricoFactura::where('COD_CLIENTE', $idCliente)->get(),            
            'ArticulosNoFacturado'          => Inventario::whereNotIn('ARTICULO', function($q)  use ($idCliente){
                                                    $q->select('ARTICULO')->from('PRODUCCION.dbo.GMV3_hstCompra_3M')->where("CLIENTE", $idCliente);
                                                })->get(),            
        );

        return response()->json($dtaCliente);
    }
    public function getDataArticulo($idArticulo)
    {
        $dtaArticulo[] = array(
            'InfoArticulo'          => Inventario::where('ARTICULO', $idArticulo)->get(),
            'NivelPrecio'           => NivelPrecio::getNivelPrecio($idArticulo),
            'Bodega'                => Bodega::getBodega($idArticulo),
        );

        return response()->json($dtaArticulo);
    }

    public function getLotes($BODEGA,$ARTICULO)
    {
        $LOTES = Lotes::getLotes($BODEGA,$ARTICULO);
        return response()->json($LOTES);
    }
}