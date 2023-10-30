<?php

namespace App\Http\Controllers;
use App\Models\uFACTURA_LINEA;
use Illuminate\Http\Request;

class DevolucionesController extends Controller {
    public function __construct()
    {
        $this->middleware('auth');
    }   

    public function getDevoluciones()
    {  
        return view('Devoluciones.home');    
        
    }
    public function getData(Request $request)
    {

        $dtaHome =  uFACTURA_LINEA::getData($request);
        
        return response()->json($dtaHome);
    }

    
}