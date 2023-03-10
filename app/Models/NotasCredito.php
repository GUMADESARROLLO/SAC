<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class NotasCredito extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_notas_creditos";
}