<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ScheduleController extends Controller
{
    public function CalcVinneta()
    {
        DB::connection('sqlsrv')->select('SET NOCOUNT ON; EXEC PRODUCCION.dbo.fn_vinnetas_calc ');

        \Log::channel('Schedule')->info("Actualizacion de Vinetas");

    }
    public function RunPedidos()
    {
        $url = route('pedidos');
        $client = new Client(['verify' => false]);
        $client->get($url);
        \Log::channel('Schedule_pedidos')->info("Ejecucion de Tarea de Pedidos ");

        
    }
}
