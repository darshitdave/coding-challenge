<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    public function get_owner(){
        return $this->hasMany('App\Models\Owner','car_id','id');
    }
}
