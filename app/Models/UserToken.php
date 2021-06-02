<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    public function user(){
        return $this->hasMany('App\Models\Customer','id','user_id');
    }
}
