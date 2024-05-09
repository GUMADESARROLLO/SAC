<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ScheduleController;

class CheckPromosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:promos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esta verificando si la promociones se a Vencido';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $scheduleController = new ScheduleController();
        //$scheduleController->RunPedidos();
        $scheduleController->CheckPromo();
        $this->info('Tarea de Verifica vigencia de las promociones.');
    }
}
