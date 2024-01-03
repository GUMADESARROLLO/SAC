<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationSqlsrv extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "DESARROLLO.dbo.tlb_verificacion";

}
