<?php

namespace App\Models;
use Ramsey\Uuid\Uuid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PedidoLineaUMK extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    //protected $table = "Softland.umk.PEDIDO_LINEA";
    protected $table = "DESARROLLO.dbo.PEDIDO_LINEA";

    static public function generateGUID() {
        return strtoupper(Uuid::uuid4()->toString());
    }
   



}
