<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientesHistorico3M extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.GMV3_hstCompra_3M";
}
