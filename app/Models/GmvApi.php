<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\GmvPedidos;
use App\Models\GvmVineta;
use App\Models\GvmRecibo;
use App\Models\GmvComentario;
use Carbon\Carbon;
use DateTime;
use Exception;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class GmvApi extends Model
{  
    public static function Articulos(Request $request){

        $CODIGO_RUTA = $request->input('Ruta');

        $PROYECTO_B = array("F19", "F21", "F22", "F23");
        $Lista = (in_array($CODIGO_RUTA , $PROYECTO_B)) ? '20' : '80' ;

        $EXCENTOS   = array("F02", "F04", "F11", "F20","F18","F19");
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
            $json[$i]['img_url']                  = Storage::temporaryUrl('product/'.$set_img, now()->addMinutes(5));
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
            $obj->created_at    = $date;
            $obj->address       = $address;
            $obj->shipping      = $shipping;
            $obj->order_list    = $order_list;
            $obj->order_total   = $order_total;
            $obj->comment       = $comment;
            $obj->player_id     = $player_id;

            $response = $obj->save();

            if($response){
                return 'Data Inserted Successfully';
            }else {
                return array('Error'=>'Try Again');
            }        
        }catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }
    }

    public static function get_shipping(){
        $query = "SELECT * FROM ecommerce_android_app.tbl_shipping ORDER BY shipping_id ASC";
       
        $response = DB::select($query);
    
        $set = array();
        if(count($response) >= 1) {
            foreach($response as $link){
                $set['result'][] = $link;
            }
        }

        return $set;

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
        $orderBy = $request->input('OrderBy');

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
        $orderBy    = $request->input('OrderBy');
        $Desde      = $request->input('Desde');
        $Hasta      = $request->input('Hasta');

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
        $image_64 = $request->imagenes;  
        $Id_Recibo  = $request->Id_Recibo;
        $id_img = time() . '-' . rand(0, 99999);
        $imageName = $Id_Recibo. " - ". $id_img .".png";
        
        Storage::disk('s3')->put('Adjuntos-Recibos/'.$imageName, base64_decode($image_64));

        $query = "INSERT INTO tbl_order_recibo_adjuntos (id_recibo,Nombre_imagen) VALUES ('$Id_Recibo','$imageName')";

        $Result = DB::connection('mysql_gumanet')->select($query);

        if ($Result) {
            return array('Success'=>'Adjunto Guardado');
        } else {
            return array('Error'=>'Try Again');
        }
    }

    public static function get_recibos_adjuntos($Recibo){ 
        $i=0;
        $array = array();

        $response = DB::connection('mysql_gumanet')->table('tbl_order_recibo_adjuntos')->where('id_recibo', $Recibo)->get();

        if(count($response) >= 1){
            foreach($response as $row){
                $array[$i]['mId']               = $row->id;
                $array[$i]['mRecibo']           = $row->id_recibo;
                $array[$i]['mNombreImagen']     = $row->Nombre_imagen;
                $array[$i]['imagen_url']        = Storage::Disk('s3')->temporaryUrl('Adjuntos-Recibos/'.$row->Nombre_imagen, now()->addMinutes(5));
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
                $json = ['tax'=>$row->tax, 'currency_code'=>$row->currency_code];
                //$json[] = $row;
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

    public static function get_comentarios_im($Ruta,$OrderBy){

        $orderBy = strtolower($OrderBy);
        $i = 0;
        $json = array();

        $response = DB::connection('mysql_gumanet')->table('tbl_comentarios')->where('Autor', $Ruta)->orderBy('Fecha', $orderBy)->get();

        if(count($response) >= 1){
            foreach($response as $row){
                $json[$i]['Titulo']    = $row->Titulo;
                $json[$i]['Contenido'] = $row->Contenido;
                $json[$i]['Fecha']     = $row->Fecha;
                $json[$i]['Autor']     = $row->Autor;
                $json[$i]['Imagen']    = Storage::temporaryUrl('news/'.$row->Imagen, now()->addMinutes(5));;
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
        try{
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
        }catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }
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
            $json[$i]['img_url']                  = Storage::temporaryUrl('product/'.$set_img, now()->addMinutes(5));
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
        try{
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
        }catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }
    }

    public static function get_nc(Request $request){
        $cliente = $request->input('cliente');

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
        
        return $json;
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

    public static function post_report(Request $request){
        try{
            $Fecha          = $request->input('sndFecha');
            $Nombre         = $request->input('sndTitulo');
            $CodRuta        = $request->input('sndCodigo');
            $NamRuta        = $request->input('sndNombre');
            $Comentario     = $request->input('snd_comentario');
            $imagektp       = $request->input('snd_image');
            $Empresa        = '1';
            $Read           = '0';
            $Updated_at     = date('Y-m-d H:i:s');
            $nama_imagen    = "";

            if($imagektp !=""){
                $nama_imagen = time() . '-' . rand(0, 99999) . ".jpg";
                //$pathktp = "../upload/news/" . $nama_imagen;
                //file_put_contents($pathktp, base64_decode($imagektp));  
                Storage::disk('s3')->put('news/'.$nama_imagen, base64_decode($imagektp));  
            }

            $query = "INSERT INTO tbl_comentarios (Titulo,Contenido, Autor, Nombre,Fecha,Imagen,empresa,`Read`,updated_at) VALUES ('$Nombre','$Comentario', '$CodRuta', '$NamRuta','$Fecha','$nama_imagen','$Empresa','$Read','$Updated_at')";
            $response = DB::connection('mysql_gumanet')->select($query);
            //$response = DB::insert($query);

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

    public static function post_update_datos(Request $request){
        $KeysSecret 	= "A7M";
        $table_name 	= 'ecommerce_android_app.tbl_admin';
        $where_clause	= "WHERE username = '".$request->Ruta."'";
        $whereSQL 		= '';

        $form_data = array(
            'email'  		  	=> $request->Email,
            'Telefono'  		=> $request->Telefono,
            'password'  		=> hash('sha256',$KeysSecret.$request->Contrasenna)
        );

        if(!empty($where_clause)) {
            if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
                $whereSQL = " WHERE ".$where_clause;
            } else {
                $whereSQL = " ".trim($where_clause);
            }
        }

        $sql = "UPDATE ".$table_name." SET ";
        $sets = array();
        foreach($form_data as $column => $value) {
             $sets[] = "`".$column."` = '".$value."'";
        }
        $sql .= implode(', ', $sets);
        $sql .= $whereSQL;

        $hasil = DB::update($sql);

        if ($hasil > 0) {
            return array("Success"=>"Data Inserted Successfully");
        } else {
            return array('Error'=>'Try Again');
        }
    }

    public static function push_pin(Request $request){
        try{
            $cliente     = $request->cliente;
            $date        = date('Y-m-d h:i:s');

            $query = "SELECT * FROM ecommerce_android_app.tlb_pins WHERE Cliente = '".$cliente."'";
            $total_records = DB::select($query);

            if(count($total_records) >= 1){
                $qDelete = "DELETE FROM ecommerce_android_app.tlb_pins WHERE Cliente = '".$cliente."'";
                if (DB::delete($qDelete)) {
                    return 'Defijado';
                }else {
                    return array('Error'=>'Try Again');
                }
        
            }else{
                $query2 = "INSERT INTO ecommerce_android_app.tlb_pins (Cliente,created_at) VALUES ('$cliente','$date')";
                if (DB::insert($query2)) {
                    return 'Fijado';
                }else {
                    return array('Error'=>'Try Again');
                }
            }
        }catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }
    }

    public static function stac_recup(Request $request){

        $dta = array(); $i = 0;

        $anio = $request->sAnno;
        $mes  = $request->sMes;
        $Ruta = $request->ruta;
        $fecha       = date('Y-m-d',strtotime(str_replace('/', '-',($anio.'-'.$mes.'-01'))));

        $Meta_Recuperacion  =   0.00;
        $Recup_Credito      =   0.00;
        $Recup_Contado      =   0.00;
        $Recup_Total        =   0.00;
        $Recup_cumple       =   0.00;

        $qRecuperacion= "SELECT * FROM gumanet.umk_recuperacion WHERE fecha_recup = '".$fecha."' and ruta='".$Ruta."' and idCompanny = 1";
        
        $response = DB::Select($qRecuperacion);

        if(count($response) >= 1) {
            $link_recuperacion = $response;
            $Recup_Credito = number_format($response[0]->recuperado_credito,2,".","");
            $Recup_Contado = number_format($link_recuperacion[0]->recuperado_contado,2,".","");
        }

        $qMeta= "SELECT * FROM gumanet.meta_recuperacion_exl WHERE fechaMeta = '".$fecha."' and ruta='".$Ruta."' and idCompanny = 1";
        $rMeta = DB::select($qMeta);
        if(count($rMeta) >= 1) {
            $link_meta = $rMeta;
            $Meta_Recuperacion = number_format($link_meta[0]->meta,2,".","");
        }

        $Recup_Total = $Recup_Credito + $Recup_Contado;

        $dta[$i]['Meta_Recuperacion']           = $Meta_Recuperacion;
        $dta[$i]['Recup_Credito']               = $Recup_Credito;
        $dta[$i]['Recup_Contado']               = $Recup_Contado;
        $dta[$i]['Recup_Total']                 = number_format($Recup_Total,2,".","");
        $dta[$i]['Recup_cumple']                = ($Meta_Recuperacion==0) ? "100.00" : number_format(((floatval($Recup_Total)/floatval($Meta_Recuperacion))*100),2);

        return $dta;
    
    }

    public static function get_history_lotes(Request $request){
        $lote = $request->lote;
        $cliente = $request->cliente;

        $query = "SELECT * FROM PRODUCCION.dbo.GMV_Search_Lotes T0 WHERE  T0.LOTE ='".$lote."' AND T0.CCL='".$cliente."'  GROUP BY T0.CCL,T0.NCL,T0.LOTE,T0.FACTURA,T0.Dia,t0.ARTICULO,T0.DESCRIPCION";
        $i = 0;
        $json = array();

        $response = DB::connection('sqlsrv')->select($query);
    
        foreach ($response as $fila) {
            $json[$i]['mLote']          = $fila->LOTE;
            $json[$i]['mFactura']       = $fila->FACTURA;
            $json[$i]['mDia']           = $fila->Dia;
            $json[$i]['mArticulo']      = $fila->CCL;
            $json[$i]['mDescripcion']   = $fila->NCL;
            $i++;
        }

        return $json;
    }

    public static function plan_crecimiento($Cliente,$Ruta){



        $Q01="SELECT * FROM PRODUCCION.dbo.view_plan_crecimiento WHERE CLIENTE_CODIGO='".$Cliente ."'";
    
        $Q02="SELECT month(T0.Fecha_de_Factura) number_month,SUBSTRING(t0.MES,0,4) name_month,t0.[AÑO] annio,sum(T0.VentaNetaLocal) ttMonth 
            FROM Softland.dbo.ANA_VentasTotales_MOD_Contabilidad_UMK T0 WHERE T0.Fecha_de_Factura BETWEEN '2022-07-01 00:00:00.000' and '2023-08-01 00:00:00.000'
            AND T0.CLIENTE_CODIGO= '".$Cliente ."' and T0.VentaNetaLocal  > 0
            GROUP BY MONTH ( T0.Fecha_de_Factura ),YEAR  ( T0.Fecha_de_factura),t0.MES,t0.[AÑO] ORDER BY YEAR  ( T0.Fecha_de_factura) ASC,month(T0.Fecha_de_Factura)";

        $dta        = array(); 
        $dta_month  = array(); 

        $i=0;

        $query_result01 = DB::connection('sqlsrv')->select($Q01);
        foreach ($query_result01 as $key) {
            $dta['EVALUADO']      = ceil($key->EVALUADO);
            $dta['CRECIMIENTO']      = ceil($key->CRECIMIENTO);
            $dta['COMPRA_MIN']      = ceil($key->COMPRA_MIN);
            $dta['PROM_CUMP']      = ceil(number_format($key->PROM_CUMP,0));
        }
        $query_result02 = DB::connection('sqlsrv')->select($Q02);
        foreach ($query_result02 as $key) {        
            $dta_month[$i]['number_month']    = $key->number_month;      
            $dta_month[$i]['name_month']    = $key->name_month;      
            $dta_month[$i]['annio']    = $key->annio;      
            $dta_month[$i]['ttMonth']      = ceil($key->ttMonth);
            $i++;
        }

        $dtaBodega[] = array(
            'InfoCliente' => $dta,
            'SalesMonths' => $dta_month
        );
        
        return $dtaBodega;
    }

    public static function post_historico_factura(Request $request){
        $ruta = $request->cliente;

        $Q="SELECT
            T0.FACTURA,
            T0.Dia,
            T0.[Nombre del cliente] AS Cliente,
            SUM ( T0.Venta ) AS Venta,
            ( SELECT COUNT ( * ) FROM Softland.dbo.APK_CxC_DocVenxCL AS T1 WHERE T1.DOCUMENTO= T0.FACTURA ) AS ACTIVA,
            ( SELECT ISNULL(SUM(T4.SALDO_LOCAL) , 0) FROM Softland.dbo.APK_CxC_DocVenxCL AS T4 WHERE T4.DOCUMENTO = T0.FACTURA ) AS SALDO,
            ISNULL(convert(nvarchar(11),( SELECT T2.FECHA_VENCE FROM Softland.dbo.APK_CxC_DocVenxCL AS T2 WHERE T2.DOCUMENTO= T0.FACTURA ),103), '-/-/-') AS FECHA_VENCE,
            (SELECT T3.DVencidos FROM Softland.dbo.APK_CxC_DocVenxCL AS T3 WHERE T3.DOCUMENTO= T0.FACTURA ) AS DVencidos,
            T0.Plazo
            FROM
                Softland.dbo.VtasTotal_UMK T0 
            WHERE
                T0.[Cod. Cliente] ='".$ruta."' 
            GROUP BY
                T0.FACTURA,
                T0.Dia,
                T0.[Nombre del cliente],
                T0.Plazo
            ORDER BY
                T0.Dia DESC";

        $dta = array(); $i=0;
        $query = DB::connection('sqlsrv')->select($Q);
        foreach ($query as $key) {
            $dta[$i]['FACTURA']    = $key->FACTURA;
            $dta[$i]['FECHA']      = date('d/m/Y', strtotime($key->Dia));
            $dta[$i]['CLIENTE']    = $key->Cliente;
            $dta[$i]['MONTO']      = str_replace(",", "",number_format($key->Venta,2));
            $dta[$i]['ACTIVA']     = $key->ACTIVA;
            $dta[$i]['PLAZO']      = $key->Plazo;
            $dta[$i]['VENCE']      = $key->FECHA_VENCE;
            $dta[$i]['DVENCIDOS']  = $key->DVencidos;
            $dta[$i]['SALDO']      = str_replace(",", "",number_format($key->SALDO,2));
            $i++;
        }

        return $dta;
    }

    public static function recibo_anular(Request $request){
        $recibo         = $request->recibo_anular;
        $fecha_recibo   = $request->Fecha_Recibo;
        $Ruta           = $request->Ruta;
        
        $qIsExist = "SELECT * FROM gumanet.tbl_order_recibo T0 WHERE T0.recibo = '".$recibo."' AND  T0.ruta  = '".$Ruta."' AND T0.status in (0,1,4) ";    
        $response = DB::select($qIsExist);
        
        if(count($response) != 1){

            $ruta           = $Ruta;
            $cod_cliente    = "00000";
    
            $recibo         = $recibo;
            $fecha_recibo   = $fecha_recibo;
            
            $name_cliente   = "N/D";    
            $order_list     = "[00000000;0.00;0.00;0;0.00;0.00;0.00;00000;ANULADO],";
            $order_total    = "C$ 0.00";
            $comment        = "ESTE RECIBO FUE ANULADO POR EL VENDEDOR";
            $comment_anul   = "";
            $player_id      = $request->Player_Id;
            $date           = date('Y-m-d H:i:s');
    
            $query = "INSERT INTO gumanet.tbl_order_recibo (ruta, cod_cliente,recibo,fecha_recibo, name_cliente,created_at, order_list, order_total, comment,comment_anul, player_id,status) 
                VALUES ('$ruta', '$cod_cliente', '$recibo', '$fecha_recibo', '$name_cliente','$date', '$order_list', '$order_total', '$comment', '$comment_anul', '$player_id',4)";

            $result = DB::insert($query);
            if ($result) {
                return array('Success'=>'Nuevo');
            } else {
                return array('Error'=>'Error');
            }
                        
        }else{
            return array('Success'=>'Existe');
        }
    }

    public static function post_verificacion(Request $request){
        $Lati        = $request->Lati;
        $Logi        = $request->Logi;
        $cliente     = $request->cliente;
        $date        = $request->date;
    
    
        $query = "SELECT * FROM ecommerce_android_app.tlb_verificacion WHERE Cliente = '".$cliente."'";
        $result = DB::select($query);
    
        if(count($result) >= 1){
    
            $table_name 	= 'ecommerce_android_app.tlb_verificacion';
            $where_clause	= "WHERE Cliente = '".$cliente."'";
            $whereSQL 		= '';
    
            $form_data = array(
                'Lati'  		=> $Lati,
                'Longi'  		=> $Logi,
                'updated_at'  		=> $date
            );
            if(!empty($where_clause)) {
                if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
                    $whereSQL = " WHERE ".$where_clause;
                } else {
                    $whereSQL = " ".trim($where_clause);
                }
            }
            $sql = "UPDATE ".$table_name." SET ";
            $sets = array();
            foreach($form_data as $column => $value) {
                $sets[] = "`".$column."` = '".$value."'";
            }
            $sql .= implode(', ', $sets);
            $sql .= $whereSQL;
    
    
            $hasil = DB::update($sql);
    
            if ($hasil > 0) {
                return array("Success"=>"Data Inserted Successfully");
            } else {
                return array('Error'=>'Try Again');
            }
    
    
    
        }else{
            $query = "INSERT INTO ecommerce_android_app.tlb_verificacion (Cliente,Lati,Longi,created_at) VALUES ('$cliente','$Lati', '$Logi', '$date')";
            if (DB::insert($query)) {
                return array("Success"=>"Data Inserted Successfully");
            } else {
                return array('Error'=>'Try Again');
            }
        }
    }

    
    public static function setPedidos(Request $request)
    {

        $Pedidos = Pedido::WHERE('name', 'F06')->limit(10)->get();
        $pedidos_a_insertar = array();
        $lineass_a_insertar = array();

        $PedidoCodigo = PedidoUMK::getConsecutivo('PX6');

        foreach ($Pedidos as $key => $value) {
            $OrdenList = '';

            $ultimoConsecutivo = $PedidoCodigo;
    
            $ultimoNumero = intval(substr($ultimoConsecutivo, 6));
            $nuevoNumero = $ultimoNumero + ($key + 1);
            $nuevoConsecutivo = sprintf('PX8-%06d', $nuevoNumero);

            $Cliente_Pedido = trim($value->email);
            $Cliente_Pedido = str_replace('-', '', $Cliente_Pedido);

            $Monto_Pedido   = $value->order_total;
            $Monto_Pedido = number_format((float) str_replace(',', '', $Monto_Pedido), 2, '.', '');
            $ttUnidades     = '40.00000000';

            
            $PedidoFecha      = date('Y-m-d 00:00:00.000');
            $PedidoCliente    = Clientes::WHERE('CLIENTE',$Cliente_Pedido)->get();

            $Cliente        = $PedidoCliente[0]->CLIENTE;
            $NOMBRE         = $PedidoCliente[0]->NOMBRE;
            $DiGeo1         = $PedidoCliente[0]->DIVISION_GEOGRAFICA1;
            $DiGeo2         = $PedidoCliente[0]->DIVISION_GEOGRAFICA2;
            $DIRECCION      = $PedidoCliente[0]->DIRECCION;
            $VENDEDOR       = $PedidoCliente[0]->VENDEDOR;
            $COBRADOR       = $PedidoCliente[0]->COBRADOR;
            $NIVEL_PRECIO   = $PedidoCliente[0]->NIVEL_PRECIO;
            $GUID           = PedidoUMK::generateGUID();

            $pedidos_a_insertar[$key]['PEDIDO'] = $nuevoConsecutivo;
            $pedidos_a_insertar[$key]['ESTADO'] = 'F';
            $pedidos_a_insertar[$key]['FECHA_PEDIDO'] = $PedidoFecha;
            $pedidos_a_insertar[$key]['FECHA_PROMETIDA'] = $PedidoFecha;
            $pedidos_a_insertar[$key]['FECHA_PROX_EMBARQU'] = $PedidoFecha;
            $pedidos_a_insertar[$key]['FECHA_ULT_EMBARQUE'] = $PedidoFecha;
            $pedidos_a_insertar[$key]['FECHA_ULT_CANCELAC'] = '1980-01-01 00:00:00.000';
            $pedidos_a_insertar[$key]['ORDEN_COMPRA'] = null;
            $pedidos_a_insertar[$key]['FECHA_ORDEN'] = $PedidoFecha;
            $pedidos_a_insertar[$key]['TARJETA_CREDITO'] = ' ';
            $pedidos_a_insertar[$key]['EMBARCAR_A'] = $NOMBRE;
            $pedidos_a_insertar[$key]['DIREC_EMBARQUE'] = 'ND';
            $pedidos_a_insertar[$key]['DIRECCION_FACTURA'] = $DIRECCION;
            $pedidos_a_insertar[$key]['RUBRO1'] = null;
            $pedidos_a_insertar[$key]['RUBRO2'] = null;
            $pedidos_a_insertar[$key]['RUBRO3'] = null;
            $pedidos_a_insertar[$key]['RUBRO4'] = null;
            $pedidos_a_insertar[$key]['RUBRO5'] = null;
            $pedidos_a_insertar[$key]['OBSERVACIONES'] = '';
            $pedidos_a_insertar[$key]['COMENTARIO_CXC'] = null;
            $pedidos_a_insertar[$key]['TOTAL_MERCADERIA'] = $Monto_Pedido;
            $pedidos_a_insertar[$key]['MONTO_ANTICIPO'] = '.00000000';
            $pedidos_a_insertar[$key]['MONTO_FLETE'] = '.00000000';
            $pedidos_a_insertar[$key]['MONTO_SEGURO'] = '.00000000';
            $pedidos_a_insertar[$key]['MONTO_DOCUMENTACIO'] = '.00000000';
            $pedidos_a_insertar[$key]['TIPO_DESCUENTO1'] = 'P';
            $pedidos_a_insertar[$key]['TIPO_DESCUENTO2'] = 'P';
            $pedidos_a_insertar[$key]['MONTO_DESCUENTO1'] = '.00000000';
            $pedidos_a_insertar[$key]['MONTO_DESCUENTO2'] = '.00000000';
            $pedidos_a_insertar[$key]['PORC_DESCUENTO1'] = '.00000000';
            $pedidos_a_insertar[$key]['PORC_DESCUENTO2'] = '.00000000';
            $pedidos_a_insertar[$key]['TOTAL_IMPUESTO1'] = '.00000000';
            $pedidos_a_insertar[$key]['TOTAL_IMPUESTO2'] = '.00000000';
            $pedidos_a_insertar[$key]['TOTAL_A_FACTURAR'] = $Monto_Pedido;
            $pedidos_a_insertar[$key]['PORC_COMI_VENDEDOR'] = '.00000000';
            $pedidos_a_insertar[$key]['PORC_COMI_COBRADOR'] = '.00000000';
            $pedidos_a_insertar[$key]['TOTAL_CANCELADO'] = '.00000000';
            $pedidos_a_insertar[$key]['TOTAL_UNIDADES'] = $ttUnidades;
            $pedidos_a_insertar[$key]['IMPRESO'] = 'N';
            $pedidos_a_insertar[$key]['FECHA_HORA'] = '2023-06-06 08:59:22.537';
            $pedidos_a_insertar[$key]['DESCUENTO_VOLUMEN'] = '.00000000';
            $pedidos_a_insertar[$key]['TIPO_PEDIDO'] = 'N';
            $pedidos_a_insertar[$key]['MONEDA_PEDIDO'] = 'L';
            $pedidos_a_insertar[$key]['VERSION_NP'] = '1';
            $pedidos_a_insertar[$key]['AUTORIZADO'] = 'N';
            $pedidos_a_insertar[$key]['DOC_A_GENERAR'] = 'F';
            $pedidos_a_insertar[$key]['CLASE_PEDIDO'] = 'N';
            $pedidos_a_insertar[$key]['MONEDA'] = 'L';
            $pedidos_a_insertar[$key]['NIVEL_PRECIO'] = $NIVEL_PRECIO;
            $pedidos_a_insertar[$key]['COBRADOR'] = $COBRADOR;
            $pedidos_a_insertar[$key]['RUTA'] = 'ND';
            $pedidos_a_insertar[$key]['USUARIO'] = 'YURBINA';
            $pedidos_a_insertar[$key]['CONDICION_PAGO'] = '30';
            $pedidos_a_insertar[$key]['BODEGA'] = '002';
            $pedidos_a_insertar[$key]['ZONA'] = 'ND';
            $pedidos_a_insertar[$key]['VENDEDOR'] = $VENDEDOR;
            $pedidos_a_insertar[$key]['CLIENTE'] = $Cliente;
            $pedidos_a_insertar[$key]['CLIENTE_DIRECCION'] = $Cliente;
            $pedidos_a_insertar[$key]['CLIENTE_CORPORAC'] = $Cliente;
            $pedidos_a_insertar[$key]['CLIENTE_ORIGEN'] = $Cliente;
            $pedidos_a_insertar[$key]['PAIS'] = 'NI';
            $pedidos_a_insertar[$key]['SUBTIPO_DOC_CXC'] = '0';
            $pedidos_a_insertar[$key]['TIPO_DOC_CXC'] = 'FAC';
            $pedidos_a_insertar[$key]['BACKORDER'] = 'N';
            $pedidos_a_insertar[$key]['CONTRATO'] = null;
            $pedidos_a_insertar[$key]['PORC_INTCTE'] = '.00000000';
            $pedidos_a_insertar[$key]['DESCUENTO_CASCADA'] = 'S';
            $pedidos_a_insertar[$key]['TIPO_CAMBIO'] = null;
            $pedidos_a_insertar[$key]['FIJAR_TIPO_CAMBIO'] = 'N';
            $pedidos_a_insertar[$key]['ORIGEN_PEDIDO'] = 'F';
            $pedidos_a_insertar[$key]['DESC_DIREC_EMBARQUE'] = null;
            $pedidos_a_insertar[$key]['DIVISION_GEOGRAFICA1'] = $DiGeo1;
            $pedidos_a_insertar[$key]['DIVISION_GEOGRAFICA2'] = $DiGeo2;
            $pedidos_a_insertar[$key]['BASE_IMPUESTO1'] = null;
            $pedidos_a_insertar[$key]['BASE_IMPUESTO2'] = null;
            $pedidos_a_insertar[$key]['NOMBRE_CLIENTE'] = $NOMBRE;
            $pedidos_a_insertar[$key]['FECHA_PROYECTADA'] = $PedidoFecha;
            $pedidos_a_insertar[$key]['FECHA_APROBACION'] = null;
            $pedidos_a_insertar[$key]['NoteExistsFlag'] = '0';
            $pedidos_a_insertar[$key]['RecordDate'] = '2023-06-06 10:41:17.180';
            $pedidos_a_insertar[$key]['RowPointer'] = $GUID;
            $pedidos_a_insertar[$key]['CreatedBy'] = 'FA/YURBINA';
            $pedidos_a_insertar[$key]['UpdatedBy'] = 'FA/CGUTIERREZ';
            $pedidos_a_insertar[$key]['CreateDate'] = '2023-06-06 08:59:22.550';

            $OrdenList  = $value->order_list;
            $Lineas     = explode("],", $OrdenList);
            $cLineas    = count($Lineas) -1;
            

            for ($l=0; $l < $cLineas ; $l++){
                $GUIDLINEA = PedidoLineaUMK::generateGUID();

                $Lineas_detalles     = explode(";", $Lineas[$l]);
                
                $Articulo = $Lineas_detalles[1];
                $Cantidad = $Lineas_detalles[4];
                $Cantidad = number_format((float) str_replace(',', '', $Cantidad), 2, '.', '');

                $lineass_a_insertar[$key]['PEDIDO'] = $nuevoConsecutivo;
                $lineass_a_insertar[$key]['PEDIDO_LINEA'] = '0';
                $lineass_a_insertar[$key]['BODEGA'] = '';
                $lineass_a_insertar[$key]['LOTE'] = '';
                $lineass_a_insertar[$key]['LOCALIZACION'] = '';
                $lineass_a_insertar[$key]['ARTICULO'] = $Articulo;
                $lineass_a_insertar[$key]['ESTADO'] = '';
                $lineass_a_insertar[$key]['FECHA_ENTREGA'] = '';
                $lineass_a_insertar[$key]['LINEA_USUARIO'] = '0';
                $lineass_a_insertar[$key]['PRECIO_UNITARIO'] = '0.0';
                $lineass_a_insertar[$key]['CANTIDAD_PEDIDA'] = $Cantidad;;
                $lineass_a_insertar[$key]['CANTIDAD_A_FACTURA'] = '0.0';
                $lineass_a_insertar[$key]['CANTIDAD_FACTURADA'] = '0.0';
                $lineass_a_insertar[$key]['CANTIDAD_RESERVADA'] = '0.0';
                $lineass_a_insertar[$key]['CANTIDAD_BONIFICAD'] = '0.0';
                $lineass_a_insertar[$key]['CANTIDAD_CANCELADA'] = '0.0';
                $lineass_a_insertar[$key]['TIPO_DESCUENTO'] = '';
                $lineass_a_insertar[$key]['MONTO_DESCUENTO'] = '0.0';
                $lineass_a_insertar[$key]['PORC_DESCUENTO'] = '0.0';
                $lineass_a_insertar[$key]['DESCRIPCION'] = '';
                $lineass_a_insertar[$key]['COMENTARIO'] = '';
                $lineass_a_insertar[$key]['PEDIDO_LINEA_BONIF'] = '0';
                $lineass_a_insertar[$key]['UNIDAD_DISTRIBUCIO'] = '';
                $lineass_a_insertar[$key]['FECHA_PROMETIDA'] = '';
                $lineass_a_insertar[$key]['LINEA_ORDEN_COMPRA'] = '0';
                $lineass_a_insertar[$key]['PROYECTO'] = '';
                $lineass_a_insertar[$key]['FASE'] = '';
                $lineass_a_insertar[$key]['NoteExistsFlag'] = '0';
                $lineass_a_insertar[$key]['RecordDate'] = '';
                $lineass_a_insertar[$key]['RowPointer'] = $GUIDLINEA;
                $lineass_a_insertar[$key]['CreatedBy'] = '';
                $lineass_a_insertar[$key]['UpdatedBy'] = '';
                $lineass_a_insertar[$key]['CreateDate'] = '';
            }
        }

        try {

            PedidoUMK::insert($pedidos_a_insertar);
            PedidoLineaUMK::insert($lineass_a_insertar);
            
  


        } catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            dd($mensaje);
            //return response()->json($mensaje);
        }

        
        
        /*
        
        DECLARE @Pedido varchar(50) = 'PX5-013126'
        DECLARE @D1 varchar(50) = '2023-06-01 00:00:00.000'
        DECLARE @D2 varchar(50) = '2023-06-09 00:00:00.000'

        SELECT VENDEDOR,PEDIDO,FECHA_PEDIDO,TOTAL_A_FACTURAR,NIVEL_PRECIO,CLIENTE,NOMBRE_CLIENTE,CreatedBy,RowPointer FROM Softland.umk.PEDIDO WHERE FECHA_PEDIDO BETWEEN @D1 AND @D2 AND PEDIDO = @Pedido ORDER BY PEDIDO DESC;

        SELECT PEDIDO,PEDIDO_LINEA,BODEGA,LOTE,ARTICULO,LINEA_USUARIO ,PRECIO_UNITARIO,CANTIDAD_PEDIDA ,CANTIDAD_FACTURADA,PEDIDO_LINEA_BONIF,CreatedBy,RowPointer FROM Softland.umk.PEDIDO_LINEA WHERE PEDIDO = @Pedido;

        SELECT VENDEDOR,PEDIDO,FACTURA,FECHA_PEDIDO,TOTAL_UNIDADES,CreatedBy,RowPointer FROM Softland.umk.FACTURA WHERE PEDIDO = @Pedido;

        SELECT FACTURA,LINEA,PEDIDO,LOTE,CANTIDAD,PRECIO_UNITARIO FROM Softland.umk.FACTURA_LINEA WHERE PEDIDO = @Pedido ;

        //SELECT * FROM Softland.umk.CONSECUTIVO_FA

        */
        dd();
        return $pedidos_a_insertar;
        



    }
}