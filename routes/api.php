<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Api routes	 
Route::post('store_logs', [ApiController::class, "LogInput"])->name('store.logs');
Route::post('store_logoutput/{output}', [ApiController::class, "LogOutput"])->name('store.logoutput');
Route::post('generate_device_token', [ApiController::class, "generateDeviceToken"])->name('generateDeviceToken');


//register
Route::post('register_user', [ApiController::class, "userRegister"])->name('userRegister');
//login
Route::post('login_user', [ApiController::class, "userLogin"])->name('userLogin');

//city search
Route::post('city_people', [ApiController::class, "cityWisePeople"])->name('cityWisePeople');

//street search
Route::post('street_car', [ApiController::class, "streetWiseCars"])->name('streetWiseCars');

//person details
Route::post('person_details', [ApiController::class, "personDetails"])->name('personDetails');

//car owner(s) details
Route::post('owner_details', [ApiController::class, "ownerDetails"])->name('ownerDetails');
