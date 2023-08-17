<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GmvRecibo extends Model
{
    protected $connection = 'mysql_gumanet';
    protected $table = "tbl_order_recibo";
}
