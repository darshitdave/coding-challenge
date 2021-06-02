<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    public function get_street(){
        return $this->hasOne('App\Models\Street','id','street_id');
    }
}
