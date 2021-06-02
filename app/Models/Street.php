<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    use HasFactory;

    public function get_cars(){
        return $this->hasMany('App\Models\Car','street_id','id');
    }
}
