<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CONSECUTIVO_FA  extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "Softland.umk.CONSECUTIVO_FA";
}