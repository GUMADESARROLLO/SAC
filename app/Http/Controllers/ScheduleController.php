<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function handle()
    {
        DB::connection('sqlsrv')->select('SET NOCOUNT ON; EXEC PRODUCCION.dbo.fn_vinnetas_calc ');

        \Log::channel('Schedule')->info("Actualizacion de Vinetas");
    }
}
