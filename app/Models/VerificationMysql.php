<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationMysql extends Model
{
    protected $connection = 'mysql_pedido';
    public $timestamps = false;
    protected $table = "tlb_verificacion";

}
