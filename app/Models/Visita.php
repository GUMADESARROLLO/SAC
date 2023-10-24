<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Http\Request;
use DateTime;
use DateInterval;

class Visita extends Model {
    protected $table = "tbl_visitas";

 
    
    public static function Add(Request $request)
    {

        if ($request->ajax()) {
            try {

                $Cliente    = $request->input('Cliente');
                $FechaVi    = $request->input('FechaVi');
                $Descrip    = $request->input('Descrip');
                $Ruta       = $request->input('Ruta');

                $iCliente   = ClientesFull::where('CLIENTE',$Cliente)->first();

                $obj = new Visita();   
                $obj->cliente       = $Cliente;   
                $obj->ruta          = $Ruta;
                $obj->nombre        = $iCliente->NOMBRE;                
                $obj->fechavisita   = $FechaVi;
                $obj->descripcion   = $Descrip;
                $obj->activo        = 'S';                 
                $response = $obj->save();

                return $response;
                
            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }
        
    }
    public static function reutilizar(Request $request)
    {

        if ($request->ajax()) {
            try {
                
                $ruta       = $request->input('ruta');
                $dtIni      = $request->input('startDate');
                $dtEnd      = $request->input('endDate');
                
                $NewFechas  = [];

                $Clientes_Visitas = Visita::where('activo', 'S')
                ->where('ruta', $ruta)
                ->whereBetween('fechavisita', [$dtIni, $dtEnd])
                ->get();


                foreach ($Clientes_Visitas as $key => $f) {
                    $dt = new DateTime($f->fechavisita);
                    $dt->add(new DateInterval('P1W')); 
                    $NewFechas[] = [
                        'ruta'      => $f->ruta,
                        'cliente'   => $f->cliente, 
                        'nombre'    => $f->nombre,
                        'activo'    => 'S',
                        'fechavisita'   => $dt->format('Y-m-d H:i:s')
                    ];
                }
                $response = Visita::insert($NewFechas); 
                return $response;
                
            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }
        
    }
    public static function getData($Ruta){
        $array = array();

        $result = Visita::where('activo','S')->where('ruta',$Ruta)->get();

        foreach($result as $key => $row ){
            $array[$key] = [
                'id'            => $row->id,
                'fechaInicio'   => $row->fechavisita,
                'descripcion'   => $row->descripcion,
                'nombre'        => $row->nombre,
                'cliente'       => $row->cliente,
                'activo'        => $row->activo,
            ];
           
        }

        return $array;
    }
    public static function UpdateVisita(Request $request)
    {
        if ($request->ajax()) {
            try {

                $id     = $request->input('id');
                $date   = $request->input('date');
                
                $response =   Visita::where('id',  $id)->update([
                    "fechavisita" => $date,
                ]);

                return response()->json($response);


            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }

    }
    public static function rmVisita($id)
    {
        try {
                
            $delete = Visita::where('id', $id )->delete();
            return  $delete;               
        } catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }
    }
}
