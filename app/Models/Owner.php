<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    public function get_person(){
        return $this->hasOne('App\Models\Person','id','person_id');
    }
}
