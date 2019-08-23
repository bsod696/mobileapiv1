<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\User;
use App\Guest;
use App\StreamLink;

class APIControllerv3 extends Controller
{
	##LOGIN##
	public function loginProc(Request $request){
		$studentmail = $request->studentmail;
		$password = $request->password;
		$user = User::Where('studentmail',$studentmail)->count();

		if($user != 0){
			$userData = User::Where('studentmail',$studentmail)->first();
			
			if($userData->access_type == "OWNER"){$level = '1';}
			else{$level = '0';}

			if(!empty($userData)){
				$studentid=$userData->studentid;
			}

			if($userData->password != ""){
				$login_data = User::where('studentmail',$studentmail)->first();

				if($login_data->access_type == "GUEST"){	
					$msg = array("text"=>"Your account was blocked from this access. Please contact with administrator.");
					$datamsg = response()->json([
						'result' => $msg
					]);
					return $datamsg->content();
				}
				else{
					if(Hash::check($password, $userData->password)){
						$login_datas = User::where('studentmail',$studentmail)->first();
						$msg = array(
							"PIN"=>$userData->PIN, 
							"house"=>$userData->house, 
							"studentmail"=>$userData->studentmail,
							"password"=>$userData->password,  
							"studentid"=>$userData->studentid,
							"temporaryPIN"=>$userData->temporaryPIN,
							"access_type"=>$userData->access_type
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
			$studentid = $request->studentid;
			
			$email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $studentmail);
			if(!isValidEmail($studentmail)) { echo '{"result":{"text":"Please enter valid email address."}}'; }
			else{  
				$userData = '';
				$mainCount = User::where('studentmail',$studentmail)->count();
				//dd($mainCount);
				if($mainCount==0){
					$user = User::create([
						'PIN' => $PIN,
						'house' => $house,
						'studentmail' => $studentmail,
						'studentid' => $studentid,
						'password' => "noaccess",
						'access_type' => 'OWNER'
					]);

					$userData = User::where('studentmail',$studentmail)->first();
					$msg = array(
							"PIN"=>$userData->PIN, 
							"house"=>$userData->house, 
							"studentmail"=>$userData->studentmail,
							"studentid"=>$userData->studentid,
							"access_type"=>$userData->access_type
					);
					$datamsg = response()->json([
						'result' => $msg
					]);
					return $datamsg->content();
				}
				else {
					$user = User::where('studentmail',$studentmail)->update([
						'PIN' => $PIN,
						'house' => $house,
						'studentmail' => $studentmail,
						'studentid' => $studentid,
						'access_type' => 'OWNER'
					]);

					$userData = User::where('studentmail',$studentmail)->first();
					$msg = array(
							"PIN"=>$userData->PIN, 
							"house"=>$userData->house, 
							"studentmail"=>$userData->studentmail,
							"studentid"=>$userData->studentid,
							"access_type"=>$userData->access_type
					);
					$datamsg = response()->json([
						'result' => $msg
					]);
					return $datamsg->content();
				}
			}
	}

	##DELETE USER##
	public function deleteUser(Request $request) {
		$studentmail = $request->studentmail;
		$delusr=User::where('studentmail',$studentmail)->delete();
		$userData=User::where('studentmail',$studentmail)->first();

		$msg = array("text"=>"User with student ID ".$userData->studentid." deleted.");
		$datamsg = response()->json([
			'result' => $msg
		]);
		return $datamsg->content();
	}

	##UPDATE PIN##
	public function updatePIN(Request $request) {
		$studentmail = $request->studentmail;
		$PIN = $request->PIN;
		$check_users = User::where('studentmail', $studentmail)->where('access_type','OWNER')->count();

		if($check_users != 0){
			$user = User::where('studentmail',$studentmail)->update(['PIN' => $PIN]);
			$userData = User::where('studentmail',$studentmail)->first();
			$msg = array(
				"PIN"=>$userData->PIN, 
				"house"=>$userData->house, 
				"studentmail"=>$userData->studentmail,
				"studentid"=>$userData->studentid,
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

	##DELETE TEMPORARY PIN##
	public function deletePIN(Request $request) {
		$mobile = $request->mobile;
		$del = Guest::where('mobile', $mobile)->update(['temporaryPIN' => 'EXPIRED']);

		$msg = array("text"=>"temporaryPIN expired");
		$datamsg = response()->json([
			'result' => $msg
		]);
		return $datamsg->content();
	}

	##LIST GUEST ACCESS##
	public function guestHistory(Request $request) {
		$studentmail = $request->studentmail;
		$check_users = User::where('studentmail', $studentmail)->where('access_type','OWNER')->count();

		if($check_users != 0){
			$guestData = Guest::where('studentmail',$studentmail)->get();
			$msg = array(
				"guestData"=>$guestData
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

	##OTP##
	public function generateOTP(Request $request) {
		$PIN = $request->PIN;
		$house = $request->house;
		$studentmail = $request->studentmail;
		$studentid = $request->studentid;
		$temporaryPIN = $request->temporaryPIN;
		$mobile = $request->mobile; 
		$check_users = User::where('studentmail', $studentmail)->where('access_type','OWNER')->count();
		if($check_users != 0){
			//for($i = 0; $i < 2; $i++) {$temporaryPIN .= mt_rand(0, 9);}
			$userData = User::where('studentmail',$studentmail)->first();	
			//$user = User::where('studentmail',$studentmail)->update(['temporaryPIN' => $temporaryPIN]);
			$guest = Guest::create([
				"PIN"=>"noaccess", 
				"house"=>$house, 
				"studentmail"=>$studentmail,
				"password"=>"noaccess",  
				"studentid"=>$studentid,
				"temporaryPIN"=>$temporaryPIN,
				"mobile" => $mobile,
				"access_type" => "GUEST",
				"access_date" => Carbon::now()
			]);
			$msg = array(
				"PIN"=>"noaccess", 
				"house"=>$house, 
				"studentmail"=>$studentmail,
				"password"=>"noaccess",  
				"studentid"=>$studentid,
				"temporaryPIN"=>$temporaryPIN,
				"mobile" => $mobile,
				"access_type" => "GUEST"
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
		$studentmail = $request->studentmail;
		$userData = User::where('studentmail',$studentmail)->where('access_type', 'OWNER')->first();

		if(!empty($userData)){
			$up = StreamLink::where("status", "INACTIVE")->update(['status' => "ACTIVE"]);
			$linkcheck = StreamLink::where('status', 'ACTIVE')->count();
			if($linkcheck != 0){
				$linkdata = StreamLink::where('status', 'ACTIVE')->first();
				$msg = array(
					"link"=>$linkdata->strlink, 
					"status"=>$linkdata->status, 
					"username"=>$userData->username, 
					"access_type"=>$userData->access_type
				);
				$datamsg = response()->json([
					'result' => $msg
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
		else{
			$msg = array("text"=>"Not authorized");
			$datamsg = response()->json([
				'result' => $msg
			]);
			return $datamsg->content();
		}
	}
	public function stopCam(Request $request) {
		$studentmail = $request->studentmail;
		$userData = User::where('studentmail',$studentmail)->where('access_type', 'OWNER')->first();

		if(!empty($userData)){
			$up = StreamLink::where("status", "ACTIVE")->update(['status' => "INACTIVE"]);
			$msg = array("text"=>"Streaming link deactivated.");
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
}
