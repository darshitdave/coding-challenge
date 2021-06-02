<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public function get_people(){
        return $this->hasMany('App\Models\Person','city_id','id');
    }
    
}
