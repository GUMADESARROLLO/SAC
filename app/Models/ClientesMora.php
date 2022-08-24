<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientesMora extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.GMV_PERFILES_CLIENTE";
}
