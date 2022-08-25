<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientesHistoricoFactura extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb4_historico_factura";
}
