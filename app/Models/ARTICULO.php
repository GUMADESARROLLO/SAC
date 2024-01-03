<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ARTICULO extends Model
{
    protected $primaryKey = 'ARTICULO';
    
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "Softland.umk.ARTICULO";

    public function PRECIOS()
    {
        return $this->hasMany(ARTICULO_PRECIO::class,'ARTICULO');
    }

}