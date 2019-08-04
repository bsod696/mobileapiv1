<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\StreamLink;

class APIControllerv2 extends Controller
{
	##LOGIN##
	public function loginProc(Request $request){
		$userData = '';
		$user = User::Where('studentmail',$request->studentmail)->count();

		if($user != 0){
			$userData = User::Where('studentmail',$request->studentmail)->first();
			
			if($userData->access_type == "U"){$level = '1';}
			else{$level = '0';}

			if(!empty($userData)){
				$studentid=$userData->studentid;
				$userData->token = apiToken($studentid);
			}

			if($userData->password != ""){
				$login_data = User::where('studentmail',$request->studentmail)->first();

				if($login_data->access_type == "G"){	
					$msg = array("text"=>"Your account was blocked from this access. Please contact with administrator.");
					$datamsg = response()->json([
						'result' => $msg
					]);
					return $datamsg->content();
				}
				else{
					if(Hash::check($request->password, $userData->password)){
						$login_datas = User::where('studentmail',$request->studentmail)->first();
						$msg = array(
							"PIN"=>$userData->PIN, 
							"house"=>$userData->house, 
							"studentmail"=>$userData->studentmail,
							"password"=>$userData->password,  
							"studentid"=>$userData->studentid,
							"temporaryPIN"=>$userData->temporaryPIN,
							"access_type"=>$userData->access_type,
							"token"=>$userData->token
						);
						$datamsg = response()->json([
							'result' => $msg
						]);
						return $datamsg->content();
					}
					else{
						$msg = array("text"=>"Password is incorrect.");
						$datamsg = response()->json([
							'result' => $msg
						]);
						return $datamsg->content();
					}
				}
			}
		}
		else{
			$msg = array("text"=>"User does not exist.");
			$datamsg = response()->json([
				'result' => $msg
			]);
			return $datamsg->content();
		}
	}

	##REGISTER##
	public function signup(Request $request) {
			$PIN = $request->PIN;
			$house = $request->house;
			$studentmail = $request->studentmail;
			$password = $request->password;
			$studentid = $request->studentid;
			$temporaryPIN = $request->temporaryPIN;

			$email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $studentmail);
			if(!isValidEmail($email)) { echo '{"error":{"text":"Please enter valid email address."}}'; }
			elseif(strlen($password)<3) { echo '{"error":{"text":"Password must be 3 characters only."}}'; }
			else{  
				$userData = '';
				$mainCount = User::where('studentid',$studentid)->orWhere('studentmail',$studentmail)->count();
				$created=time();
				if($mainCount==0){
					$hash = sha1($studentmail);
					
					$user = User::create([
						'PIN' => $PIN,
						'house' => $house,
						'studentmail' => $studentmail,
						'password' =>  bcrypt($password),
						'studentid' => $studentid,
						'temporaryPIN' => $temporaryPIN,
						'access_type' => 'U',
						'token' => ''
					]);

					$userData = User::where('studentmail',$studentmail)->first();
					$systemToken = apiToken($userData->studentid);
					$user = User::where('studentmail',$studentmail)->update(['token' => $systemToken]);
					$msg = array(
							"PIN"=>$userData->PIN, 
							"house"=>$userData->house, 
							"studentmail"=>$userData->studentmail,
							"password"=>$userData->password,  
							"studentid"=>$userData->studentid,
							"temporaryPIN"=>$userData->temporaryPIN,
							"access_type"=>$userData->access_type,
							"token"=>$userData->token
					);
					$datamsg = response()->json([
						'result' => $msg
					]);
					return $datamsg->content();
				}
				else {
					$msg = array("text"=>"This username or email has already been used. Please enter another.");
					$datamsg = response()->json([
						'result' => $msg
					]);
					return $datamsg->content();
				}
			}
	}

	##OTP##
	public function generateOTP(Request $request) {
		$PIN = $request->PIN;
		$house = $request->house;
		$studentmail = $request->studentmail;
		$studentid = $request->studentid;
		$temporaryPIN = $request->temporaryPIN;
			
		$check_users = User::where('studentmail', $studentmail)->where('access_type','U')->count();
		$onecode = '';
		if($check_users != 0){
			$user = User::where('studentmail',$studentmail)->update(['temporaryPIN' => $temporaryPIN]);
			$userData = User::where('studentmail',$studentmail)->first();
			$systemToken = apiToken($userData->id);
			$msg = array(
				"PIN"=>$userData->PIN, 
				"house"=>$userData->house, 
				"studentmail"=>$userData->studentmail,
				"password"=>$userData->password,  
				"studentid"=>$userData->studentid,
				"temporaryPIN"=>$userData->temporaryPIN,
				"access_type"=>$userData->access_type,
				"token"=>$userData->token
			);
			$datamsg = response()->json([
				'result' => $msg
			]);
			return $datamsg->content();
		}
		else{
			$msg = array("text"=>"Not authorized");
			$datamsg = response()->json([
				'result' => $msg
			]);
			return $datamsg->content();
		}
	}

	##STREAMING CAMERA##
	public function streamCam(Request $request) {
		$up = StreamLink::where("status", "ACTIVE")->update(['status' => "ACTIVE"]);
		$linkcheck = StreamLink::where('status', 'ACTIVE')->count();
		if($linkcheck != 0){
			$linkdata = StreamLink::where('status', 'ACTIVE')->first();
			$msg = array(
				"link"=>$linkdata->strlink, 
				"status"=>$linkdata->status
			);
			$datamsg = response()->json([
				'userData' => $msg
			]);
			return $datamsg->content();
		}
		else{
			$msg = array("text"=>"No active stream link available.");
			$datamsg = response()->json([
				'result' => $msg
			]);
			return $datamsg->content();
		}
	}
	public function stopCam(Request $request) {
		$up = StreamLink::where("status", "ACTIVE")->update(['status' => "INACTIVE"]);
		$msg = array("text"=>"Streaming link deactivated.");
		$datamsg = response()->json([
			'result' => $msg
		]);
		return $datamsg->content();
	}
}
