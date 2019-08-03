<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\StreamLink;

class APIControllerv1 extends Controller
{
	##LOGIN##
	public function loginProc(Request $request){
		$userData = '';
		$user = User::where('username',$request->email)->orWhere('email',$request->email)->count();

		if($user != 0){
			$userData = User::where('username',$request->email)->orWhere('email',$request->email)->first();
			
			if($userData->access_type == "U"){$level = '1';}
			else{$level = '0';}

			if(!empty($userData)){
				$id=$userData->id;
				$userData->token = apiToken($id);
			}

			if($userData->password != ""){
				$login_data = User::where('username',$request->email)->orWhere('email',$request->email)->first();

				if($login_data->access_type == "G"){	
					$msg = array("text"=>"Your account was blocked from this access. Please contact with administrator.");
					$datamsg = response()->json([
						'error' => $msg
					]);
					return $datamsg->content();
				}
				else{
					if(Hash::check($request->password, $userData->password)){
						$login_datas = User::where('username',$request->email)->orWhere('email',$request->email)->first();
						$msg = array("id"=>$userData->id, "username"=>$userData->username, "fullname"=>$userData->fullname, "email"=>$userData->email,"email_verified"=>$userData->email_verified,"mobile" => $userData->mobile, "access_type"=>$userData->access_type,"token"=>$userData->token);
						$datamsg = response()->json([
							'userData' => $msg
						]);
						return $datamsg->content();
					}
					else{
						$msg = array("text"=>"Password is incorrect.");
						$datamsg = response()->json([
							'error' => $msg
						]);
						return $datamsg->content();
					}
				}
			}
		}
		else{
			$msg = array("text"=>"User does not exist.");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}
	}

	##REGISTER##
	public function signup(Request $request) {
			$username = $request->username;
			$fullname = $request->fullname;
			$email = $request->email;
			$password = $request->password;
			$cpassword = $request->cpassword;
			$mobile = $request->mobile;

			$email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
			if(!isValidUsername($username)) { echo '{"error":{"text":"Please enter valid username."}}'; }
			elseif(strlen($username)<6) { echo '{"error":{"text":"Username must be more than 6 characters."}}'; }
			elseif(!isValidEmail($email)) { echo '{"error":{"text":"Please enter valid email address."}}'; }
			elseif(strlen($password)<3) { echo '{"error":{"text":"Password must be 3 characters only."}}'; }
			elseif($password !== $cpassword) { echo '{"error":{"text":"Password does not match with password for confirmation."}}'; }
			else{  
				$userData = '';
				$mainCount = User::where('username',$username)->orWhere('email',$email)->count();
				$created=time();
				if($mainCount==0){
					$hash = sha1($email);
					
					$user = User::create([
						'username' => $username,
						'fullname' => $fullname,
						'password' => bcrypt($password),
						'rawpass' => $password,
						'email' => $email,
						'mobile' => $mobile,
						'access_type' => 'U',
						'token' => ''
					]);

					$userData = User::where('username',$username)->first();
					$systemToken = apiToken($userData->id);
					$user = User::where('username',$username)->update(['token' => $systemToken]);
					$msg = array("id"=>$userData->id, "username"=>$userData->username, "fullname"=>$userData->fullname, "email"=>$userData->email,"email_verified"=>$userData->email_verified,"mobile" => $userData->mobile, "access_type"=>$userData->access_type,"token"=>$userData->token);
					$datamsg = response()->json([
						'userData' => $msg
					]);
					return $datamsg->content();
				}
				else {
					$msg = array("text"=>"This username or email has already been used. Please enter another.");
					$datamsg = response()->json([
						'error' => $msg
					]);
					return $datamsg->content();
				}
			}
	}

	##CHANGE PASSWORD##
	public function change_password(Request $request) {                            
		$id = $request->id;
		$token = $request->token;
		$cpass = $request->cpass;
		$npass = $request->npass;
		$cnpass = $request->cnpass;
		$systemToken = apiToken($id);

		if($token == $systemToken){
			$check_users = User::where('id', $id)->first();

			if(Hash::check($request->cpass, $check_users->password)){
				if ($request->npass  == $request->cnpass){
					$password = $request->npass;
					User::whereId($id)->update([
						'password' => Hash::make($password),
						'rawpass' => $password
					]);
					$datamsg = response()->json([
						'message' => 'Password successfully changed'
					]);
					return $datamsg->content();
				}
				else{
					$datamsg = response()->json([
						'error' => 'Password is not match with confirm password'
					]);
					return $datamsg->content();
				}
			}
			else{
				$datamsg = response()->json([
					'error' => 'The old password is wrong'
				]);
				return $datamsg->content();
				}
		}
		else{
			$msg = array("text"=>"No access");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}
	}

	// ##FORGOT PASSWORD##
	// public function reset_password(Request $request) {                         
	// 	$email = $request->email;
	// 	$sql_email = User::where('email',$email)->count();

	// 	if($sql_email==0) {  
	// 		$msg = array("text"=>"No such user with this email address.");
	// 		$datamsg = response()->json([
	// 			'error' => $msg
	// 		]);
	// 		return $datamsg->content();
	// 	}
	// 	else {
	// 		//whatsapp code
	// 		$userData = User::where('email', $sql_email)->first();
	// 		$name = $userData->fullname;
	// 		$message = "Click on the link to reset password.";
	// 		$hash =  hash('sha256', $sql_email);
	// 		send_reset_password($sql_email, $name, $message, $hash);

	// 		$msg = array("text"=>"Email with instructions to reset password was sent. Please check your inbox.");
	// 		$datamsg = response()->json([
	// 			'success' => $msg
	// 		]);
	// 		return $datamsg->content();
	// 	}
	// }

	##OTP##
	public function generateOTP(Request $request) {
		$id = $request->id;
		$token = $request->token;
		$fullname = $request->fullname;
		$mobile = $request->mobile;
		$systemToken = apiToken($id);

		if($token == $systemToken){
			$check_users = User::where('id', $id)->where('access_type','U')->count();
			$onecode = '';
			if($check_users != 0){
				for($i = 0; $i < 4; $i++) {$onecode .= mt_rand(0, 9);}
				$mobilecheck = User::where('mobile',$mobile)->count();
		
				if ($mobilecheck == 0){
					$user = User::create([
						'username' => 'GUEST',
						'fullname' => $fullname,
						'password' => bcrypt($onecode),
						'rawpass' => $onecode,
						'email' => 'guest mail',
						'mobile' => $mobile,
						'access_type' => 'G',
						'token' => 'guestentryonly'
					]);
					$userData = User::where('mobile',$mobile)->first();
					$systemToken = apiToken($userData->id);
					$msg = array("id"=>$userData->id, "username"=>$userData->username, "fullname"=>$userData->fullname, "email"=>$userData->email,"email_verified"=>$userData->email_verified,"mobile" => $userData->mobile, "access_type"=>$userData->access_type,"token"=>$userData->token);
					$datamsg = response()->json([
						'userData' => $msg
					]);
					return $datamsg->content();
				}
				else{
					$user = User::where('mobile',$mobile)->update([
						'password' => bcrypt($onecode),
						'rawpass' => $onecode,
						'token' => 'guestentryonly'
					]);
					$userData = User::where('mobile',$mobile)->first();
					$systemToken = apiToken($userData->id);
					$msg = array("id"=>$userData->id, "username"=>$userData->username, "fullname"=>$userData->fullname, "email"=>$userData->email,"email_verified"=>$userData->email_verified,"mobile" => $userData->mobile, "access_type"=>$userData->access_type,"token"=>$userData->token);
					$datamsg = response()->json([
						'userData' => $msg
					]);
					return $datamsg->content();
				}
			}
			else{
				$msg = array("text"=>"Not authorized");
				$datamsg = response()->json([
					'error' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("text"=>"No access");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}		
	}

	##STREAMING CAMERA##
	public function streamCam(Request $request) {
		$id = $request->id;
		$token = $request->token;
		$systemToken = apiToken($id);

		if($token == $systemToken){
			$userData = User::where('id',$id)->where('access_type', 'U')->first();

			if(!empty($userData)){
				$linkcheck = StreamLink::where('status', 'ACTIVE')->count();
				if($linkcheck != 0){
					$linkdata = StreamLink::where('status', 'ACTIVE')->first();
					$msg = array("link"=>$linkdata->strlink, "status"=>$linkdata->strlink, "id"=>$userData->id, "username"=>$userData->username, "fullname"=>$userData->fullname, "email"=>$userData->email,"email_verified"=>$userData->email_verified,"mobile" => $userData->mobile, "access_type"=>$userData->access_type,"token"=>$userData->token);
					$datamsg = response()->json([
						'userData' => $msg
					]);
					return $datamsg->content();
				}
				else{
					$msg = array("text"=>"No active stream link available.");
					$datamsg = response()->json([
						'error' => $msg
					]);
					return $datamsg->content();
				}
			}
			else{
				$msg = array("text"=>"Not authorized");
				$datamsg = response()->json([
					'error' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("text"=>"No access");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}	
	}
}
