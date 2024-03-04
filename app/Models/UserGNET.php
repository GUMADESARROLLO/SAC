<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class UserGNET extends Model {
    protected $connection = 'mysql_gnet_pro';    
    protected $table = "rutas";
    public $timestamps = false;
}
