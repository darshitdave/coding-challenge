<?php

namespace App\Http\Controllers;

use App\Models\UserToken;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function randomStringGenerater($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public function updateUserAppToken($headers,$user_id){

        $randToken = $this->randomStringGenerater(32);
        $usertoken = UserToken::where('device_token',$headers['device-token'])
                              ->update(['app_token' => $randToken,'user_id' => $user_id]);
        if($usertoken){
            return $randToken;
        } else {
            return false;
        }
    }

}
