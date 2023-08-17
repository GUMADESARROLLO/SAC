<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $connection = 'mysql_pedido';  
    protected $table = 'tbl_product';
    public $timestamps = false;
}
