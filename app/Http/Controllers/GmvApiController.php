<?php
namespace App\Http\Controllers;
use App\Models\GmvApi;
use App\Models\Comision;
use GMP;
use Illuminate\Http\Request;


class GmvApiController extends Controller{

    public function Articulos(Request $request)
    {   
        $obj = GmvApi::Articulos($request);
        
        return response()->json($obj);
    }
    
    public function post_order(Request $request){
        $obj = GmvApi::post_order($request);

        return response()->json($obj);
    }

    public function get_shipping(){
        $obj = GmvApi::get_shipping();

        return response()->json($obj);
    }

    public function get_vineta(Request $request){
        $obj = GmvApi::get_vineta($request);

        return response()->json($obj);
    }

    public function post_order_vineta(Request $request){
        $obj = GmvApi::post_order_vineta($request);

        return response()->json($obj);
    }

    public function get_liquidacion_vineta(Request $request){
        $obj = GmvApi::get_liquidacion_vineta($request);

        return response()->json($obj);
    }

    public function del_order_vineta(Request $request){
        $obj = GmvApi::del_order_vineta($request);

        return response()->json($obj);
    }

    public function post_order_recibo(Request $request){
        $obj = GmvApi::post_order_recibo($request);

        return response()->json($obj);
    }

    public function get_recibos_colector(Request $request){
        $obj = GmvApi::get_recibos_colector($request);

        return response()->json($obj);
    }

    public function del_recibo_colector(Request $request){
        $obj = GmvApi::del_recibo_colector($request);

        return response()->json($obj);
    }

    public function get_recibos_adjuntos(Request $request){
        $obj = GmvApi::get_recibos_adjuntos($request);

        return response()->json($obj);
    }

    public function get_help(){
        $obj = GmvApi::get_help();
        return response()->json($obj);
    }

    public function get_tax_currency(){
        $obj = GmvApi::get_tax_currency();
        return json_encode($obj);
    }

    public function get_comentarios(Request $request){
        $obj = GmvApi::get_comentarios($request);
        return response()->json($obj);
    }

    public function get_comentarios_im(Request $request){
        $obj = GmvApi::get_comentarios_im($request);
        return response()->json($obj);
    }
    
    public function get_banner(){
        $obj = GmvApi::get_banner();
        return response()->json($obj);
    }

    public function get_news(){
        $obj = GmvApi::get_news();
        return response()->json($obj);
    }

    public function category_id(Request $request){
        $obj = GmvApi::category_id($request);
        return response()->json($obj);
    }

    public function get_category(){
        $obj = GmvApi::get_category();

        return response()->json($obj);
    }

    public function clients_id(Request $request){
        $obj = GmvApi::clients_id($request);
        
        return response()->json($obj);
    }

    public function get_perfil_user(Request $request){
        $obj = GmvApi::get_perfil_user($request);
        
        return response()->json($obj);
    }

    public function product_id(Request $request){
        $obj = GmvApi::product_id($request);
        
        return response()->json($obj);
    }

    public function get_detalle_factura(Request $request){
        $obj = GmvApi::get_detalle_factura($request);

        return response()->json($obj);
    }

    public function last_3m(Request $request){
        $obj = GmvApi::last_3m($request);

        return response()->json($obj);
    }

    public function get_stat_articulo(Request $request){
        $obj = GmvApi::get_stat_articulo($request);

        return response()->json($obj);
    }

    public function post_rpt_rutas(Request $request){
        $obj = GmvApi::post_rpt_rutas($request);

        return response()->json($obj);
    }

    public function articulos_sin_facturar(Request $request){
        $obj = GmvApi::articulos_sin_facturar($request);

        return response()->json($obj);
    }

    public function post_usuario(Request $request){
        $obj = GmvApi::post_usuario($request);

        return response()->json($obj);
    }

    public function get_nc(Request $request){
        $obj = GmvApi::get_nc($request);

        return response()->json($obj);
    }

    public function get_stat_ruta(Request $request){
        $obj = GmvApi::get_stat_ruta($request);

        return response()->json($obj);
    }

    public function post_report(Request $request){
        $obj = GmvApi::post_report($request);

        return response()->json($obj);
    }

    public function push_pin(Request $request){
        $obj = GmvApi::push_pin($request);

        return response()->json($obj);
    }

    public function stac_recup(Request $request){
        $obj = GmvApi::stac_recup($request);

        return response()->json($obj);
    }

    public function get_history_lotes(Request $request){
        $obj = GmvApi::get_history_lotes($request);

        return response()->json($obj);
    }

    public function plan_crecimiento(Request $request){
        $obj = GmvApi::plan_crecimiento($request);

        return response()->json($obj);
    }

    public function post_historico_factura(Request $request){
        $obj = GmvApi::post_historico_factura($request);

        return response()->json($obj);
    }

    public function recibo_anular(Request $request){
        $obj = GmvApi::recibo_anular($request);

        return response()->json($obj);
    }

    public function post_adjunto(Request $request){
        $obj = GmvApi::post_adjunto($request);

        return response()->json($obj);
    }
    
    public function post_update_datos(Request $request){
        $obj = GmvApi::post_update_datos($request);

        return response()->json($obj);
    }

    public function post_verificacion(Request $request){
        $obj = GmvApi::post_verificacion($request);

        return response()->json($obj);
    }

    public function getcomision($Ruta,$nMonth,$nYear)
    {
        $SalarioBasico  = 5000;

        // REGISTRA LA VISITA DEL VENDEDOR AL MODULO DE COMISIONES
        \Log::channel('Comisiones')->info("La Ruta ".$Ruta." ENTRO AL MODULO DE COMISIONES");

        $Comision[0] = Comision::CalculoCommision($Ruta,$nMonth,$nYear,$SalarioBasico);
        return response()->json($Comision);
        
    }
}