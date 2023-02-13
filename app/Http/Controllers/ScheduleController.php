<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function handle()
    {
        \Log::channel('Schedule')->info("Actualizacion de Vinetas");
        DB::connection('sqlsrv')->select('EXEC fn_vinnetas_calc ');
    }
}
