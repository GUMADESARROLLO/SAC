<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ARTICULO_PRECIO extends Model
{
    protected $connection = 'sqlsrv_dev';
    public $timestamps = false;
    protected $table = "Softland.umk.ARTICULO_PRECIO";
    protected $primaryKey = 'NIVEL_PRECIO';

   

}