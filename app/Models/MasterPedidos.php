<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPedidos extends Model
{
    protected $connection = 'mysql_pedido';
    protected $table = "view_master_pedidos";
}
