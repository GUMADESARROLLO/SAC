<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Session;

class PedidoComentario extends Model {
    protected $connection = 'mysql_pedido';    
    protected $table = "tbl_comment";
    public $timestamps = false;

    public static function getCommentPedido(Request $request)
    {
        if ($request->ajax()) {
            try {

                $id     = $request->input('id_item');

                return  PedidoComentario::where('orden_code',  $id)->get();

            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }

    }
    public static function AddCommentPedido(Request $request)
    {
        if ($request->ajax()) {
            try {

                $id         = $request->input('id_item');
                $Comment    = $request->input('comment');

                

                $ObjComment = new PedidoComentario();
                $ObjComment->orden_code       = $id; 
                $ObjComment->orden_comment    = $Comment; 
                $ObjComment->date_coment      = date('Y-m-d h:i:s');      
                $ObjComment->player_id        = Session::get('name_session');           
                $ObjComment->save();      
                
                //PedidoComentario::SendNotifications($request);

                return response()->json($ObjComment);

            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }

    }
    public static function DeleteCommentPedido(Request $request)
    {
        if ($request->ajax()) {
            try {

                $id     = $request->input('id');
                
                $response =   PedidoComentario::where('id_coment',  $id)->delete();

                return response()->json($response);


            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }

    }

    public static function SendNotifications(Request $request){
        if ($request->ajax()) {
            try {

                $id          = $request->input('id_item');
                
                $getPedido   = Pedido::where('code', $id)->first(['phone', 'player_id']);
                $NameCliente = $getPedido->phone;
                $Player_id   = $getPedido->player_id;
        
                $content = array("en" => "Nuevo Comentario $NameCliente ");
        
                $fields = array(
                    'app_id' => env('ONESIGNAL_APP_ID'),
                    'include_player_ids' => array($Player_id),
                    'data' => array("foo" => "bar", "cat_id"=> "1010101010"),
                    'headings'=> array("en" => "Notificacion"),
                    'contents' => $content    
                );
        
                $fields = json_encode($fields);
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, env('ONESIGNAL_URL'));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                    'Authorization: Basic '.env('ONESIGNAL_REST_KEY')));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
                $response = curl_exec($ch);
                curl_close($ch);
                
            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }
    }
}