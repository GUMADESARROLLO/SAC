<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\GmvPedidos;
use App\Models\GvmVineta;
use App\Models\GvmRecibo;
use Carbon\Carbon;
use DateTime;
use Exception;

use Illuminate\Support\Facades\Date;

class GmvApi extends Model
{  
    public static function Articulos($CODIGO_RUTA){
        $PROYECTO_B = array("F19", "F21", "F22", "F23");
        $Lista = (in_array($CODIGO_RUTA , $PROYECTO_B)) ? '20' : '80' ;

        $EXCENTOS   = array("F02", "F04", "F11", "F20","F18");
        $isExcentos = (in_array($CODIGO_RUTA , $EXCENTOS)) ? true : false;

        $json = array();
        $i=0;
        
        $Lista_Articulos = array();

        $Lotes ="  :0:N/D";

        

        if ($isExcentos) {
            $query = GmvArticulos::where('EXISTENCIA', '>' ,1)->orWhere('ARTICULO', 'like', 'VU%')->orderByRaw('CALIFICATIVO,DESCRIPCION ASC')->get();
            $RutaAsignada = $CODIGO_RUTA;
        } else {
            $Rutas = DB::table('gumadesk.tlb_rutas_asignadas')->where('Ruta', $CODIGO_RUTA)->get()->first();
            $RutaAsignada = $Rutas->Ruta_asignada;
            
            $Result_Articulos = DB::table('gumadesk.tbl_listas_articulos_rutas')->where('Ruta', $RutaAsignada)->where('Lista', $Lista)->get();
            
            foreach ($Result_Articulos as $rec){
                $Lista_Articulos[] = $rec->Articulo;
            }        

            $query = GmvArticulos::whereIn('ARTICULO',$Lista_Articulos)->get();            
        }
        

        
        foreach ($query as $fila){
            $set_img ="SinImagen.png";
            $set_des = "";

            $Info_Articulo = DB::table('ecommerce_android_app.tbl_product')->where('product_sku', $fila["ARTICULO"])->get();

            if ($Info_Articulo->count()) {
                $set_img = $Info_Articulo[0]->product_image;
                $set_des = $Info_Articulo[0]->product_description;
            }

            $qPromo = DB::table('ecommerce_android_app.tbl_news')->where('banner_sku', $fila["ARTICULO"])->get();
            $isPromo = ($qPromo->count() >= 1) ? "S" : "N" ;            
            $Precio_Articulo = (strpos($fila["ARTICULO"], "VU") !== false) ? 1 : $fila['PRECIO_IVA'] ;
            $Existe_Articulo = (strpos($fila["ARTICULO"], "VU") !== false) ? 999 : $fila['EXISTENCIA'] ;

            if (strpos($fila["ARTICULO"], "VU") !== false) {
                $set_des ='
                <!DOCTYPE html>
                    <html>
                    <head>
                        <style type="text/css">
                        .alert-box {
                            color:#555;
                            border-radius:10px;
                            font-family:Tahoma,Geneva,Arial,sans-serif;font-size:11px;
                            padding:10px 36px;
                            margin:10px;
                        }
                        .alert-box span {
                            font-weight:bold;
                            text-transform:uppercase;
                        }
                        .error {
                            border:3px solid #f5aca6;
                        }
                        </style>
                    </head>
                    <body>
                        <div class="alert-box error"><span>Importante: </span>Los Valores de precio y Existencia son informativos.</div>
                    </body>
                </html>';
            }

            $val_viñeta = "C$ 40.00";
            $isPromo ="N";


            $json[$i]['product_id']               = $fila["ARTICULO"];
            $json[$i]['product_name']             = strtoupper($fila['DESCRIPCION']);
            $json[$i]['category_id']              = "20";
            $json[$i]['category_name']            = "Medicina";
            $json[$i]['product_price']            = number_format($Precio_Articulo,2,'.','');
            $json[$i]['product_status']           = "Available";
            $json[$i]['product_image']            = $set_img;
            $json[$i]['product_description']      = $set_des;
            $json[$i]['product_quantity']         = str_replace(',', '', number_format($Existe_Articulo,2));
            $json[$i]['currency_id']              = "105";
            $json[$i]['tax']                      = "0";
            $json[$i]['currency_code']            = "NIO";
            $json[$i]['currency_name']            = "Nicaraguan cordoba oro";
            $json[$i]['product_bonificado']       = $fila["REGLAS"];
            //$json[$i]['product_lotes']            = trim($fila["LOTES"]);
            $json[$i]['product_lotes']            = $Lotes;
            $json[$i]['product_und']              = $fila["UNIDAD_MEDIDA"];
            $json[$i]['CALIFICATIVO']             = $fila["CALIFICATIVO"];
            $json[$i]['ISPROMO']                  = $isPromo. ":" . $val_viñeta . ":" . $RutaAsignada;
            $json[$i]['LAB']                      = $fila["LABORATORIO"];

            $i++;
            
        }

        return $json;    
        
    }

    public static function post_order(Request $request){

        try{
            $code        = $request->input('code');
            $name        = $request->input('name');
            $email       = $request->input('email');
            $phone       = $request->input('phone');
            $address     = $request->input('address');
            $shipping    = $request->input('shipping');
            $order_list  = $request->input('order_list');
            $order_total = $request->input('order_total');
            $comment     = $request->input('comment');
            $player_id   = $request->input('player_id');
            $date        = $request->input('date');
            $server_url  = $request->input('server_url');

            $obj = new GmvPedidos();
            
            $obj->code          = $code;
            $obj->name          = $name;
            $obj->email         = $email;
            $obj->phone         = $phone;
            $obj->address       = $address;
            $obj->shipping      = $shipping;
            $obj->created_at    = $date;
            $obj->order_list    = $order_list;
            $obj->order_total   = $order_total;
            $obj->comment       = $comment;
            $obj->player_id     = $player_id;

            $response = $obj->save();

            if($response){
                return array('Success'=>'Data Inserted Successfully');
            }else {
                return array('Error'=>'Try Again');
            }        
        }catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }
    }

    public static function get_vineta(Request $request){

        $cliente = $request->input('cliente');

        $sql = "SELECT T0.FACTURA,T0.FECHA,T0.ARTICULO,(T0.CANTIDAD - T0.CANT_LIQUIDADA) AS CANTIDAD,T0.VALOR,T0.TOTAL,T0.LINEA  FROM PRODUCCION.dbo.view_MasterVinnetaFacturadas_umk T0 WHERE  T0.CLIENTE='".$cliente."' ORDER BY T0.FACTURA";

        $query = DB::connection('sqlsrv')->select($sql);

        $i=0;
        $json = array();

        foreach($query as $row){
            if ($row->CANTIDAD > 0) {

                $Total = $row->CANTIDAD * $row->VALOR;
    
                $json[$i]['mFactura']       = $row->FACTURA;
                $json[$i]['mFecha']         = date('d/m/Y', strtotime($row->FECHA)); 
                $json[$i]['mVineta']        = $row->ARTICULO;
                $json[$i]['mCantidad']      = number_format($row->CANTIDAD,0);
                $json[$i]['mValor']         = number_format($row->VALOR,0);
                $json[$i]['mTotal']         = number_format($Total,0);
                $json[$i]['mLinea']         = number_format($row->LINEA,0);
                $i++;
            }
        }
        return $json;
    }

    public static function post_order_vineta(Request $request){
        try{
            $ruta           = $request->input('ruta');
            $cod_cliente    = $request->input('cod_cliente');
            $recibo         = $request->input('recibo');
            $name_cliente   = $request->input('name_cliente');
            $address        = $request->input('address');
            $order_list     = $request->input('order_list');
            $order_total    = $request->input('order_total');
            $comment        = $request->input('comment');
            $comment_anul   = "";
            $player_id      = $request->input('player_id');
            $date           = $request->input('date');

            $obj = new GmvVineta();

            $obj->ruta          = $ruta;
            $obj->cod_cliente   = $cod_cliente;
            $obj->recibo        = $recibo;
            $obj->name_cliente  = $name_cliente;
            $obj->address       = $address;
            $obj->created_at    = $date;
            $obj->order_list    = $order_list;
            $obj->order_total   = $order_total;
            $obj->comment       = $comment;
            $obj->comment_anul  = $comment_anul;
            $obj->player_id     = $player_id;

            $response = $obj->save();

            if($response){
                return array('Success'=>'Data Inserted Successfully');
            }else {
                return array('Error'=>'Try Again');
            }
        }catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }
    }

    public static function get_liquidacion_vineta(Request $request){
        
        $ruta = $request->input('ruta');
        $orderBy = $request->input('orderBy');

        $json = array();
        $i = 0;

        $response = GmvVineta::where('ruta', $ruta)->where('status','!=', 3)->orderBy('date_time')->orderBy('status', $orderBy)->get();

        foreach($response as $row){
            $json[$i]['mId']               = $row['id'];
            $json[$i]['mRuta']             = $row['ruta'];
            $json[$i]['mRecibo']           = $row['recibo'];
            $json[$i]['mCod_Cliente']      = $row['cod_cliente'];
            $json[$i]['mName_Cliente']     = $row['name_cliente'];
            $json[$i]['mFecha']            = $row['date_time'];
            $json[$i]['mBenificiario']     = $row['address'];
            $json[$i]['mOrderTotal']       = $row['order_total'];
            $json[$i]['mComentario']       = $row['comment'];
            $json[$i]['mStatus']           = $row['status'];
            $json[$i]['mOrderList']        = $row['order_list'];
            $json[$i]['mComment_anul']     = $row['comment_anul']; 

            $i++;
        }

        return $json;
    }

    public static function del_order_vineta(Request $request){

        $id = $request->input('id');
        $fecha = Carbon::now()->format('Y-m-d H:i:s');

        $response = GmvVineta::where('id', $id)->update(['status' => 3, 'updated_at'=> $fecha]);

        if($response == 1){
            return array('Success'=>'Recibo Anulado');
        }else{
            return array('Error'=>'Try Again');
        }
    }

    public static function post_order_recibo(Request $request){
        try{
            $ruta               = $request->input('ruta');     
            $cod_cliente        = $request->input('cod_cliente'); 
            $recibo             = $request->input('recibo');
            $fecha_recibo       = $request->input('fecha_recibo');
            $name_cliente       = $request->input('name_cliente');
            $order_list         = $request->input('order_list');
            $order_total        = $request->input('order_total');
            $comment            = $request->input('comment');
            $comment_anul       = "";
            $player_id          = $request->input('player_id');
            $date               = $request->input('date');

            $obj = new GmvRecibo();

            $obj->ruta              = $ruta;
            $obj->recibo            = $recibo;
            $obj->fecha_recibo      = $fecha_recibo;
            $obj->cod_cliente       = $cod_cliente;
            $obj->name_cliente      = $name_cliente;
            $obj->created_at        = $date;
            $obj->order_list        = $order_list;
            $obj->order_total       = $order_total;
            $obj->comment           = $comment;
            $obj->comment_anul      = $comment_anul;
            $obj->player_id         = $player_id;

            $reciboExiste = GmvRecibo::where('recibo', $recibo)->where('ruta', $ruta)->whereIn('status', [0,1,4])->get();

            if(count($reciboExiste) != 1){
                $response = $obj->save();

                if($response){
                    return array('Success'=>'Data Inserted Successfully');
                }else {
                    return array('Error'=>'Try Again');
                }
            }else{
                return array('Stop'=>'Existe');
            }

        }catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }


    }

    public static function get_recibos_colector(Request $request){
        $Usuario    = $request->input('usuario');
        $orderBy    = $request->input('orderBy');
        $Desde      = $request->input('desde');
        $Hasta      = $request->input('hasta');

        $i = 0;
        $array = array();

        $response = GmvRecibo::whereBetween('fecha_recibo', [$Desde, $Hasta])->where('ruta', $Usuario)->where('status','!=', 3)->orderBy('id', $orderBy)->get();

        if(count($response) >= 1){
            foreach($response as $row){
                $array[$i]['mId']               = $row['id'];
                $array[$i]['mRuta']             = $row['ruta'];
                $array[$i]['mRecibo']           = $row['recibo'];
                $array[$i]['mCod_Cliente']      = $row['cod_cliente'];
                $array[$i]['mName_Cliente']     = $row['name_cliente'];
                $array[$i]['mFecha']            = $row['date_time'];
                $array[$i]['mBenificiario']     = "----";
                $array[$i]['mOrderTotal']       = $row['order_total'];
                $array[$i]['mComentario']       = $row['comment'];
                $array[$i]['mStatus']           = $row['status'];
                $array[$i]['mOrderList']        = $row['order_list'];
                $array[$i]['mComment_anul']     = $row['comment_anul']; 

                $i++;
            }
        }

        return $array;
    }

    public static function del_recibo_colector(Request $request){

        $id = $request->input('id');
        $date = Carbon::now()->format('Y-m-d H:i:s');

        $response = GmvRecibo::where('id', $id)->update(['status' => 3, 'updated_at'=> $date]);

        if($response == 1){
            return array('Success'=>'Recibo Anulado');
        }else{
            return array('Error'=>'Try Again');
        }
    }

    public static function post_adjunto(Request $request){

    }

    public static function get_recibos_adjuntos(Request $request){
        $id = $request->input('id');

        $i=0;
        $array = array();

        $response = DB::table('gumanet.tbl_order_recibo_adjuntos')->where('id_recibo', $id)->get();

        if(count($response) >= 1){
            foreach($response as $row){
                $array[$i]['mId']               = $row->id;
                $array[$i]['mRecibo']           = $row->id_recibo;
                $array[$i]['mNombreImagen']     = $row->Nombre_imagen;
                $i++;
            }
        }

        return $array;
    }

    public static function get_help(){
        $response = DB::table('ecommerce_android_app.tbl_help')->get();

        $json = array();
        if(count($response) >= 1){
            foreach($response as $row){
                $json[] = $row;
            }
        }

        return $json;
    }

    public static function get_tax_currency(){
        $query = "SELECT c.tax, o.currency_code FROM ecommerce_android_app.tbl_config c, ecommerce_android_app.tbl_currency o WHERE c.currency_id = o.currency_id AND c.id = 1";

        $response = DB::select($query);

        $json = array();

        if(count($response) >= 1){
            foreach($response as $row){
                $json[] = $row;
            }
        }

        return $json;
    }

    public static function get_comentarios(Request $request){

        $orden_code = $request->input('orden_code');
        $response = DB::table('ecommerce_android_app.tbl_comment')->where('orden_code', $orden_code)->get();

        $json = array();
        if(count($response) >= 1){
            foreach($response as $row){
                $json[] = $row;
            }
        }

        return $json;
    }

    public static function get_comentarios_im(Request $request){

        $autor = $request->input('autor');
        $orderBy = $request->input('orderBy');
        $i = 0;
        $json = array();

        $response = DB::table('gumanet.tbl_comentarios')->where('Autor', $autor)->orderBy('Fecha', $orderBy)->get();

        if(count($response) >= 1){
            foreach($response as $row){
                $json[$i]['Titulo']    = $row->Titulo;
                $json[$i]['Contenido'] = $row->Contenido;
                $json[$i]['Fecha']     = $row->Fecha;
                $json[$i]['Autor']     = $row->Autor;
                $json[$i]['Imagen']    = $row->Imagen;
                $i++;
            }
        }

        return $json;
    }

    public static function get_banner(){

        $response = DB::table('ecommerce_android_app.tbl_banner')->where('banner_status','>','0')->orderBy('banner_id', 'DESC')->get();
        $json = array();

        if(count($response) >= 1){
            foreach($response as $row){
                $json[] = $row;
            }
        } else{
            $json[0]['banner_id']            = "0";
            $json[0]['banner_image']         = "SinImagen.png";
            $json[0]['banner_description']   = "";
        }

        return $json;
    }

    public static function get_news(){

        $query = "SELECT banner_id,banner_image,banner_description,created_at FROM ecommerce_android_app.tbl_news where banner_status > 0 order by banner_id DESC";

        $response = DB::select($query);
        $json = array();

        if(count($response) >= 1){
            foreach($response as $row){
                $json[] = $row;
            }
        } else{
            $json[0]['banner_id']            = "0";
            $json[0]['banner_image']         = "SinImagen.png";
            $json[0]['banner_description']   = "";
        }

        return $json;
    }

    public static function category_id(Request $request){
        $id = $request->input('categoria_id');

        $query = "SELECT p.product_id, p.product_name, p.category_id, n.category_name, p.product_price, p.product_status, p.product_image, p.product_description, p.product_quantity, c.currency_id, c.tax, o.currency_code, o.currency_name FROM ecommerce_android_app.tbl_category n, ecommerce_android_app.tbl_product p, ecommerce_android_app.tbl_config c, ecommerce_android_app.tbl_currency o WHERE c.currency_id = o.currency_id AND c.id = 1 AND n.category_id = p.category_id AND n.category_id ='".$id."' ORDER BY p.product_id DESC";

        $response = DB::select($query);
        $json = array();
        if(count($response) >=1 ){
            foreach($response as $row){
                $json[] = $row;
            }
        }

        return $json;
    }

    public static function get_category(){
        $query = "SELECT DISTINCT c.category_id, c.category_name, c.category_image, COUNT(DISTINCT p.product_id) as product_count FROM ecommerce_android_app.tbl_category c LEFT JOIN ecommerce_android_app.tbl_product p ON c.category_id = p.category_id GROUP BY c.category_id ORDER BY c.category_id DESC";

        $response = DB::select($query);

        $json = array();

        if(count($response) >= 1){
            foreach($response as $row){
                $json[] = $row;
            }
        }

        return $json;
    }

    public static function clients_id(Request $request){
        $id = $request->input('vendedor_id');

        $sql ="SELECT T0.*,ISNULL( 0, 0 ) AS SALDO_VINETA  FROM PRODUCCION.dbo.GMV3_MASTER_CLIENTES T0 WHERE T0.VENDEDOR LIKE '%".$id."%' ORDER BY NOMBRE";

        $response = DB::connection('sqlsrv')->select($sql);
        $json = array();
        $i = 0;
        if(count($response) >= 1){
            foreach($response as $row){

                $query = DB::table('ecommerce_android_app.tlb_verificacion')->where('Cliente', $row->CLIENTE)->get();

                $Verificado = (count($query) == 0) ? "N;0.00;0.00" : "S;".$query[0]->Lati.";".$query[0]->Longi ;

                $queryPins = DB::table('ecommerce_android_app.tlb_pins')->where('Cliente', $row->CLIENTE)->get();

                $isPin = (count($queryPins) == 0) ? "N" : "S";
                $isPlan =($row->PLAN_CRECI == 0) ? "N" : "S";
    
                $retVal = ($row->MOROSO == 'S') ? $row->NOMBRE.'MOROSO' : $row->NOMBRE ;

                $json[$i]['CLIENTE']     = $row->CLIENTE;
                $json[$i]['NOMBRE']      = str_replace ( "'", '', $retVal);
                $json[$i]['DIRECCION']   = $row->DIRECCION;
                $json[$i]['DIPONIBLE']   = number_format($row->LIMITE_CREDITO - $row->SALDO,2);
                $json[$i]['LIMITE']      = number_format($row->LIMITE_CREDITO,2);
                $json[$i]['SALDO']       = number_format($row->SALDO,2);
                $json[$i]['MOROSO']      = $row->MOROSO;
                $json[$i]['TELE']        = "Tels. ".$row->TELEFONO1.' / '.$row->TELEFONO2;
                $json[$i]['CONDPA']      = "Cond. Pago: ".$row->CONDICION_PAGO.' Dias';
                $json[$i]['VERIFICADO']  = $Verificado;
                $json[$i]['PIN']         = $isPin;
                $json[$i]['PLAN']         = $isPlan;
                $json[$i]['vineta']       = number_format($row->SALDO_VINETA,2);
                $i++;

            }
        }
        return $json;

    }

    public static function get_perfil_user(Request $request){
        $id = $request->input('id_cliente');
        
        $i = 0;
        $json = array();

        $response = DB::connection('sqlsrv')->select("SELECT * FROM PRODUCCION.dbo.GMV_PERFILES_CLIENTE WHERE CLIENTE='".$id."'");

        foreach($response as $row){
            $json[$i]['NoVencidos']  = number_format($row->NoVencidos,2);
            $json[$i]['Dias30']      = number_format($row->Dias30,2);
            $json[$i]['Dias60']      = number_format($row->Dias60,2);
            $json[$i]['Dias90']      = number_format($row->Dias90,2);
            $json[$i]['Dias120']     = number_format($row->Dias120,2);
            $json[$i]['Mas120']      = number_format($row->Mas120,2);
            $json[$i]['FACT_PEND']   = $row->FACT_PEND;
            $i++;
        }


        return $json;
    }

    public static function product_id(Request $request){

        $id = $request->input('product_id');
        $i = 0;
        $json = array();

        $response = DB::connection('sqlsrv')->select("SELECT * FROM PRODUCCION.dbo.GMV_mstr_articulos WHERE EXISTENCIA > 1 and ARTICULO='".$id."'");

        foreach($response as $row){
            $set_img ="SinImagen.png";
            $set_des = "";

            $query = DB::select("SELECT p.product_image,p.product_description FROM ecommerce_android_app.tbl_product p WHERE p.product_sku= '".$row->ARTICULO."'");

            if(count($query) >=1){
                $set_img = $query[0]->product_image;
                $set_des = $query[0]->product_description;
            }

            $json['product_id']               = $row->ARTICULO;
            $json['product_name']             = utf8_encode($row->DESCRIPCION);
            $json['category_id']              = "20";
            $json['category_name']            = "Medicina";
            $json['product_price']            = number_format($row->PRECIO_IVA,2,'.','');
            $json['product_status']           = "Available";
            $json['product_image']            = $set_img;
            $json['product_description']      = $set_des;
            $json['product_quantity']         = number_format($row->EXISTENCIA,0,'.','');
            $json['currency_id']              = "105";
            $json['tax']                      = "0";
            $json['currency_code']            = "NIO";
            $json['currency_name']            = "Nicaraguan cordoba oro";
            $json['product_bonificado']            = $row->REGLAS;
            $i++;

        }
        
        return $json;
    }

    public static function get_detalle_factura(Request $request){

        $i = 0;
        $json = array();

        $factura = $request->input('factura');
        $response = DB::connection('sqlsrv')->select("SELECT * FROM PRODUCCION.dbo.GMV_FACTURA_DETALLE_HISTORICO WHERE FACTURA='".$factura."' ORDER BY ARTICULO");

        if(count($response) >= 1){
            foreach($response as $row){
                $set_img ="SinImagen.png";
                $set_des = "";

                $query = DB::select("SELECT p.product_image,p.product_description FROM ecommerce_android_app.tbl_product p WHERE p.product_sku= '".$row->ARTICULO."'");
                if(count($query) >=1){
                    $set_img = $query[0]->product_image;
                }

                $json[$i]['ARTICULO']        = $row->ARTICULO;
                $json[$i]['OBSERVACIONES']        = $row->OBSERVACIONES;
                $json[$i]['DESCRIPCION']     = strtoupper($row->DESCRIPCION);;
                $json[$i]['CANTIDAD']        = number_format($row->CANTIDAD,2);
                $json[$i]['IMAGEN']          = $set_img;
                $json[$i]['VENTA']           = $row->VENTA;
                $i++;
            }
        }else{
            $json[$i]['ARTICULO']        = "N/D";
            $json[$i]['DESCRIPCION']     = "N/D";
            $json[$i]['OBSERVACIONES']        = "";
            $json[$i]['IMAGEN']          = "SinImagen.png";
            $json[$i]['CANTIDAD']        = number_format(0.00,2);
            $json[$i]['VENTA']           = number_format(0.00,2);
        }

        return $json;
    }

    public static function last_3m(Request $request){
        $cliente = $request->input('cliente');
        $i = 0;
        $json = array();

        $response = DB::connection('sqlsrv')->select("SELECT * FROM PRODUCCION.dbo.GMV3_hstCompra_3M WHERE Cliente='".$cliente."' ORDER BY Dia");

        foreach($response as $row){
            $set_img ="SinImagen.png";

            $query = DB::select("SELECT p.product_image,p.product_description FROM ecommerce_android_app.tbl_product p WHERE p.product_sku= '".$row->ARTICULO."'");

            if(count($query) >=1){
                $set_img = $query[0]->product_image;
            }

            $json[$i]['ARTICULO']        = $row->ARTICULO;
            $json[$i]['DESCRIPCION']     = strtoupper($row->DESCRIPCION);
            $json[$i]['CANTIDAD']        = number_format($row->CANTIDAD,2);
            $json[$i]['VENTA']           = $row->Venta;
            $json[$i]['IMAGEN']          = $set_img;
            $json[$i]['FECHA']           = $row->Dia;
            $i++;
        }

        return $json;
    }

    public static function get_stat_articulo(Request $request){
        $vendedor = $request->input('vendedor');
        
        $i = 0;
        $json = array();

        $response = DB::connection('sqlsrv')->select("SELECT * FROM PRODUCCION.dbo.GMV_PERFILES_RUTA WHERE VENDEDOR='".$vendedor."'");

        foreach ($response as $row) {
            $json[$i]['NoVencidos']  = number_format($row->NoVencidos,2);
            $json[$i]['Vencidos']    = number_format($row->Vencidos,2);
            $json[$i]['Dias30']      = number_format($row->Dias30,2);
            $json[$i]['Dias60']      = number_format($row->Dias60,2);
            $json[$i]['Dias90']      = number_format($row->Dias90,2);
            $json[$i]['Dias120']     = number_format($row->Dias120,2);
            $json[$i]['Mas120']      = number_format($row->Mas120,2);
            $json[$i]['FACT_PEND']   = $row->FACT_PEND;
            $i++;
        }

        return $json;
    }

    public static function post_rpt_rutas(Request $request){
        $ruta        = $request->input('ruta');
        $desde       = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $request->input('desde'))));
        $hasta       = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $request->input('hasta'))));

        $i = 0;
        $json = array();
        $sql = "SELECT T0.FACTURA,T0.Dia,T0.[Nombre del cliente] AS Cliente,sum(T0.Venta) as Venta FROM Softland.dbo.VtasTotal_UMK T0  WHERE T0.Ruta='".$ruta."' AND  T0.Dia BETWEEN '".$desde."' and '".$hasta."' GROUP BY  T0.FACTURA,T0.Dia,T0.[Nombre del cliente]";

        $response = DB::connection('sqlsrv')->select($sql);
        foreach ($response as $row) {
            $json[$i]['FACTURA']    = $row->FACTURA;
            $json[$i]['FECHA']      = date('d/m/Y', strtotime($row->Dia));
            $json[$i]['CLIENTE']    = $row->Cliente;
            $json[$i]['MONTO']      = str_replace(",", "",number_format($row->Venta,2));
            $i++;
        }

        return $json;

    }

    public static function articulos_sin_facturar(Request $request){
        $cliente = $request->input('cliente');
        $i = 0;
        $json = array();

        $response = DB::connection('sqlsrv')->select("SELECT * FROM PRODUCCION.dbo.GMV_mstr_articulos T1 WHERE  T1.ARTICULO NOT IN (SELECT T0.ARTICULO  FROM PRODUCCION.dbo.GMV3_hstCompra_3M T0 WHERE T0.Cliente='".$cliente."' ) AND EXISTENCIA > 1 ORDER BY CALIFICATIVO ASC");

        foreach($response as $row){
            $set_img ="SinImagen.png";
            $set_des = "";

            $query = DB::select("SELECT p.product_image,p.product_description FROM ecommerce_android_app.tbl_product p WHERE p.product_sku= '".$row->ARTICULO."'");
            if(count($query) >=1){
                $set_img = $query[0]->product_image;
                $set_des = $query[0]->product_description;
            }

            $json[$i]['product_id']               = $row->ARTICULO;
            $json[$i]['product_name']             = strtoupper($row->DESCRIPCION);
            $json[$i]['category_id']              = "20";
            $json[$i]['category_name']            = "Medicina";
            $json[$i]['product_price']            = number_format($row->PRECIO_IVA,2,'.','');
            $json[$i]['product_status']           = "Available";
            $json[$i]['product_image']            = $set_img;
            $json[$i]['product_description']      = $set_des;
            $json[$i]['product_quantity']         = str_replace(',', '', number_format($row->EXISTENCIA,2));
            $json[$i]['currency_id']              = "105";
            $json[$i]['tax']                      = "0";
            $json[$i]['currency_code']            = "NIO";
            $json[$i]['currency_name']            = "Nicaraguan cordoba oro";
            $json[$i]['product_bonificado']       = $row->REGLAS;
            $json[$i]['product_lotes']            = trim($row->LOTES);
            $json[$i]['product_und']              = $row->UNIDAD_MEDIDA;
            $json[$i]['CALIFICATIVO']              = $row->CALIFICATIVO;
            $i++;
        }

        return $json;
    }

    public static function post_usuario(Request $request){
        $myString = $request->input('post_usuario');

        $myString = substr ($myString, 0, strlen($myString) - 1);

        $myString = $str = substr($myString, 1);

        $porciones = explode("@", $myString);

        $json = array();

        // change username to lowercase
        $username = strtolower($porciones[0]);

        $KeysSecret = "A7M";

        //encript password to sha256
        $password = hash('sha256',$KeysSecret.$porciones[1]);

        // get data from user table

        $response = DB::select("SELECT username,Activo,Name,Telefono,email FROM ecommerce_android_app.tbl_admin WHERE username = ? AND password = ?", array($username, $password));

        if(count($response) >= 1){
            foreach($response as $row){
                if($row->Activo=="S"){
                    $json['result'][] = array(
                        'name' => strtoupper($row->username),
                        'FullName' => strtoupper($row->Name),
                        'Tele' => strtoupper($row->Telefono),
                        'Correo' => strtoupper($row->email),
                        'success' => '1');
                }else{
                    $json['result'][] = array('msg' => 'Account disabled', 'success' => '2');
                }
            }
        }else{
            $json['result'][] = array('msg' => 'Login failed', 'success' => '0');
        }

        return $json;
    }

    public static function get_nc(Request $request){
       /* $cliente = $request->input('cliente');

        $json = array();
        $i = 0;

        $response = DB::connection('sqlsrv')->select("SELECT T0.CLIENTE,T0.DOCUMENTO,T0.FECHA,T0.SALDO_LOCAL,T0.APLICACION,T0.VENDEDOR FROM Softland.dbo.APK_CxC_DocVenxCL T0  WHERE T0.CLIENTE='".$cliente."' and T0.TIPO='N/C'");

        foreach ($response as $row) {
            $json[$i]['DOCUMENTO']      = $row->DOCUMENTO;
            $json[$i]['FECHA']          = date('d/m/Y', strtotime($row->FECHA));
            $json[$i]['SALDO_LOCAL']    = str_replace(",", "", number_format($row->SALDO_LOCAL,2));
            $json[$i]['APLICACION']     = $row->APLICACION;
            $json[$i]['VENDEDOR']       = $row->VENDEDOR;
            $i++;
        }

        return $json;*/
    }

    public static function get_stat_ruta(Request $request){
        $anio = $_GET['sAnno'];
        $mes  = $_GET['sMes'];
        $Ruta = $_GET['ruta'];

        $i=0;
        $json = array();
        $fecha       = date('Y-m-d',strtotime(str_replace('/', '-',($anio.'-'.$mes.'-01'))));

        $periodo = DB::connection('sqlsrv')->select("SELECT IdPeriodo FROM DESARROLLO.dbo.metacuota_GumaNet WHERE Fecha='".$fecha."' AND IdCompany='1' ");

        $meta_unidades = DB::connection('sqlsrv')->select("SELECT Sum(Meta) as Meta FROM DESARROLLO.dbo.gn_cuota_x_productos WHERE IdPeriodo='".$periodo[0]->IdPeriodo."' AND CodVendedor='".$Ruta."' ");

        $meta_valor = DB::connection('sqlsrv')->select("SELECT Sum(val) as val FROM DESARROLLO.dbo.gn_cuota_x_productos WHERE IdPeriodo='".$periodo[0]->IdPeriodo."' AND CodVendedor='".$Ruta."' ");

        $response = DB::connection('sqlsrv')->select("EXEC PRODUCCION.dbo.Ventas_Rutas ".$mes.", ".$anio);

        $found_key = array_search($Ruta, array_column($response, 'Ruta'));

        $Meta_Monto     = $response[$found_key]->Monto;
        $Meta_Cantidad  = $response[$found_key]->Cantidad;

        $json[$i]['mVentaReal']       = str_replace(",", "",number_format($Meta_Monto,2));
        $json[$i]['mMetaVenta']      = str_replace(",", "",number_format($meta_valor[0]->val,2));
        $json[0]['mVentaDif']        = ($Meta_Monto==0) ? "100.00" : number_format(((floatval($Meta_Monto)/floatval($meta_valor[0]->val))*100),2);


        $json[$i]['mVntCanti']        = str_replace(",", "",number_format($meta_unidades[0]->Meta,2));
        $json[$i]['mVntCantiReal']    = str_replace(",", "",number_format($Meta_Cantidad,2));
        $json[0]['mVntCantiDif']     = ($meta_unidades[0]->Meta==0) ? "100.00" : number_format(((floatval($Meta_Cantidad)/floatval($meta_unidades[0]->Meta))*100),2);


        return $json;
    }
}