<?php

namespace App\Models;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'tbl_promocion';

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(){

        if(!is_null($this->image)){
            return Storage::url('Promociones/'.$this->image);
        }
        return null;
    }
}
