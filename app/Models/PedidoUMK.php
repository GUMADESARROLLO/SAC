<?php

namespace App\Models;
use Ramsey\Uuid\Uuid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PedidoUMK extends Model
{
    protected $connection = 'sqlsrv_dev';
    public $timestamps = false;
    protected $table = "Softland.umk.PEDIDO";
    //protected $table = "DESARROLLO.dbo.PEDIDO";

    static public function generateGUID() {
        return strtoupper(Uuid::uuid4()->toString());
    }

}
