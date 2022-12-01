<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Session;

class PlanCrecimiento extends Model {
    protected $connection = 'sqlsrv';    
    protected $table = "PRODUCCION.dbo.tlb_cliente_plan_crecimiento";
    public $timestamps = false; 


    public static function AddPlanCrecimiento(Request $request)
    {
        if ($request->ajax()) {
            try {

                $id         = $request->input('id');

                $isExists = PlanCrecimiento::where('CODIGO_CLIENTE',$id)->count();
                
                if ($isExists == 0) {
                    $Obj = new PlanCrecimiento();
                    $Obj->CODIGO_CLIENTE       = $id;          
                    $Obj->save();   
                    $response = $Obj->save(); 
                } else {
                    $response = PlanCrecimiento::where('CODIGO_CLIENTE',$id)->delete();  
                }
                

                return response()->json($response);

            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }

    }
}