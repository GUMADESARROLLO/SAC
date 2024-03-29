<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ScheduleController;

class RunPedidosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:pedidos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $scheduleController->CronCheckVisita();
        $this->info('Tarea de Insert Cliente Verificados ejecutada correctamente.');
    }
}
