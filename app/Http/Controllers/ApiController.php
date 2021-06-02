<?php

namespace App\Http\Controllers;

use URL;
use Auth;
use Hash;
use Request;
use Response;
use App\Models\Car;
use App\Models\XApi;
use App\Models\User;
use App\Models\City;
use App\Models\Street;
use App\Models\Person;
use App\Models\UserToken;
use App\Models\AppVersion;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function __construct(){

        $headers = apache_request_headers();
        //check X-API
        if (isset($headers['x-api-key']) && $headers['x-api-key'] != '') {
            if (isset($headers['device-type']) && $headers['device-type'] != '') {
               if(!XApi::checkXAPI($headers['x-api-key'],$headers['device-type'])){
                   echo json_encode(array('status'=>500,'message'=>'Invalid X-API'));exit;
               }
            } else {
               echo json_encode(array('status'=>500,'message'=>'Device type not found!'));exit;
            }
        } else {
          echo json_encode(array('status'=>500,'message'=>'X-API key not found!'));exit;
        }

        //check version
        if (isset($headers['device-token']) && isset($headers['version'])) {
            $updateVersion = UserToken::where('device_token',$headers['device-token'])->update(['Version' => $headers['version']]);
        }

         if (isset($headers['device-token']) && isset($headers['fcm-token'])) {
            $updateVersion = UserToken::where('device_token',$headers['device-token'])->update(['fcm_token' => $headers['fcm-token']]);
        }

        if (isset($headers['device-type']) && isset($headers['version'])) {
            $getUserAppVersion = AppVersion::where('platform',$headers['device-type'])->where('version',$headers['version'])->first();
            if (!empty($getUserAppVersion->expireddate)) {
                if ($getUserAppVersion->expireddate < date('Y-m-d H:i:s')) {
                    //CHECK IF THE USER VERSION IS EXPIRED OR NOT
                    $res['status'] = "700";
                    $res['message'] = "App version expired!";
                    echo json_encode($res);
                    exit;
                }
            }
        }

    }

    public function Append($log_file, $value)
    {
        \File::append($log_file, $value . "\r\n");
    }

    public function LogInput()
    {
        $log_file = storage_path() . '/logs/api' . date('Ymd') . '.log';
        $headers = apache_request_headers();

        $this->Append($log_file,'----------------' . debug_backtrace()[1]['function'] . ' --------------');
        $this->Append($log_file,'Request Info : ');
        $this->Append($log_file,'Date: ' . date('Y-m-d H:i:s') . '    IP: ' .  Request::ip());
        $this->Append($log_file,'User-Agent: ' .  Request::header('User-Agent'));
        $this->Append($log_file,'URL: ' .  Request::url());
        $this->Append($log_file,'Input Parameters: ' .  json_encode(Request::all()));
        $this->Append($log_file,'Headers Parameters: ' .  json_encode($headers));
        $this->Append($log_file,'-----------');
        return;
    }

    public function LogOutput($output)
    {
        $log_file = storage_path() . '/logs/api' . date('Ymd') . '.log';
        $this->Append($log_file, 'Output: ');
        $this->Append($log_file,$output);
        $this->Append($log_file,'--------------------END------------------------');
        $this->Append($log_file,'');
        return;
    }

    public function nulltoblank($data) {

        return !$data ? $data = "" : $data;
    }

    //GENERATE DEVICE TOKEN
    public function generateDeviceToken(){

        $this->LogInput();
        $errors_array = array();
        $headers = apache_request_headers();

        if (!isset($headers['device-type']) && $headers['device-type'])
            $errors_array['device-type'] = 'Please enter device type!';

        if(count($errors_array) == 0){
            $user = new User;
            $token = $user->generateToken($headers);
            $response['device_token'] = $token;

            $message = '';
            return $this->responseSuccess($message,$data);

        } else {
            $this->LogOutput(Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array)));
            return Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array),500);
        }
    }

    //user registration
    public function userRegister(){

    	$this->LogInput();
        $errors_array = array();
        $headers = apache_request_headers();

        if (!Request::has('name') || Request::get('name') == "")
            $errors_array['name'] = 'Please enter name!';

        if (!Request::has('email') || Request::get('email') == "")
            $errors_array['email'] = 'Please send email id!';

        if (!Request::has('password') || Request::get('password') == "")
            $errors_array['password'] = 'Please enter password!';

        if(count($errors_array) == 0){
        	$data = Request::all();

            //check email
        	$check_email = User::where('email',$data['email'])->first();

            if(empty($check_email)){

                //save user
                $add_user = new User;
                $add_user->name = $data['name'];
                $add_user->email = $data['email'];
                $add_user->password = bcrypt($data['password']);
                $add_user->save();

                if($add_user){
                    
                    $user_detail['id'] = $add_user->id;
                    $user_detail['name'] = $add_user->name;
                    $user_detail['email'] = $add_user->email;

                    $this->LogOutput(Response::json(array('status'=>200,'data' => $user_detail)));
                    return Response::json(array('status'=>200,'data' => $user_detail),200);

                }
            }else{
            	
            	$this->LogOutput(Response::json(array('status_code'=>401,'message' => 'Email id already register!')));
            	return Response::json(array('status_code'=>401,'message' => 'Email id already register!'),401);

            }

        }else{

        	$this->LogOutput(Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array)));
            return Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array),500);

        }
    }

    //user login
    public function userLogin(){

    	$this->LogInput();
        $errors_array = array();
        $headers = apache_request_headers();

        if (!Request::has('password') || Request::get('password') == "")
            $errors_array['password'] = 'Please enter password!';

        if(count($errors_array) == 0){
        	$userData = array();
        	$data = Request::all();

        	$query = User::query();

            //login using email id
            if(isset($data['email']) && $data['email'] != ''){
                $query->where('email',$data['email']);
            }

            $user_check = $query->first();
            
            if(!empty($user_check)){
                //authentication
            	if(Hash::check($data['password'], $user_check->password)){
	            	$user_detail = array();

                    $user_detail['id'] = $user_check->id;
	            	$user_detail['name'] = $user_check->name;
	            	$user_detail['email'] = $user_check->email;
                    
                	$this->LogOutput(Response::json(array('status'=>200,'data' => $user_detail)));
        			return Response::json(array('status'=>200,'data' => $user_detail),200);
	                
	            }else{

	            	$this->LogOutput(Response::json(array('status_code'=>401,'message' => 'Password is wrong try again!')));
            		return Response::json(array('status_code'=>401,'message' => 'Password is wrong try again!'),401);	
	            }
            }else{
            	
            	$this->LogOutput(Response::json(array('status_code'=>401,'message' => 'User not registered!')));
            	return Response::json(array('status_code'=>401,'message' => 'User not registered!'),401);
            }

        }else{

        	$this->LogOutput(Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array)));
            return Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array),500);
        }
    }

    //city wise people
    public function cityWisePeople(){

    	$this->LogInput();
        $errors_array = array();
        $headers = apache_request_headers();

        if (!isset($headers['user-id']) || $headers['user-id'] == "")
            $errors_array['user-id'] = 'Please enter user id!';

        if(count($errors_array) == 0){
        	$data = Request::all();
            
        	$check_user = User::where('id',$headers['user-id'])->first();
        	
        	if(!empty($check_user)){

        		$query = City::query();

                //search city name
		        if(isset($data['city_name']) && $data['city_name'] != ''){

		            $query->where('city_name', 'like', '%'.$data['city_name'].'%');

		        }
                
		        $get_city = $query->first();
                if(!empty($get_city)){ //get people based on city
                    $get_city = $get_city->get_people()->paginate(10);
                }

        		if(!empty($get_city)){
        			$person_details = array();

        			foreach ($get_city as $gk => $gv) {

                        $person_details[$gk]['id'] = $gv['id']; 
                        $person_details[$gk]['name'] = $gv['name'];
                        $person_details[$gk]['age'] = $gv['age'];;        						

        			}                    

					$this->LogOutput(Response::json(array('status'=>200,'data' => $person_details)));
        			return Response::json(array('status'=>200,'data' => $person_details),200);

        		}else{

        			$this->LogOutput(Response::json(array('status'=>404,'message' => 'Record not found!')));
        			return Response::json(array('status'=>404,'message' => 'Record not found!'),404);
        		}

        	}else{

        		$this->LogOutput(Response::json(array('status_code'=>401,'message' => 'Unauthorised User!')));
            	return Response::json(array('status_code'=>401,'message' => 'Unauthorised User!'),401);
        	}
        }else{

        	$this->LogOutput(Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array)));
            return Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array),500);

        }
    }

    //street wise cars
    public function streetWiseCars(){

    	$this->LogInput();
        $errors_array = array();
        $headers = apache_request_headers();

        if (!isset($headers['user-id']) || $headers['user-id'] == "")
            $errors_array['user-id'] = 'Please enter user id!';

        if(count($errors_array) == 0){
        	$data = Request::all();

        	$check_user = User::where('id',$headers['user-id'])->first();
        	
        	if(!empty($check_user)){

        		$query = Street::query();

                //search street name
		        if(isset($data['street_name']) && $data['street_name'] != ''){

		            $query->where('steet_name', 'like', '%'.$data['street_name'].'%');

		        }
                
		        $get_car_detail = $query->first();

                if(!empty($get_car_detail)){ //get cars based on street
                    $get_car_detail = $get_car_detail->get_cars()->paginate(10);
                }
        		if(!empty($get_car_detail)){
        			$car_details = array();

        			foreach ($get_car_detail as $ck => $cv) {

                        $car_details[$ck]['id'] = $cv['id']; 
                        $car_details[$ck]['brand'] = $cv['brand'];
                        $car_details[$ck]['license_plate'] = $cv['license_plate'];
                        $car_details[$ck]['color'] = $cv['color'];

        			}                    

					$this->LogOutput(Response::json(array('status'=>200,'data' => $car_details)));
        			return Response::json(array('status'=>200,'data' => $car_details),200);

        		}else{

        			$this->LogOutput(Response::json(array('status'=>404,'message' => 'Record not found!')));
        			return Response::json(array('status'=>404,'message' => 'Record not found!'),404);
        		}

        	}else{

        		$this->LogOutput(Response::json(array('status_code'=>401,'message' => 'Unauthorised User!')));
            	return Response::json(array('status_code'=>401,'message' => 'Unauthorised User!'),401);
        	}
        }else{

        	$this->LogOutput(Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array)));
            return Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array),500);

        }
    }

    //based on person name get address and street.
    public function personDetails(){

    	$this->LogInput();
        $errors_array = array();
        $headers = apache_request_headers();

        if (!isset($headers['user-id']) || $headers['user-id'] == "")
            $errors_array['user-id'] = 'Please enter user id!';

        if(count($errors_array) == 0){
        	$data = Request::all();

        	$check_user = User::where('id',$headers['user-id'])->first();
        	
        	if(!empty($check_user)){

        		$query = Person::query();

                //search city name
		        if(isset($data['person_name']) && $data['person_name'] != ''){

		            $query->where('name', 'like', '%'.$data['person_name'].'%');

		        }
                //get address and street
                $query->with(['get_address' => function($q){ $q->with(['get_street']); }]);
		        $get_person_detail = $query->get();
                
        		if(!empty($get_person_detail) && count($get_person_detail) > 0){
        			$person_details = array();
                    
        			foreach ($get_person_detail as $pk => $pv) {

                        $person_details[$pk]['id'] = $pv['id']; 
                        $person_details[$pk]['name'] = $pv['name'];
                        //address
                        if(!empty($pv['get_address'])){
                            $person_details[$pk]['address'] = $pv['get_address']['house_address'];
                        }
                        //street
                        if(!empty($pv['get_address']['get_street'])){
                            $person_details[$pk]['street'] = $pv['get_address']['get_street']['steet_name'];
                        }

        			}                    

					$this->LogOutput(Response::json(array('status'=>200,'data' => $person_details)));
        			return Response::json(array('status'=>200,'data' => $person_details),200);

        		}else{

        			$this->LogOutput(Response::json(array('status'=>404,'message' => 'Record not found!')));
        			return Response::json(array('status'=>404,'message' => 'Record not found!'),404);
        		}

        	}else{

        		$this->LogOutput(Response::json(array('status_code'=>401,'message' => 'Unauthorised User!')));
            	return Response::json(array('status_code'=>401,'message' => 'Unauthorised User!'),401);
        	}
        }else{

        	$this->LogOutput(Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array)));
            return Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array),500);

        }
    }

    //License plate number get owner details
    public function ownerDetails(){

    	$this->LogInput();
        $errors_array = array();
        $headers = apache_request_headers();

        if (!isset($headers['user-id']) || $headers['user-id'] == "")
            $errors_array['user-id'] = 'Please enter user id!';

        if(count($errors_array) == 0){
        	$data = Request::all();

        	$check_user = User::where('id',$headers['user-id'])->first();
        	
        	if(!empty($check_user)){

        		$query = Car::query();
                //search city name 
		        if(isset($data['license_plate']) && $data['license_plate'] != ''){

		            $query->where('license_plate', 'like', '%'.$data['license_plate'].'%');
                    
		        }
                //get owner details
                $query->with(['get_owner' => function($q){ $q->with(['get_person']); }]);
		        $get_owner_detail = $query->first();
                
        		if(!empty($get_owner_detail)){
        			$person_details = array();
                    
        			foreach ($get_owner_detail['get_owner'] as $ok => $ov) {
                        
                        $person_details[$ok]['id'] = $ov['get_person']['id']; 
                        $person_details[$ok]['name'] = $ov['get_person']['name'];
                        $person_details[$ok]['age'] = $ov['get_person']['age'];

        			}                    
                    
					$this->LogOutput(Response::json(array('status'=>200,'data' => $person_details)));
        			return Response::json(array('status'=>200,'data' => $person_details),200);

        		}else{

        			$this->LogOutput(Response::json(array('status'=>404,'message' => 'Record not found!')));
        			return Response::json(array('status'=>404,'message' => 'Record not found!'),404);
        		}

        	}else{

        		$this->LogOutput(Response::json(array('status_code'=>401,'message' => 'Unauthorised User!')));
            	return Response::json(array('status_code'=>401,'message' => 'Unauthorised User!'),401);
        	}
        }else{

        	$this->LogOutput(Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array)));
            return Response::json(array('status'=>500,'message' => 'errors','errors' => $errors_array),500);

        }
    }
    
}
