<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class  ClientesInfo extends Model
{
    protected $connection = 'sqlsrv_dev';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb4_master_cliente_info";
}
