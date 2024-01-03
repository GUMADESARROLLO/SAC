<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ConsecutivoFa  extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.gmv_CONSECUTIVO_FA";

    public static function getConsecutivosS($Ptv)
    {
        $consecutivosPTV = ['PX6', 'PX9', 'PTV9', 'PTV3', 'PX09', 'PDX14', 'PX5', 'PTV4', 'PTV8', 'PX7', 'PX8', 'PZ', 'PTV2', 'PTV6', 'PTV7', 'PDX13', 'PDX12', 'PDX11', 'PDX10'];
        
        $Consecutivos = self::whereIn('PTV', $consecutivosPTV)->get()->toArray();

        $index_key = array_search($Ptv, array_column($Consecutivos, 'PTV')); 

        return $Consecutivos[$index_key]['VALOR_CONSECUTIVO'];

        
    }
    public static function getConsecutivos()
    {    
        return self::get()->toArray();
    }

}