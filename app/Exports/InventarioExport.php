<?php

namespace App\Exports;

use App\Models\Inventario;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventarioExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'CODIGO',
            'DESCRIPCION',
            'TOTAL',
            'PRECIO',
        ];
    }
    public function collection()
    {
         $inv = Inventario::getArticulosFavoritos();
         return $inv;

    }
}