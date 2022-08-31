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
}