<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    public function get_address(){
        return $this->hasOne('App\Models\House','id','house_id');
    }
    
}
