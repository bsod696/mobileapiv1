<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Images;
use App\User;
use App\Currency;
use App\PriceApi;
use App\WalletAddress;
use App\Setting;
use App\Ref_detail;
use App\AtmMap;
use App\Balance;
use App\Gasprice;
use App\TotalAsset;
use App\Appver;
use DataTables;
use App\Transaction;
use App\Verification;
use App\Kyc;
use App\Limitation;
use App\Withdrawal;
use App\Pinkexcbuy;
use App\Pinkexcsell;
use App\Jompay;
use App\Jompay_limit;
use App\News;
use App\Redeem;
use App\Notification;
use App\StellarPod;
use App\StellarTransaction;
use App\StellarPinkexcbuy;
use App\StellarWithdrawal;
use App\StellarPinkexcsell;
use App\StellarInfo;
use App\Anypayop;
use App\Anypaytrans;
use Jcsofts\LaravelEthereum\Facade\Ethereum as Ethereum;
use Jcsofts\LaravelEthereum\Lib\EthereumTransaction;
use Carbon\Carbon;
use EthereumRPC\EthereumRPC;
use ERC20\ERC20;
use App\Lib\GoogleAuthenticator;
use App\CoinvataUsage;
use App\CoinvataLevel;
use App\ConvertPinkexc;
use Cache;
use App\State;
use App\Banklist;
//use CashaddrConverter;
use App\HibahDetail;
use Curl;
use App\PenerimaHibah;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PaymentOp;
use ZuluCrypto\StellarSdk\XdrModel\Asset;

class ApiController extends Controller
{
	 

	public function loginfp(Request $request)
	{
		$msg = array("text"=>"Currently under maintenance, please go to our website https://colony.pinkexc.com/.");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();

	}

	public function loginfp1(Request $request){
		$login_datas = User::where('username',$request->email)->orWhere('email',$request->email)->first();
		$userData = User::where('username',$request->email)->orWhere('email',$request->email)->first();
		$check_verification = Kyc::where('uid', $login_datas->id)->first();
		if($check_verification->level == "Level 2")
		{
			$level1 = '1';
			$level2 = '1';
		}
		else
		{
			$level1 = '1';
			$level2 = '0';
		}

		$id=$userData->id;
		$userData->enable_fp = '';
		$userData->currency = 'MYR';
		$userData->enable_sp = '';
		$userData->crypto = '';
		$userData->token = apiToken($id);

		if ($check_verification != null){$status = $check_verification->status;}
		else{$status = "uncompleted";}

		$msg = array("id"=>$userData->id, "username"=>$userData->username, "phone"=>$check_verification->phone, "secret_pin" => $userData->secret_pin, "country" => $check_verification->country,"email"=>$userData->email,"email_verified"=>$check_verification->email_verified,"email_hash"=>$check_verification->email_hash,"personal_verified"=>$check_verification->personal_verified,"mobile_verified"=>$check_verification->mobile_verified,"Level_1_status"=>$level1,"Level_2_status"=>$level2,"status"=>$userData->status,"google_auth_code"=>$userData->google_auth_code,"googleauth_status"=>$userData->googleauth_status,"secret_pin_status"=>$userData->secret_pin_status,"status_level2" => $status,"token"=>$userData->token,"enable_fp"=>$userData->enable_fp,"currency"=>$userData->currency,"enable_sp"=>$userData->enable_sp, "total_asset"=>'');


		$datamsg = response()->json([
			'userData' => $msg
		]);

		return $datamsg->content();
	}
	public function login(Request $request)
	{
		$msg = array("text"=>"Currently under maintenance, please go to our website https://colony.pinkexc.com/.");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();

	}

	public function login1(Request $request)
	{
		$userData = '';
		$user = User::where('username',$request->email)->orWhere('email',$request->email)->count();

		if($user != 0)
		{

			$userData = User::where('username',$request->email)->orWhere('email',$request->email)->first();
			$check_verification = Kyc::where('uid', $userData->id)->first();
			if($check_verification->level == "Level 2")
			{
				$level1 = '1';
				$level2 = '1';
			}
			else
			{
				$level1 = '1';
				$level2 = '0';
			}


			if(!empty($userData))
			{
				$id=$userData->id;
				$userData->token = apiToken($id);
				$userData->enable_fp = '';
				$userData->currency = 'MYR';
				$userData->enable_sp = '';
				$userData->crypto = '';
			}

			if($userData->password == "")
			{

				$login_data = User::where('username',$request->email)->orWhere('email',$request->email)->first();

				if($login_data->status == "2")
				{	
					$msg = array("text"=>"Your account was blocked. Please contact with administrator.");
					$datamsg = response()->json([
						'error' => $msg
					]);

					return $datamsg->content();
				}

				else if(md5($request->password) == $login_data->passwordmd5)
				{
					$login_datas = User::where('username',$request->email)->orWhere('email',$request->email)->where('passwordmd5',md5($request->password))->first();


					User::whereId($login_datas->id)
					->update([
						'password' => bcrypt($request->password)
					]);

					$check_verification = Kyc::where('uid', $login_datas->id)->first();

					if ($check_verification != null)
					{
						$status = $check_verification->status;
					}
					else
					{
						$status = "uncompleted";					}


						$msg = array("id"=>$userData->id, "username"=>$userData->username, "phone"=>$check_verification->phone, "secret_pin" => $userData->secret_pin, "country" => $check_verification->country,"email"=>$userData->email,"email_verified"=>$check_verification->email_verified,"email_hash"=>$check_verification->email_hash,"personal_verified"=>$check_verification->personal_verified,"mobile_verified"=>$check_verification->mobile_verified,"Level_1_status"=>$level1,"Level_2_status"=>$level2,"status"=>$userData->status,"google_auth_code"=>$userData->google_auth_code,"googleauth_status"=>$userData->googleauth_status,"secret_pin_status"=>$userData->secret_pin_status,"status_level2" => $status,"token"=>$userData->token,"enable_fp"=>$userData->enable_fp,"currency"=>$userData->currency,"enable_sp"=>$userData->enable_sp, "total_asset"=>'');

						$datamsg = response()->json([
							'userData' => $msg
						]);

						return $datamsg->content();
					}
					else
					{

						$msg = array("text"=>"Password is incorrect.");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();
                        //Password is incorrect
                    //return "Password is incorrect";
					}
				}
				else
				{

					if(Hash::check($request->password, $userData->password))
					{
						$login_datas = User::where('username',$request->email)->orWhere('email',$request->email)->first();

						$check_verification = Kyc::where('uid', $login_datas->id)->first();

						if ($check_verification != null)
						{
							$status = $check_verification->status;
						}
						else
						{
							$status = "";
						}


						$msg = array("id"=>$userData->id, "username"=>$userData->username, "phone"=>$check_verification->phone, "secret_pin" => $userData->secret_pin, "country" => $check_verification->country,"email"=>$userData->email,"email_verified"=>$check_verification->email_verified,"email_hash"=>$userData->email_hash,"personal_verified"=>$check_verification->personal_verified,"mobile_verified"=>$check_verification->mobile_verified,"Level_1_status"=>$level1,"Level_2_status"=>$level2,"status"=>$userData->status,"google_auth_code"=>$userData->google_auth_code,"googleauth_status"=>$userData->googleauth_status,"secret_pin_status"=>$userData->secret_pin_status,"token"=>$userData->token,"enable_fp"=>$userData->enable_fp,"currency"=>$userData->currency,"enable_sp"=>$userData->enable_sp, "status_level2" => $status, "reupload_ic_front"=>$check_verification->reupload_ic_front,"reupload_ic_end"=>$check_verification->reupload_ic_end,"reupload_bank1"=>$check_verification->reupload_bank1,"reupload_bank2"=>$check_verification->reupload_bank2,"reupload_userpic"=>$check_verification->reupload_userpic);

						$datamsg = response()->json([
							'userData' => $msg


						]);

						return $datamsg->content();
					}
					else
					{
						$msg = array("text"=>"Password is incorrect.");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();


					}
				}
			}
			else
			{
				$msg = array("text"=>"User does not exist.");
				$datamsg = response()->json([
					'error' => $msg
				]);

				return $datamsg->content();


			}

		}
		/* ### User registration ### */
		public function signup(Request $request){
			$msg = array("text"=>"This service currently unavailable.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}
		public function signup1(Request $request) {

			$username = $request->username;
			$email = $request->email;
			$password = $request->password;
			$cpassword = $request->cpassword;
			$secret_pin1 = $request->secret_pin;
			$country = $request->country;
			$urlcode = '0';

			$email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
			$secret_pin2 = preg_match('/^[0-9]{6}$/', trim($secret_pin1));
			if(!isValidUsername($username)) { echo '{"error":{"text":"Please enter valid username."}}'; }
			elseif(strlen($username)<6) { echo '{"error":{"text":"Username must be more than 6 characters."}}'; }
			elseif(!isValidEmail($email)) { echo '{"error":{"text":"Please enter valid email address."}}'; }
			elseif(strlen($password)<8) { echo '{"error":{"text":"Password must be more than 8 characters."}}'; }
			elseif($password !== $cpassword) { echo '{"error":{"text":"Password does not match with password for confirmation."}}'; }
			elseif(strlen($secret_pin1)!=6) { echo '{"error":{"text":"Secret PIN must be 6 digits."}}'; }
			elseif(!$secret_pin2) { echo '{"error":{"text":"Secret PIN must be digits only."}}'; }
			elseif($country == '') { echo '{"error":{"text":"Please choose country name"}}'; }
			else
			{  

				$userData = '';
				$mainCount = User::where('username',$username)->orWhere('email',$email)->count();
				$created=time();
				if($mainCount==0)
				{

					/*Inserting user values*/
					$hash = sha1($email);
					send_email_verify($email,'Colony Account Verification',$username,'To unlock Pinkexc Online Direct full features need to activate your account with link below.',$hash);

					$user = User::create([
						'username' => $username,
						'password' => bcrypt($password),
						'email' => $email,
						'secret_pin' => bcrypt($secret_pin1),
						'country' => $country,
						'hash' => $hash,
						'ip' => \Request::ip(),
						'time_signup' => time(),
						'status' => '1',
						'urlcode' => $urlcode,
						'secret_pin_status' => 1
					]);

					if( $country == 130){
						$limit = 100000;
						$resident = 'yes';
					}else{
						$limit = 50000;
						$resident = 'no';
					}


				         //buy
					Limitation::create([
						'uid' => $user->id,
						'fullname' => $username,
						'limit_amount' => $limit,
						'limit_usage' => 0.0000,
						'limit_balance' => $limit,
						'category' => 'buy',
						'resident' => $resident
					]);

            //sell
					Limitation::create([
						'uid' => $user->id,
						'fullname' => $username,
						'limit_amount' => $limit,
						'limit_usage' => 0.0000,
						'limit_balance' => $limit,
						'category' => 'sell',
						'resident' => $resident
					]);




					$btc = btc_generate_address($request->username);
					$userData = User::where('email',$request->email)->first();


					$systemToken = apiToken($userData->id);
					$msg = array("id"=>$userData->id, "username"=>$userData->username, "secret_pin" => $userData->secret_pin, "country" => $userData->country,"email"=>$userData->email,"status"=>$userData->status,"google_auth_code"=>$userData->google_auth_code,"googleauth_status"=>$userData->googleauth_status,"secret_pin_status"=>$userData->secret_pin_status,"token"=>$systemToken,"enable_fp"=>$userData->enable_fp,"currency"=>$userData->currency,"enable_sp"=>$userData->enable_sp);

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

		/* ### Fetch Wallet ### */
/*public function wallet(Request $request) {
$msg = array("text"=>"This service currently unavailable.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}*/
		public function wallet(Request $request) 
		{

			$id = $request->id;
			$token = $request->token;
			$crypto = $request->crypto;

			$systemToken = apiToken($id);

			if($token == $systemToken)
			{
				$get_address = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first();
				if ($get_address == null) {
					$msg = array("text"=>"Address ".$crypto." not exists.");
					$datamsg = response()->json([
						'error' => $msg
					]);

					return $datamsg->content();
				}
				else
				{
					if($crypto=="XLM"){
						$address = StellarInfo::where('id', 2)->first()->account_id;
						$memo = User::where('id',$id)->first()->username;
						$msg = array("address"=>$address, "memo"=>$memo);
					}
					else{
						if($crypto =="BCH")
						{
							$address = \CashaddrConverter::convertFromCashaddr($get_address->address);
						}
						else
						{
							$address = $get_address->address;
						}
						$msg = array("address"=>$address);
					}
					$datamsg = response()->json([
						'walletData' => $msg
					]);
					return $datamsg->content();			}
				}
				else
				{
					$msg = array("text"=>"No access");
					$datamsg = response()->json([
						'error' => $msg
					]);

					return $datamsg->content();
				}


			}


			/*###### BTC USER BALANCE ######*/

			public function get_user_balance(Request $request) 
			{
				$id = $request->id;
				$token = $request->token;
				$crypto = $request->crypto;

				$systemToken = apiToken($id);
				if($token == $systemToken)
				{
					$get_address = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first();

        //if null
					if ($get_address == null) 
					{
						$msg = array("text"=>"Address ".$crypto." not exists.");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();
					}
					else 
					{
						$converter = new \Bezhanov\Ethereum\Converter();
						if($crypto=='ETH'){eth_getbalance($id);}
						elseif($crypto=='LIFE'){life_getbalance($id);}
						elseif($crypto=='XLM'){}
						elseif($crypto=='XRP'){}
						else{all_getbalance($id, $crypto);}
            //data from database
						$current_price = PriceAPI::where('crypto', $crypto)->first()->price;
						$name = PriceAPI::where('crypto', $crypto)->first()->name;
				//$walladdress = $get_address->address;
						if($crypto=='XLM'){
							$crypto_balance = xlm_getbalance_pod($id);
						}
						else{
							$crypto_balance = $get_address->available_balance;
						}
						$crypto_balance_myr = round(($current_price * $crypto_balance), 8);

						$myr_market = number_format($current_price,2);

						$user_currency = 'myr';

						$btcBalance1 = $crypto_balance;
						$balusr = $btcBalance1;

						//$network_fee_myr = Setting::where('id', '1')->first()->network_fee;
						$network_fee_myr = getestimatefee($crypto);
						//$network_fee_myr = str_replace("\n", '', $network_fee_myr);

						$fee_data = round(($network_fee_myr / $current_price),5);
						$amt_can_withdraw =  $balusr - $fee_data;

						if($amt_can_withdraw <= 0){
							$amount_withdraw = 0.00000000;
						}
						else
						{
							//$amount_withdraw = round($amt_can_withdraw,8);
							$amount_withdraw = bcdiv($amt_can_withdraw, 1, 8);

						}

						$verify_level = DB::table('users')
						->join('kyc', 'kyc.uid', '=', 'users.id')
						->selectRaw('users.*')
						->whereRaw('users.id  = '.$id)
						->whereRaw('kyc.level = "Level 2"')
						->whereRaw('kyc.status = "completed"')
						->count();

						if($verify_level != 0)
						{
							$level_user = "Level 2";
						}
						else
						{
							$level_user = "Level 1";  
						}
						if($crypto=="ETH"){
							$gasprice = Gasprice::where('id',1)->first();
							$fast1 = $gasprice->rapid;
							$gprice = $fast1+10;
							$withdraw_commision = Setting::first()->withdrawal_commission;
							$fee = $withdraw_commision / $current_price;	

							$network = ($converter->toWei($gprice, 'gwei')*100000)*2;
							$network = ($converter->fromWei($network, 'ether'));
							$total = $network + $fee;

							$amount_can_with = $crypto_balance - $total;
							//$amount_withdraw = round($amount_can_with,8);
							$amount_withdraw = bcdiv($amount_can_with, 1, 8);
							//$amount_withdraw = $amount_withdraw-0.00000001;
							if($amount_withdraw<0){
								$amount_withdraw = 0;
							}
						}


						if($crypto=='DOGE'){
							$msg = array("crypto_amount"=>number_format($crypto_balance,2),"myr_amount"=>number_format($crypto_balance_myr,2),"currency"=>$user_currency,"fee"=>$fee_data,"amount_withdraw"=>$amount_withdraw,"myr_market"=>$myr_market,"level"=>$level_user);
							$datamsg = response()->json([
								'cryptoBalance' => $msg
							]);}
							else{
								$msg = array("crypto_amount"=>number_format($crypto_balance,5),"myr_amount"=>number_format($crypto_balance_myr,2),"currency"=>$user_currency,"fee"=>$fee_data,"amount_withdraw"=>$amount_withdraw,"myr_market"=>$myr_market,"level"=>$level_user);
								$datamsg = response()->json([
									'cryptoBalance' => $msg
								]);
							}

							return $datamsg->content();


						}
					}
					else
					{
						echo '{"error":{"text": "No access"}}';
					}

				}


				/*##### LIST TRANSACTION  #####*/
				public function transaction(Request $request) {

					$id = $request->id;
					$token = $request->token;
					$country = $request->country;
					$crypto = $request->crypto;
					$payee = '';
					$systemToken = apiToken($id);

					if($token == $systemToken)
					{
						/*
						if($crypto == 'BTC'){

							$datamsg = response()->json([
								'error' => "BTC is undermaintenance."
							]);

							return $datamsg->content();
						}*/


						$trans_object = User::where('id', $id)->first();

						$label = 'usr_' . $trans_object->username;
						$otheraccount = "";
						$data_crypto = WalletAddress::where('uid',$id)->where('crypto',$crypto)->count();
						
						if($data_crypto != 0)
						{
							if($crypto == "BTC")
							{
								$total_trans = transactions_btc($label);
							}
							else if($crypto == "BCH")
							{
								$total_trans = transactions_bch($label);
							}
							else if($crypto == "LTC")
							{
								$total_trans = transactions_ltc($label);
							}
							else if($crypto == "DASH")
							{
								$total_trans = transactions_dash($label);
							}
							else if($crypto == "DOGE")
							{
								$total_trans = transactions_doge($label);
							}
							else if($crypto == "ETH")
							{
								$address = WalletAddress::where('uid',$id)->where('crypto','ETH')->first()->address;

								$total_trans = transactions_eth($address);
							}
							else if($crypto == "LIFE")
							{
								$address = WalletAddress::where('uid',$id)->where('crypto','LIFE')->first()->address;

								$total_trans = transactions_life($address);
							}
							elseif ($crypto == "XLM"){
								$total_trans = StellarPod::where('source_id', $id)->orwhere('destination_id', $id)->orderBY('id', 'desc')->get();
							}
							elseif ($crypto == "XRP"){
								$total_trans = transactions_xrp($label);
							}
						}
						else
						{
							$total_trans = array();
						}
						if($crypto == "ETH" || $crypto == "LIFE")
						{
							$i=1;
							foreach ($total_trans as $key => $detail) {



								$converter = new \Bezhanov\Ethereum\Converter();
								$arr[0]=$detail->hash;
								$arr[1]=date('d-M-Y', $detail->timeStamp);
								$arr[2]=$detail->from;
								if ($arr[2] == $address){$arr[3] = "send";}
								else{$arr[3] = "receive";}
								$arr[4]=$detail->to;
								$arr[5]=floatval($value = $converter->fromWei(($detail->value), 'ether'));
								$arr[6]=floatval($value = $converter->fromWei(($detail->gasUsed), 'ether'));
								$refcount = Ref_detail::where('uid',$id)->where('txid',$arr[0])->where('crypto',$crypto)->count();
								if($refcount !=0){
									$refdetail = Ref_detail::where('uid',$id)->where('txid',$arr[0])->where('crypto',$crypto)->first();

									$payee=$refdetail->payee;
								}else{
									$payee='';
								}


								$myr_market = PriceAPI::where('crypto', $crypto)->first()->price;

								$market_price = number_format((($arr[5])*$myr_market), 2);

								if($detail->confirmations <= 6)
								{
									$arr[7]=$detail->confirmations;
								}
								else
								{
									$arr[7]='6+';
								}

								$admin = WalletAddress::where('uid','888')->where('crypto','ETH')->first()->address;
								if($arr[4]!=$admin){
									if($arr[3]=="send"){
										$arr_data2[] = array('id_number' => $i,'address' => $arr[4],'payee'=>$payee,'category' => ucfirst($arr[3]),'amount' => round($arr[5],5),'market_amt'=> $market_price,'timereceived' => $arr[1],'txid' => $arr[0],'confirmations' => $arr[7]);
									}
									else{
										$arr_data2[] = array('id_number' => $i,'address' => $arr[2],'payee'=>$payee,'category' => ucfirst($arr[3]),'amount' => round($arr[5],5),'market_amt'=> $market_price,'timereceived' => $arr[1],'txid' => $arr[0],'confirmations' => $arr[7]);
									} 	                    

								}					
								$i++;
							}


						}
						elseif($crypto == 'XLM'){
							$i=1;
							foreach($total_trans as $arr_transaction){
								$myr_market = PriceAPI::where('crypto', $crypto)->first()->price;
								$market_price = number_format((($arr_transaction->send_token)*$myr_market), 2);

								if($arr_transaction->str_transaction_id!='' && $arr_transaction->str_transaction_id!=null && $arr_transaction->str_transaction_id!="0"){
									$otheraccount = StellarTransaction::where('id', $arr_transaction->str_transaction_id)->first();
									$status = $arr_transaction->status;
									if($arr_transaction->status == 'receive'){
										$address = $otheraccount->source_accID;
									}
									else{
						//echo($arr_transaction->str_transaction_id);
										$address = $otheraccount->destination_accID;
									}
									$time = str_replace('T',' ',$otheraccount->str_datetime);
									$time = str_replace('Z','',$time);
									$time = date("d-M-Y", strtotime($time));;
									$memo = '';
									$memo = StellarTransaction::where('id', $arr_transaction->str_transaction_id)->first()->memo;
								}
								else{
									if($arr_transaction->status == 'receive'){
										if($arr_transaction->source_id!='wallet'&& $arr_transaction->source_id!='admin'){
											$address = User::where('id', $arr_transaction->source_id)->first()->username;
										}
										else{
											$address = $arr_transaction->source_id;
										}
										$status = "receive";
									}
									else{
										if($arr_transaction->destination_id == $id){
											$address = '';
											if($arr_transaction->source_id=="wallet"||$arr_transaction->source_id=="admin"){$address = $arr_transaction->source_id;}
											else{$address = User::where('id', $arr_transaction->source_id)->first()->username;}
											$status = "receive";
										}
										else{
											$address = '';
											if($arr_transaction->destination_id=="wallet"||$arr_transaction->destination_id=="admin"){$address = $arr_transaction->source_id;}
											else{$address = User::where('id', $arr_transaction->destination_id)->first()->username;}
											$status = "send";
										}


									}
									$times = Carbon::parse($arr_transaction->created_at);
									$time = $times->format('d-M-Y');
									$memo = StellarWithdrawal::where('id', $arr_transaction->pod_id)->first()->memo;					
								}
								if($arr_transaction->hash == null || $arr_transaction->hash == ''){
									$hash = "Colony";
								}
								else{$hash = $arr_transaction->hash;}
								$arr_data2[] = array('id_number' => $i,'address' => $address ,'memo'=> $memo,'category' => ucfirst($status),'amount' => round($arr_transaction->send_token,5),'market_amt'=> $market_price,'timereceived' => $time,'hash' => $hash,'status'=>$arr_transaction->str_status);
								$i++;
							}
						}
						else
						{
							$i=1;
							foreach($total_trans as $arr_transaction)
							{	

								$myr_market = PriceAPI::where('crypto', $crypto)->first()->price;
								if($crypto == 'DOGE'){
									$market_price = number_format((($arr_transaction->amount)*$myr_market), 2);
									$amount_price = round($arr_transaction->amount,2);
								}
								else{
									$market_price = number_format((($arr_transaction->amount)*$myr_market), 2);
									$amount_price = round($arr_transaction->amount,5);
								}

								if($arr_transaction->category == "move")
								{
									$payee='';
									if($arr_transaction->otheraccount != "usr_pinkexc_fees")
									{            

										$confirm = '6+';

										$countdata = substr($arr_transaction->amount,0,1);



										if($countdata == '-')
										{
											$change_category = 'send';
										}
										else
										{
											$change_category = 'receive';
										}

										if($arr_transaction->otheraccount == "usr_admin")
										{
											$arr_transaction->otheraccount = "usr_Buy&Sell";
						}
						if($arr_transaction->otheraccount == "usr_jompay")
						{
							$arr_transaction->otheraccount = "usr_Utilities";
						}

						/*
						else
						{
							$otheraccount = $arr_transaction->otheraccount;
						}*/
						$address = str_replace('usr_','',$arr_transaction->otheraccount);
						if($address == 'niha324345'){$address='mysaiful165';}
						$arr_data2[] = array('id_number' => $i,'address' => $address ,'payee'=>$payee, 'category' => ucfirst($change_category),'amount' => $amount_price,'market_amt'=> $market_price,'timereceived' => date('d M Y',$arr_transaction->time),'txid' => 'FREE','confirmations' => $confirm);
					}


					
					

				}
				else
				{

					$confirmations = $arr_transaction->confirmations;
					if($confirmations <= 5)
					{
						$confirm = $confirmations;
					}
					else
					{
						$confirm = '6+';
					}
					$refcount = Ref_detail::where('uid',$id)->where('txid',$arr_transaction->txid)->where('crypto',$crypto)->count();
					if($refcount !=0){
						$refdetail = Ref_detail::where('uid',$id)->where('txid',$arr_transaction->txid)->where('crypto',$crypto)->first();

						$payee=$refdetail->payee;
					}
					else{
						$payee='';
					}


					$change_category = $arr_transaction->category;
						//$otheraccount = $arr_transaction->address;

					$arr_data2[] = array('id_number' => $i,'address' => $arr_transaction->address ,'payee'=>$payee,'category' => ucfirst($change_category),'amount' => $amount_price,'market_amt'=> $market_price,'timereceived' => date('d M Y',$arr_transaction->time),'txid' => $arr_transaction->txid,'confirmations' => $confirm);
					


				}
				$i++;					
			}

		}



		if(!empty($arr_data2))
		{
			if($crypto=='XLM'){
				$array_data3 = $arr_data2;
				$datamsg = response()->json([
					'listTransaction' => $array_data3
				]);
			}
			else{
				$array_data3 = array_reverse($arr_data2);
				$datamsg = response()->json([
					'listTransaction' => $array_data3
				]);
			}
			return $datamsg->content();
		}
		else
		{

			$datamsg = response()->json([
				'error' => "No Transaction Found."
			]);

			return $datamsg->content();
		}
	}
	else
	{
		$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}
}


/*##### CHANGE PASSWORD #####*/

public function change_password(Request $request) {                            

	$id = $request->id;
	$token = $request->token;
	$cpass = $request->cpass;
	$npass = $request->npass;
	$cnpass = $request->cnpass;
	$secret_pin = $request->secret_pin;

	$systemToken = apiToken($id);

	if($token == $systemToken)
	{

		$check_users = User::where('id', $id)->first();

		if($secret_pin == '') 
		{
			$msg = array("text"=>"Please enter secret pin");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();
		}
		else
		{
			if(Hash::check($request->cpass, $check_users->password))
			{
				if(Hash::check($secret_pin, $check_users->secret_pin))
				{
					if ($request->npass  == $request->cnpass)
					{
						$password = Hash::make($request->npass);

						User::whereId($id)
						->update([
							'password' => $password
						]);

						$datamsg = response()->json([
							'message' => 'Password successfully changed'
						]);

						return $datamsg->content();
					}
					else
					{

						$datamsg = response()->json([
							'error' => 'Password is not match with confirm password'
						]);

						return $datamsg->content();
					}

				}
				else
				{
					$datamsg = response()->json([
						'error' => 'Secret PIN do not match'
					]);

					return $datamsg->content();

				}

			}
			else
			{
				$datamsg = response()->json([
					'error' => 'The old password is wrong'
				]);

				return $datamsg->content();

			}
		}
	}
	else
	{
		$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}

}


/*##### FORGOT PASSWORD #####*/

public function reset_password(Request $request) {                         

	$email = $request->email;

	$sql_email = User::where('email',$email)->count();

	if($sql_email==0) 
	{  
		$msg = array("text"=>"No such user with this email address.");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}
	else 
	{
		$hash = md5($email);

		$user = User::where('email',$email)->first();

		$id = $user->id;

		User::whereId($id)
		->update([
			'email_hash' => $hash
		]);

		send_reset_password($user->email,'Colony Account Reset Password ',$user->username,'To unlock Colony full features need to activate your account with link below.',$hash);


		$msg = array("text"=>"Email with instructions to reset password was sent. Please check your inbox.");
		$datamsg = response()->json([
			'success' => $msg
		]);

		return $datamsg->content();

	}


}


/*#### COUNTRY ######*/

public function country()
{

	$currencyDetails =  Cache::remember('currencyDetails', 22*60, function() {
		return Currency::all();
	});

	$datamsg = response()->json([
		'currencyDetails' => $currencyDetails
	]);

	return $datamsg->content();


}

/*#### ATM ######*/

public function atm1(){
	$msg = array("title"=>"Temporary closed until further notice.");
	$datamsg = response()->json([
		'atmDetails' => $msg
	]);

	return $datamsg->content();

}



public function atm()
{

	$atmDetails = AtmMap::all();
	foreach($atmDetails as $key =>$atmDetail)
	{

		$data[] = array('title' => $atmDetails[$key]['atm_title'],'latitude' => $atmDetails[$key]['latitude'], 'longitude' => $atmDetails[$key]['longitude'], 'atm_address1' => $atmDetails[$key]['atm_address1'] , 'atm_address2' => $atmDetails[$key]['atm_address2'] , 'atm_poscode' => $atmDetails[$key]['atm_poscode'] , 'atm_city' => $atmDetails[$key]['atm_city'] , 'atm_state' => $atmDetails[$key]['atm_state'],'operation' => $atmDetails[$key]['operation'] );

		$location[] = array($atmDetails[$key]['atm_title'],$atmDetails[$key]['latitude'],$atmDetails[$key]['longitude'],$atmDetails[$key]['atm_address1'],$atmDetails[$key]['atm_address2'],$atmDetails[$key]['atm_poscode'],$atmDetails[$key]['atm_city'],$atmDetails[$key]['atm_state']);
	}

	$locations = json_encode($location);
	$stlocation = str_replace("{","[",$locations);
	$ndlocation = str_replace("}","]",$stlocation);

	echo '{"atmDetails": '.json_encode($data).',"location":'.json_encode($ndlocation).'}';
}

/*#### USER DETAILS ######*/

public function detailuser(Request $request)
{

	$id = $request->id;
	$token = $request->token;

	$systemToken = apiToken($id);

	if($token == $systemToken)
	{

		$row = User::where('id',$id)->first();

		$mainver = Kyc::where('uid',$id)->count();

		$currencyDetails = Currency::where('id',$row->country)->orderBy('country')->first();
		$picture = 'user_profile/defaultpicture.png';
		if($mainver == 0)
		{
			$name = 'NULL';
			$mobile = '+'.$currencyDetails->ccode.'0';
			$level = 'Level 1';
			$status = '';
			$bankname1 = '';
			$bankname2 = '';
			$banknumber1 = '';
			$banknumber2 = '';
		}
		else
		{
			$userDet = Kyc::where('uid',$id)->first();

			$name = $userDet->name;
			$mobile = '+'.$currencyDetails->ccode.''.$userDet->phone;
			$level = $userDet->level;
			$status = $userDet->status;
			$bankname1 = $userDet->bankname1;
			$bankname2 = $userDet->bankname2;
			$banknumber1 = $userDet->banknumber1;
			$banknumber2 = $userDet->banknumber2;

			if($row->picture==''){$picture = 'user_profile/defaultpicture.png';}
			else{$picture = 'user_profile/'.$row->picture;}
		}

		$email = $row->email;
		$country = $currencyDetails->country;

		$check_verification = Kyc::where('uid', $id)->first();
		if($check_verification->level == "Level 2")
		{
			$level1 = '1';
			$level2 = '1';
			$reupload_ic_front=$check_verification->reupload_ic_front;
			$reupload_ic_end =$check_verification->reupload_ic_end;
			$reupload_userpic =$check_verification->reupload_userpic;
			$reupload_bank1 =$check_verification->reupload_bank1;
		}
		else
		{
			$level1 = '1';
			$level2 = '0';
			$reupload_ic_front='';
			$reupload_ic_end ='';
			$reupload_userpic ='';
			$reupload_bank1 ='';
		}

		$datamsg = response()->json([
			"profilepic"=>"https://colony.pinkexc.com/assets/".$picture,
			"username"=>$row->username,
			"name"=>$name,
			"mobile"=>$mobile,
			"level"=>$level,
			"email"=>$email,
			"country"=>$country,
			"status"=>$status,
			"bankname1"=>$bankname1,
			"bankname2"=>$bankname2,
			"banknum1"=>$banknumber1,
			"banknum2"=>$banknumber2,
			"Level_2_status"=>$level2,
			"reupload_ic_front" =>$reupload_ic_front,
			"reupload_ic_end" =>$reupload_ic_end,
			"reupload_userpic" =>$reupload_userpic,
			"reupload_bank1" =>$reupload_bank1,
			"colonyid" => $row->id_colony,
		]);

		return $datamsg->content();

	}
	else
	{
		$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}


}

/*###### WITHDRAW ######*/

public function withdraw(Request $request) { 

	$id = $request->id;
	$token = $request->token;
	$balance = $request->balance;
	$fee = $request->fee;
	$amount = $request->amount_withdraw;
	$address = $request->address;
	$secret_pin = $request->pin;
	$crypto = $request->crypto;
	$pin = $secret_pin;
	$totalfunds =$amount + $fee;
		//$amt_can_withdraw =  $balance - $fee;

	$systemToken = apiToken($id);

	$balance = round((WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance),5);

	if($crypto == 'BTC'){ $checkadd = preg_match('/[13][a-km-zA-HJ-NP-Z1-9]{25,34}/', trim($address));}
	elseif($crypto == 'BCH'){ $checkadd = preg_match('/[13qpQP][a-zA-Z0-9]{25,41}/', trim($address));}
	elseif($crypto == 'DOGE'){ $checkadd = preg_match('/D{1}[5-9A-HJ-NP-U]{1}[1-9A-HJ-NP-Za-km-z]{32}/', trim($address));}
	elseif($crypto == 'DASH'){ $checkadd = preg_match('/X[1-9A-HJ-NP-Za-km-z]{33}/', trim($address));}
	elseif($crypto == 'ETH'){ $checkadd = preg_match('/0x[a-fA-F0-9]{40}/', trim($address));}
	elseif($crypto == 'LTC'){ $checkadd = preg_match('/[L3][a-km-zA-HJ-NP-Z1-9]{26,33}/', trim($address));}
	elseif($crypto == 'XRP'){ $checkadd = preg_match('/r[1-9a-km-zA-HJ-NP-Z]{25,35}/', trim($address));}
	else{
		$msg = array("text"=>"Unvalid crypto.");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}
	/*
	if($crypto == 'BTC'){
			$msg = array("text"=>"This service currently not available.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();
		}
	*/
	
	if(!$checkadd){$msg = array("text"=>"Invalid wallet address.");
	$datamsg = response()->json([
		'error' => $msg
	]);

	return $datamsg->content();}
	else{
		if($token == $systemToken)
		{
			$label = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->label;

			if(!is_numeric($amount)) { 
				$msg = array("text"=>"Please enter valid amount.");
				$datamsg = response()->json([
					'error' => $msg
				]);

				return $datamsg->content();


			}
			elseif(empty($address))
			{

				$msg = array("text"=>"Please enter recipient address.");
				$datamsg = response()->json([
					'error' => $msg
				]);

				return $datamsg->content();
			}
			elseif($balance < $amount) 
			{
				$msg = array("text"=>"Sorry, you do not have enough funds for withdrawal ".$amount.".");
				$datamsg = response()->json([
					'error' => $msg
				]);

				return $datamsg->content();
			}
			else {

				$current_price = PriceAPI::where('crypto', $crypto)->first()->price;
				$price_myr = round($current_price, 2);
				$change = PriceAPI::where('crypto', $crypto)->first()->percentage;
				$name = PriceAPI::where('crypto', $crypto)->first()->name;
				$walladdress = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->address;
				$crypto_balance = round((WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance),5);

				$crypto_balance_myr = round(($current_price * $crypto_balance), 2);

        //NETWORK FEE RM0.5
				//$network_fee_myr = Setting::where('id', '1')->first()->network_fee;
				//$network_fee_crypto = round(($network_fee_myr / $price_myr),5);
				$network_fee_myr = getestimatefee($crypto);
				$network_fee_crypto = round(($network_fee_myr / $price_myr), 5);

        //WITHDRAWAL COMMISSION RM0.5
				$withdrawal_comm_myr = Setting::where('id', '1')->first()->withdrawal_commission;
				$withdrawal_comm_crypto = round(($withdrawal_comm_myr / $price_myr),5);


        //re-calculate crypto amount
				$total_fee_crypto = $network_fee_crypto + $withdrawal_comm_crypto;
				$crypto_amount = round(($amount*$current_price),5);
				$totalAll = $crypto_amount + $total_fee_crypto ;

				$total_fee_myr = round(($current_price * $total_fee_crypto),2);
				$max_send_crypto = $crypto_balance - $total_fee_crypto;
				$max_send_myr = $crypto_balance_myr - $total_fee_myr;
//END CALCULATION
				$check_users = User::where('id', $id)->first();

				$check_label_in_node = get_label_crypto($crypto, $address);
				


            // dd($check_label_in_node);
				if(Hash::check($request->pin, $check_users->secret_pin))
				{
					if ($check_label_in_node == "") {

						/* EXTERNAL */
						if (($crypto_balance == 0) || ($amount > $crypto_balance )) {

							$msg = array("text"=>"Insufficient Balance");
							$datamsg = response()->json([
								'error' => $msg
							]);

							return $datamsg->content();
						} 
						else 
						{
							//$txid = send_crypto($crypto, $label, $address, $amount);
							$fee = $withdrawal_comm_crypto;

							$moveto_pinkexc = withdrawal_fees_crypto($crypto, $label, $fee);
																					
							$txid = send_crypto_comment($crypto, $label, $address, $amount,'withdraw');
							
						}
					} else {
						/* INTERNAL */
						if(($crypto_balance == 0) ||($amount > $crypto_balance)){
							$msg = array("text"=>"Insufficient Balance");
							$datamsg = response()->json([
								'error' => $msg
							]);

							return $datamsg->content();

						}
						else
						{

                //$txid = move_crypto($crypto, $label, $check_label_in_node, $amount); //will return 1 as true
							$txid = move_crypto_comment($crypto, $label, $check_label_in_node, $amount, 'withdraw');
							$fee = 'free';

						}
					}

            //update available balance
					if($check_label_in_node == "")
					{
						$getip = PriceApi::where('crypto', $crypto)->first()->ip_getinfo;
						$gettxid = $txid;

						$post = [
							'id' => 19,
							'txid' => $txid
						];

						$ch = curl_init($getip);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

						$result = curl_exec($ch);
						$data = json_decode($result);
						curl_close($ch);
						$result_array = count($data->details);

		//send
						$nfee = -($data->details[0]->fee);
                //external
						$type = 'external';
						$available_bal = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance;
						$available_bal = round(($available_bal - $amount - $withdrawal_comm_crypto - $network_fee_crypto  ),8);
					}
					else
					{
                //internal
						$nfee = '';
						$type = 'internal';
						$available_bal = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance;
						$available_bal = round(($available_bal - $amount),8);
					}


                //RECORD INTO DATABASE
					if($txid)
					{

                  //INSERT WITHDRAWAL
						$insert_verify = Withdrawal::create([
							'uid' => $id,
							'before_bal'=>$crypto_balance,
							'after_bal'=>$available_bal,
							'status' => 'success',
							'nfee' => $nfee,
							'amount' => $amount,
							'recipient' => $address ,
							'myr_amount' => $crypto_amount,
							'fee' => $fee,
							'rate' => $current_price,
							'txid' => $txid,
							'type' => $type,
							'crypto' => $crypto 

						]);

						$update_balance = WalletAddress::where('uid', $id)->where('crypto', $crypto)
						->update([
							'available_balance' => $available_bal
						]);


						$datamsg = response()->json([
							'success' => 'Successfully withdraw'
						]);

						return $datamsg->content();

					}
					else
					{

                //INSERT WITHDRAWAL
						$insert_verify = Withdrawal::create([
							'uid' => $id,
							'status' => 'failed',
							'amount' => $amount,
							'recipient' => $address ,
							'fee' => '',
							'txid' => 'Invalid address',
							'crypto' => $crypto    

						]);

						$update_balance = WalletAddress::where('uid', $id)->where('crypto', $crypto)
						->update([
							'available_balance' => $available_bal
						]);

						$msg = array("text"=>"Withdraw is failed. Please try again");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();

					}

				}
				else
				{
					$msg = array("text"=>"Secret PIN do not match");
					$datamsg = response()->json([
						'error' => $msg
					]);

					return $datamsg->content();
				}


			}
		}

	}
}
public function submitEth(Request $request){
$msg = array("text"=>"Under maintenance.");
            $datamsg = response()->json([
                'error' => $msg
            ]);
    
            return $datamsg->content();

        }
  //Submit Withdraw ETH
        public function submitEth1(Request $request){
        	$converter = new \Bezhanov\Ethereum\Converter();
        	$id = $request->id;
        	$token = $request->token;
        	$crypto_type = $request->crypto_type;
        	$crypto_address = $request->crypto_address;
        	$myr_amount = $request->myr_amount;
        	$crypto_amount = $request->crypto_amount;
        	$price = $request->price;
        	$secretpin = $request->secretpin;


        	$gasL = '100000';
        	$gasP = $converter->toWei($request->price, 'gwei');
        	$amount = $request->crypto_amount;

    //Get Data ETH
        	$current_price = PriceAPI::where('name', 'Ethereum')->first()->price;
        	$myr_amount = $crypto_amount * $current_price;
        	$withdraw_commision = Setting::first()->withdrawal_commission;
        	$fee = $withdraw_commision / $current_price;
        	$address_fee = WalletAddress::where('uid',888)->where('crypto','ETH')->first()->address;
        	$check_users = User::where('id', $id)->first();
        	$systemToken = apiToken($id);
        	$checkadd = preg_match('/0x[a-fA-F0-9]{40}/', trim($crypto_address));
        	if(!$checkadd){
        		$msg = array("text"=>"Invalid wallet address.");
        		$datamsg = response()->json([
        			'error' => $msg
        		]);
        		return $datamsg->content();
        	}
        	else{
        		$count = Withdrawal::where('uid', $id)->where('crypto','ETH')->count();
        		if($count != 0 && $count != ''){

        			$getdate =Withdrawal::where('uid', $id)->where('crypto','ETH')->orderBy('id', 'desc')->first()->created_at;
        			$newtimestamp = strtotime($getdate.'+10 minutes');

        			$currentdate = date('Y-m-d H:i:s');
        			$tmp = strtotime($currentdate);

        			$test = ($newtimestamp - $tmp)/60;
        			$newdate = date('Y-m-d H:i:s', $newtimestamp);

        			$new = number_format($test,0);

        			if($currentdate <= $newdate ){
        				$msg = array("text"=>"Please wait for $new minutes to make a new withdrawal for ETH. Thank you.");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);

        				return $datamsg->content();

        			}
        		}
        		if($token == $systemToken)
        		{
        			if(	$crypto_type != '' && $crypto_address != '' && $myr_amount != '' && $crypto_amount != '' &&	$price != '' &&	$secretpin != ''){
        				if(Hash::check($request->secretpin, $check_users->secret_pin)){
            	//Check Address ETH (From)
            	//Address User
        					$address_from = WalletAddress::where('uid',$id)->where('crypto','ETH')->first()->address;
				//$converter = new \Bezhanov\Ethereum\Converter();

        					$balance_from = Ethereum::eth_getBalance($address_from,'latest',TRUE);
        					$balance_from = number_format($balance_from, 0, '', '');
        					$balance_fromWei = $converter->fromWei($balance_from, 'ether');

        					if($balance_fromWei != 0)
        					{
        						$estFee = $converter->fromWei($gasP*$gasL, 'ether');

        						$bal = $balance_fromWei - (($estFee*2)+$fee);

        						if (strpos($bal, '-') == true) {

        							$bal = 0;
        						}
        					}
        					else
        					{

        						$estFee = $converter->fromWei($gasP*$gasL, 'ether');

        						$bal = 0;
        					}

        					$totalFee = ($estFee*2)+$fee;

        					$totalBal = $totalFee + $amount;



        					if($totalBal < $balance_fromWei)
        					{
        						$balance_after =  $balance_fromWei - $totalBal;
              //Send fund to external wallet
        						$from = $address_from;
        						$to = $request->crypto_address;
        						$gas = '0x'.dec2hex($gasL);
        						$gasPrice = '0x'.dec2hex($gasP);
        						$value = '0x'.dec2hex($converter->toWei($amount, 'ether'));

        						$transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);

        						$txid =  Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');

        						if($txid != '')
        						{

        							Withdrawal::create([
        								'uid' => $id,
        								'status' => 'success',
        								'amount' => number_format($amount,8),
        								'before_bal'=>number_format($balance_fromWei,8),
        								'after_bal'=>number_format($balance_after,8),
        								'recipient' => $request->crypto_address,
        								'fee' => number_format($fee,8),
        								'myr_amount'=>$myr_amount,
        								'txid' => $txid,
        								'rate' => $current_price,
        								'gasPrice' => $gasP,
        								'gasLimit' => $gasL,
        								'date' => Carbon::now(),
        								'crypto' => 'ETH',
        								'type'=>'external'
        							]);
        						}
        						else
        						{
        							Withdrawal::create([
        								'uid' => $id,
        								'status' => 'failed',
        								'amount' => number_format($amount,8),
        								'recipient' => $request->crypto_address,
        								'fee' => number_format($fee,8),
        								'txid' => '0',
        								'gasPrice' => $gasP,
        								'gasLimit' => $gasL,
        								'date' => Carbon::now(),
        								'crypto' => 'ETH'
        							]);
        						}


              //Send fund to external wallet
        						$from_fee = $address_from;
        						$to_fee = $address_fee;
        						$gas_fee = '0x'.dec2hex($gasL);
        						$gasPrice_fee = '0x'.dec2hex($gasP);
        						$value_fee = '0x'.dec2hex($converter->toWei($fee, 'ether'));

        						$transaction_fee = new EthereumTransaction($from_fee, $to_fee, $value_fee, $gas_fee, $gasPrice_fee);

        						$txid_fee =  Ethereum::personal_sendTransaction($transaction_fee,'Pinkexc@22');
					//dd($value_fee);
        						if($txid_fee != '')
        						{

        							Withdrawal::create([
        								'uid' => 888,
        								'status' => 'success',
        								'amount' => number_format($fee,8),
        								'recipient' => 'admin',
        								'fee' => '0',
        								'txid' => $txid_fee,
        								'gasPrice' => $gasP,
        								'gasLimit' => $gasL,
        								'date' => Carbon::now(),
        								'crypto' => 'ETH'
        							]);
        						}
        						else
        						{
        							Withdrawal::create([
        								'uid' => 888,
        								'status' => 'failed',
        								'amount' => number_format($fee,8),
        								'recipient' => 'admin',
        								'fee' => '0',
        								'txid' => '0',
        								'gasPrice' => $gasP,
        								'gasLimit' => $gasL,
        								'date' => Carbon::now(),
        								'crypto' => 'ETH'
        							]);
        						}
        						$balance_user = Ethereum::eth_getBalance($address_from,'latest',TRUE);

        						$balanceuser_fromWei = $converter->fromWei($balance_user, 'ether');

        						WalletAddress::where('uid', $id)
        						->where('crypto', 'ETH')
        						->update(['available_balance' => number_format($balanceuser_fromWei,8)]);

        						$msg = array("text"=>'Successfully withdraw');
        						$datamsg = response()->json([
        							'success' => $msg
        						]);

        						return $datamsg->content();

        					}
        					else
        					{

        						$msg = array("text"=>'Insufficient funds. Maximum you can withdraw is '.number_format($bal,8).' ETH');
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();

        					}


        				}
        				else
        				{

        					$msg = array("text"=>"Secret PIN do not match");
        					$datamsg = response()->json([
        						'error' => $msg
        					]);

        					return $datamsg->content();
        				}
        			}
        			else
        			{
        				$msg = array("text"=>"Please fill in the field");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);

        				return $datamsg->content();
        			}


        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}
        	}

        }


        /*###### RECENT ORDER BUY ######*/
        public function listbuy(Request $request)
        {
        	$id = $request->id;
        	$token = $request->token;
        	$crypto = $request->crypto;

        	$systemToken = apiToken($id);

        	if($token == $systemToken)
        	{
        		if($crypto=='XLM'){
        			$pinkexcbuyc = StellarPinkexcbuy::where('uid', $id)->count();
        			if($pinkexcbuyc)
        			{
        				$pinkexcbuy = StellarPinkexcbuy::where('uid', $id)->orderBy('id', 'desc')->get();
        				$datamsg = response()->json([
        					'buyData' => $pinkexcbuy
        				]);
        				return $datamsg->content();

        			}
        			else{
        				$msg = array("text"=>"You do not have transaction yet.");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);

        				return $datamsg->content();
        			}

        		}
        		else{
        			$pinkexcbuyc = Pinkexcbuy::where('uid', $id)->where('crypto',$crypto)->count();
        			if($pinkexcbuyc)
        			{
        				$pinkexcbuy = Pinkexcbuy::where('uid', $id)->where('crypto',$crypto)->orderBy('id', 'desc')->get();
        				$datamsg = response()->json([
        					'buyData' => $pinkexcbuy
        				]);
        				return $datamsg->content();

        			}
        			else
        			{

        				$msg = array("text"=>"You do not have transaction yet.");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);

        				return $datamsg->content();

        			}
        		}	}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}


        	}

        	/*###### CURRENT PRICE  ######*/
        	public function currentprice(Request $request)
        	{

        		$id = $request->id;
        		$token = $request->token;
        		$crypto = $request->crypto;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{
        			$current_price = PriceAPI::where('crypto', $crypto)->first()->price;
        			if($crypto == "DOGE"){
        				$current_price = round($current_price,6);
        			}
        			else{
        				$current_price = round($current_price,2);
        			}
        			$datamsg = response()->json([
        				'price' => $current_price,
        				'currency' => 'myr'
        			]);

        			return $datamsg->content();
        		}
        		else
        		{

        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}

        	}

        	/*###### CURRENT PRICE - BUY ######*/
        	public function current(Request $request)
        	{
        		$id = $request->id;
        		$token = $request->token;
        		$crypto = $request->crypto;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{
            //Limit
        			$limit = Limitation::where('uid',$id)->where('category','buy')->orderBy('id', 'desc')->limit(1)->first();

        			if($limit->limit_level == "Level 1")
        			{
        				$dataLimit = "Your Current Limit Level 1 Balance : ". number_format($limit->limit_balance,2)." MYR";
        			}
        			else if($limit->limit_level == "Level 2")
        			{
        				$dataLimit = "Your Current Limit Level 2 Balance : ". number_format($limit->limit_balance,2)." MYR";
        			}
        			else
        			{
        				$dataLimit = "NULL";
        			}


        			$current_price = PriceAPI::where('crypto', $crypto)->first()->price;

        			$setting = Setting::where('id',1)->first();

        			$price_instantbuy = ($current_price * $setting->buy_comission) + $current_price;
        			$price_instantsell =  ($current_price -($current_price * $setting->sell_comission));

        			if($crypto=='DOGE'){
        				$price_instantbuy = round($price_instantbuy,5);
        			}
        			else{
        				$price_instantbuy = round($price_instantbuy,2);
        			}

        			$datamsg = response()->json([
        				'currentMarket' => $current_price,
        				'price_instantbuy' => $price_instantbuy,
        				'price_instantsell' => round($price_instantsell,2),
        				'dataLimit' => $dataLimit,
        				'limit' => $limit->limit_level,
        				'limit_balance' => $limit->limit_balance		]);

        			return $datamsg->content();

        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}

        	}
        	/*###### INSTANT BUY ######*/
        	public function instantbuy(Request $request)
        	{

        		$id = $request->id;
        		$token = $request->token;
        		$amountMyr = $request->amountMyr;
        		$currentPrice = $request->currentPrice;
        		$limit = $request->limit;
        		$dataLimit = $request->dataLimit;
        		$crypto = $request->crypto;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{
        			$buyeremail = User::where('uid',$id)->first()->email;
        			$buyaddress = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->address;
        			$buyusername = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->label;
        			$crypto_balance = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance;

			//$currentPrice = PriceAPI::where('crypto',$crypto)->first()->price;

        			$totalBtc = $amountMyr / $currentPrice;
        			$btcAmount = round($totalBtc,8);

        			$limit_balance = Limitation::where('uid',$id)->where('category','buy')->first()->limit_balance;

        			$lbalance2 = $limit_balance - $amountMyr;

        			$row_buy = Pinkexcbuy::where('uid',$id)->where('status','unpaid')->first();

        			$status = count($row_buy->status);

        			$refusr =  User::where('uid',$id)->first()->username;

        			$refnum = 'PODB'.$refusr;

        			$verificationinfo = Kyc::where('uid',$id)->first();

        			if($verificationinfo->level == "Level 1" || $verificationinfo->level == "Level 2" && $verificationinfo->status != "completed")
        			{
        				$msg = array("text"=>"Sorry, Please upgrade to level 2");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);

        				return $datamsg->content();

        			}
        			else
        			{

        				if($status == 1)
        				{

        					$msg = array("text"=>"Sorry, Please upload receipt to make another request or cancel to make new request.");
        					$datamsg = response()->json([
        						'error' => $msg
        					]);

        					return $datamsg->content();

        				}
        				else
        				{

        					if($amountMyr < 100){

        						$msg = array("text"=>"Sorry, The minimum limit for instant buy is 100 MYR per day. Please try again. Thank you.");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();

        					}
        					elseif($amountMyr > 100000)
        					{
        						$msg = array("text"=>"Sorry, The maximum limit for instant buy is 100000 MYR per day. Please try again. Thank you.");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();

        					}
        					elseif($amountMyr > $limit_balance)
        					{
        						$msg = array("text"=>"Sorry, limit balance is now is RM ".$limit_balance."");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();


        					}
        					elseif($limit_balance == 0)
        					{
        						$msg = array("text"=>"You are reach the limitation in this month");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();


        					}
        					else
        					{
        						$tableSetting = Setting::where('id',1)->first();

        						$process_time = $tableSetting->process_time;
        						$timeout = $process_time * 60;
        						$timeout = time() + $timeout;
        						$time = time();


        						$stmt_buy = Pinkexcbuy::create([
        							'uid' => $id,
        							'username' => $buyusername,
        							'currentbal' => $crypto_balance,
        							'crypto_amount' => $btcAmount,
        							'afterbal' => '0.00000',
        							'myr_amount' => $amountMyr,
        							'walladdress' => $buyaddress,
        							'receipt' => '',
        							'bankname' => '',
        							'banknum' => '',
        							'accname' => '',
        							'date' => Carbon::now(),
        							'status' => 'unpaid',
        							'refnumber' => $refnum,
        							'crypto_release' => '0',
        							'trans_no' => '',
        							'rate' => $currentPrice,
        							'process_time' => $process_time,
        							'timeout' => $timeout,
        							'start_time' => $time,
        							'pay_type' => 'uploadreceipt',
        							'txid' => '',
        							'crypto' => $crypto,
        						]);

        						$msg = array("text"=>"Your request has been sent. Please make a payment below. Thank you.");
        						$datamsg = response()->json([
        							'success' => $msg
        						]);

        						return $datamsg->content();

        						/* email to admin*/
			// $to = 'finance@pinkexc.com';
			// $fromEmail = $buyeremail; 
			// $fromName = $buyeremail;

			// $subject = 'Pinkexc Online | Instant Buy Request';
			// $type = 'Request Instant Buy ';

			// $message=" From: $buyeremail \n Username : $buyusername \n Type: $type \n Amount of BTC : $btcAmount ( $myrAmount MYR ) \n Wallet Address : $buyaddress \n \n  Status : Request";

        						/* Start of headers */ 
			// $headers = "From: $buyeremail;"; 



			// $flgchk = mail ("$to", "$subject", "$message", "$headers"); 

        					}
        				}
        			}

        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}
        	}


        	/*###### INSTANT BUY ######*/
        	public function checkstatusbuy(Request $request)
        	{

        		$id = $request->id;
        		$token = $request->token;
        		$crypto = $request->crypto;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{
        			$row_buy = Pinkexcbuy::where('uid',$id)->where('status','unpaid')->where('crypto',$crypto)->first();

        			$status = count($row_buy->status);

        			$datamsg = response()->json([
        				'status' => $status
        			]);

        			return $datamsg->content();
        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}
        	}

        	/*###### CANCEL BUY #######*/

        	public function cancelbuy(Request $request)
        	{
        		$id = $request->id;
        		$token = $request->token;
        		$buy_id = $request->buy_id;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{
        			$buyeremail = User::where('id',$id)->first()->email;

        			$row_buy = Pinkexcbuy::where('id',$buy_id)->first();

        			$buyusername = $row_buy->username;
        			$btcAmount = $row_buy->crypto_amount;
        			$myrAmount = $row_buy->myr_amount;
        			$buyaddress = $row_buy->walladdress;

        			$updt1 = Pinkexcbuy::where('id', $buy_id)
        			->update([
        				'status' => 'cancel'
        			]);

        			echo '{"success":{"text":"Your request has been cancelled."}}';

        			/* email to admin*/
			// $to ='finance@pinkexc.com';
			// $fromEmail = $buyeremail; 
			// $fromName = $buyeremail;; 

			// $subject = 'Pinkexc Online | Instant Buy Cancelled';
			// $type = 'Cancel Instant Buy ';

			// $message=" From: $buyeremail \n Username : $buyusername \n Type: $type \n Amount of BTC : $btcAmount ( $myrAmount MYR ) \n Wallet Address : $buyaddress \n \n Status : Order Cancelled";

			// /* Start of headers */ 
			// $headers = "From: $buyeremail;"; 



			// $flgchk = mail ("$to", "$subject", "$message", "$headers"); 


        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}
        	}

        	/*###### PROCEED BUY #######*/

        	public function proceedbuy(Request $request)
        	{

        		$id = $request->id;
        		$token = $request->token;
        		//$buy_id = $request->buy_id;
        		$receipt = $request->receipt;
			$crypto = $request->crypto;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{
				$row_user = User::where('id',$id)->first();
				$buyeremail = $row_user->email;
				$buyusername = $row_user->username;
				if($crypto!='XLM'){
        				$row_buy = Pinkexcbuy::where('uid',$id)->where('status','unpaid')->first();
					
				}
				else{
					$row_buy = StellarPinkexcbuy::where('uid',$id)->where('status','unpaid')->first();	
				}

				if($row_buy==null){
        				$msg = array("text"=>"Sorry, you have no pending order.");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);
        				return $datamsg->content();
				}

            			$myrAmount = $row_buy->myrAmount;

        			$row_limit = Limitation::where('uid',$id)->where('category','buy')->orderBy('id', 'desc')->limit(1)->first();

        			$limit_balance = $row_limit->limit_balance;
        			$limit_datenow = $row_limit->daterecord;
        			$fullname = $row_limit->fullname;
				$datenow = date("Y-m-d H:i:s");
							
				$time = time();

        			$buyid = $row_buy->id;

        			$verificationinfo = Kyc::where('uid',$id)->first();

        			if($verificationinfo->level == "Level 1" || $verificationinfo->level == "Level 2" && $verificationinfo->status != "completed")
        			{
        				$msg = array("text"=>"Sorry, Please upgrade to level 2");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);
        				return $datamsg->content();
				}
				//dd($time > $row_buy->timeout);
							
				if($time > $row_buy->timeout){
					$price = PriceAPI::where('crypto',$crypto)->first();

					if($crypto == 'XLM'){
						$crypto_amount = bcdiv($row_buy->myr_amount/$price->price_pinkexcbuy, 1, 7);
					}
					else{
						$crypto_amount = bcdiv($row_buy->myr_amount/$price->price_pinkexcbuy, 1, 5);
					}
						$afterbal = $row_buy->currentbal + $crypto_amount;
						$rate = bcdiv($price->price_pinkexcbuy, 1, 5);
						$current_rate = $price->price;
						$change_price = 'yes';
				}
				else{
					$crypto_amount = $row_buy->crypto_amount;
					$afterbal = $row_buy->afterbal;
					$rate = $row_buy->rate;
					$current_rate = $row_buy->current_rate;
					$change_price = $row_buy->change_price;
				}

				if($crypto!='XLM'){
        				$updt1 = Pinkexcbuy::where('id', $buyid)
        				->update([
						'receipt' => $receipt,
						'crypto_amount'=>$crypto_amount,
						'afterbal'=>$afterbal,
						'rate'=>$rate,
						'current_rate'=>$current_rate,
						'change_price'=>$change_price,
						'status'=>'process',
						]);
					}
				else{
					$updt1 = StellarPinkexcbuy::where('id', $buyid)
        				->update([
						'receipt' => $receipt,
						'crypto_amount'=>$crypto_amount,
						'afterbal'=>$afterbal,
						'rate'=>$rate,
						'current_rate'=>$current_rate,
						'change_price'=>$change_price,
						'status'=>'process',
						]);
					}

        			if( date("Y-m",strtotime($limit_datenow)) == date("Y-m",strtotime($datenow))){$lbalance2 = $limit_balance - $myrAmount;}
				else{$lbalance2 = 100000 - $myrAmount;}
								
					$rows_limitbuy = Limitation::create([
						'uid' => $id,
						'fullname' => $fullname,
						'limit_level' => 'Level 2',
						'limit_amount' => '100000',
						'daterecord' => $datenow,
						'limit_usage' => $myrAmount,
						'limit_balance' => $lbalance2,
						'category' => 'buy',
					]);

					/* email to admin*/
        				$to = 'sales@pinkexc.com';
        				$fromEmail = $buyeremail; 
        				$fromName = $buyeremail; 

        				$subject = 'Colony | Instant Buy Pending';
        				$type = 'Instant Buy Pending ';

        				$message=" From: $buyeremail \n Username : $buyusername \n Type: $type \n Amount of $crypto : $crypto_amount ( $myrAmount MYR ) \n Full Name : $fullname \n Address : $address \n \n Status : Pending \n\n ";

        				/* Start of headers */ 
        				$headers = "From: $fromName"; 

        				//$flgchk = mail ("$to", "$subject", "$message", "$headers"); 
					//send_email_touser($to, $subject,$buyusername,$message,$buyid,$crypto);
					send_email_toadmin($to, $subject,'Colony',$message,$buyid,$crypto);

					$to = $buyeremail;
        				$fromEmail ='noreply@pinkexc.com';
        				$fromName = 'noreply@pinkexc.com'; 

        				$subject = 'Colony | Instant Buy ';

        				$message="Dear $buyusername,\nyou have requested for instant buy with Colony. The process will take approximately 24 to 48 hours of working day. We will notify and send an email to you after the process is complete. \n\nThank you for your business.";

        				/* Start of headers */ 
        				$headers = "From: $fromName";

					//$flgchk = mail ("$to", "$subject", "$message", "$headers");
					send_email_touser($to, $subject,$buyusername,$message,$buyid,$crypto);
								
								
					$content = "Dear " . $buyusername . ", you have requested for instant buy " . $crypto . " with amount " . $crypto_amount . $crypto . " ( " . $myrAmount . " MYR ). The process will take within 24 until 48 hours on working days. We will notify and send an email to you after the process is complete. Thank you for your business. ";
					$sendnotify = Notification::create([
							'uid' => $id,
							'title' => 'Instant Buy Request',
							'content' => $content,
							'read' => '0'
						]);
								

        				$msg = array("text"=>"Hi ".$buyusername.", We have received your request for instant buy with Colony. The process will take approximately 24 to 48 hours of working day. We will notify and send an email to you after the process is complete. Thank you for your business.");
        				$datamsg = response()->json([
        					'success' => $msg
        				]);

        				return $datamsg->content();

        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}

        	}
        	/*###### CURRENT PRICE - SELL ######*/
        	public function currentsell(Request $request)
        	{
        		$id = $request->id;
        		$token = $request->token;
        		$crypto = $request->crypto;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{
			//Bank Name
        			$bankDet = Kyc::where('uid',$id)->first();

            //Limit
        			$limit = Limitation::where('uid',$id)->where('category','sell')->orderBy('id','desc')->limit(1)->first();

        			if($limit->limit_level == "Level 1")
        			{
        				$dataLimit = "Your Current Limit Level 1 Balance : ". number_format($limit->limit_balance,2)." MYR";
        			}
        			else if($limit->limit_level == "Level 2")
        			{
        				$dataLimit = "Your Current Limit Level 2 Balance : ". number_format($limit->limit_balance,2)." MYR";
        			}
        			else
        			{
        				$dataLimit = "NULL";
        			}

        			$price2 = PriceAPI::where('crypto',$crypto)->first()->price;

        			$rate = Setting::where('id',1)->first()->sell_comission;

        			$price_instantsell = ($price2-($price2 * $rate));

        			if($crypto == 'DOGE'){
        				$price_instantsell = round($price_instantsell,5);
        			}
        			else{
        				$price_instantsell = round($price_instantsell,2);
        			}

        			if($crypto == 'ETH'){
        				$get_address = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->address;
        				$converter = new \Bezhanov\Ethereum\Converter();
        				$price = Gasprice::where('id',1)->first()->rapid;
        				$wallet_balance = Ethereum::eth_getBalance($get_address,'latest',TRUE);
        				$value = $converter->fromWei($wallet_balance, 'ether');
        				$gasL = '100000';
        				if($price == 0 || $price ==''){$price=50;}
        				$gasP = $converter->toWei($price, 'gwei');

        				$estFee = $converter->fromWei($gasP*$gasL, 'ether');
        				if($value == ''){$value=0;}
        				$totalFee = $estFee;
        				$totalBal = $totalFee;

        				$max = $value - $totalFee;
        				if (strpos($max, '-') !== false) {
        					$max = 0;
        				}
        			}
        			else{$max = 0;}

        			$datamsg = response()->json([
        				'price_instantsell' => $price_instantsell,
        				'dataLimit' => $dataLimit,
        				'max' => $max,
        				'limit' => $limit->limit_level,
        				'limit_balance' => $limit->limit_balance,
        				'bankname1' => $bankDet->bankname1,
        				'banknumber1' => $bankDet->banknumber1,
        				'bankname2' => $bankDet->bankname2,
        				'banknumber2' => $bankDet->banknumber2,
        				'account_name' => $bankDet->name,
        				'daterecord' => $limit->daterecord

        			]);

        			return $datamsg->content();
        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}
        	}

        	/*###### INSTANT BUY ######*/
        	public function checkstatussell(Request $request)
        	{

        		$id = $request->id;
        		$token = $request->token;
        		$crypto = $request->crypto;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{

        			$row_sell = Pinkexcsell::where('uid',$id)->where('status','unpaid')->where('crypto',$crypto)->first();

        			$status = count($row_sell->status);

        			$datamsg = response()->json([
        				'status' => $status
        			]);

        			return $datamsg->content();

        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}

        	}

		public function instantsell(Request $request) {
			$msg = array("text"=>"Service is currently unavailable.");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();


		}


        	public function instantsell2(Request $request) {

        		$id = $request->id;
        		$token = $request->token;
        		$amount = $request->amount;
        		$currentPrice = $request->currentPrice;
        		$limit = $request->limit;
        		$dataLimit = $request->dataLimit;
        		$bankname = $request->bankname;
        		$account_name = $request->account_name;
        		$payment_method = 'Online Banking';
        		$limit_datenow = $request->daterecord;
        		$datenow = date("Y-m-d H:i:s");
        		$crypto = $request->crypto;
        		$banknum = $request->banknum;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{
        			$current_price = PriceAPI::where('crypto', $crypto)->first()->price_pinkexcsell;
        			$totalMyr = $amount * $current_price;
        			$myr_amount = round($totalMyr,2);
		/*		
		if($crypto == 'BTC')
		{
			$msg = array("text"=>"Sorry, BTC under maintenance.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}*/


        			$check_verification = Kyc::where('uid', $id)->first();
        			$check_user_level2 = User::where('id', $id)->first();

        			if($check_verification->level == "Level 2")
        			{
        				$level1 = '1';
        				$level2 = '1';
        			}
        			else
        			{
        				$level1 = '1';
        				$level2 = '0';
        			}

        			if($check_verification == null || $check_verification->status == "pending for review" || $check_verification->status == "pending for reupload" || $check_verification->status == "uncompleted" || $check_verification->status == "pending review for reupload" || $check_verification->status == "rejected" || $check_verification->status == "reupdate")
        			{
        				$msg = array("text"=>"Sorry, Please upgrade to level 2");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);

        				return $datamsg->content();

        			}elseif($check_verification->status == 'completed' && $level2 == '0')
        			{

        				$msg = array("text"=>"Sorry, Please upgrade to level 2");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);

        				return $datamsg->content();
        			}
        			elseif($crypto=="LIFE"){
        				$msg = array("text"=>$crypto." is unavailable for sell until further notice.");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);

        				return $datamsg->content();
        			}
        			else
        			{


        				$get_address = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first();
        				$percentage_sell = Setting::where('id', '1')->first()->sell_comission;

              //if null
        				if ($get_address == null) {

        					$msg = array("text"=>"Sorry, Please add wallet ".$crypto);
        					$datamsg = response()->json([
        						'error' => $msg
        					]);

        					return $datamsg->content();

        				} 
        				else
        				{

            //data from database
        					$current_price = PriceAPI::where('crypto', $crypto)->first()->price;
        					$change = PriceAPI::where('crypto', $crypto)->first()->percentage;
        					$name = PriceAPI::where('crypto', $crypto)->first()->name;
        					$walladdress = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->address;
        					if(($crypto != 'ETH' && $crypto != 'XRP'))
        					{
							all_getbalance($id, $crypto);
        						$crypto_balance = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance;
        					}
        					else if($crypto == "XRP")
        					{
        						$current_price = PriceApi::where('crypto', $crypto)->first()->price;

        						$price_myr = round($current_price, 2);
        						$crypto_balance = round((WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance), 5);
        						$crypto_balance_myr = round(($current_price * $crypto_balance), 2);

	        			//NETWORK FEE RM0.5
        						$network_fee_myr = Setting::where('id', '1')->first()->network_fee;
        						$network_fee_crypto = round(($network_fee_myr / $price_myr), 5);
	        			//WITHDRAWAL COMMISSION RM0.5
        						$sell_comm_myr = Setting::where('id', '1')->first()->sell_commission;
        						$sell_comm_crypto = round(($sell_comm_myr / $price_myr), 5);


	        			//re-calculate crypto amount
        						$total_fee_crypto = $network_fee_crypto + $sell_comm_crypto;
        						$crypto_amount = round(($myr_amount / $current_price), 8);

        						$totalAll = $crypto_amount + $total_fee_crypto;
        						$total_amount = $crypto_balance -  $totalAll ;

        					}

        					else
        					{
        						$converter = new \Bezhanov\Ethereum\Converter();
        						$price = Gasprice::where('id',1)->first()->rapid;
        						$wallet_balance = Ethereum::eth_getBalance($get_address->address,'latest',TRUE);
        						$wallet_balance = number_format($wallet_balance, 0, '', '');
        						$crypto_balance = $converter->fromWei($wallet_balance, 'ether');
        						$gasL = '100000';
        						if($price == 0 || $price ==''){$price=50;}
        						$gasP = $converter->toWei($price, 'gwei');

        						if($crypto_balance != 0)
        						{
        							$estFee = $converter->fromWei($gasP*$gasL, 'ether');

        							$bal = $crypto_balance - $estFee;

        							if (strpos($bal, '-') !== false) {
        								$bal = 0;
        							}
        						}
        						else
        						{
        							$estFee = $converter->fromWei($gasP*$gasL, 'ether');
        							$bal = 0;
        						}

        						$totalFee = $estFee;
        						$totalBal = $totalFee + $amount;
        						$max = $crypto_balance - $totalFee;
        						if (strpos($max, '-') !== false) {$max = 0;}

        						if($totalBal > $crypto_balance)
        						{
        							$msg = array("text"=>"Insufficient balance. You can only withdraw $max");
        							$datamsg = response()->json([
        								'error' => $msg
        							]);

        							return $datamsg->content();
        						}
        					}

        					$crypto_balance_myr = round(($current_price * $crypto_balance), 2);
        					$label = $get_address->label;

            //CALCULATION
        					if($crypto=='DOGE'){
        						$sell_price = round( ($current_price - ($current_price * $percentage_sell) ), 6);
        					}
        					else{
        						$sell_price = round( ($current_price - ($current_price * $percentage_sell) ), 2);
        					}
        					$new_crypto_amount = round($myr_amount/$sell_price,5);
        					$afterbal = round(($crypto_balance - $new_crypto_amount),8);

        					$refnum = 'PODS'.$check_user_level2->username;
        					$trans_no = 'MPODS'.$check_user_level2->username;


            //CHECK TABLE LIMITATION
        					$sqlbuylimit = Limitation::where('uid', $id)->where('category','sell')->orderBy('id', 'desc')->first();

        					$fullname = $sqlbuylimit->fullname;
        					$limit_amount = $sqlbuylimit->limit_amount;
        					$limit_fullname = $sqlbuylimit->fullname;
        					$limit_usage = $sqlbuylimit->limit_usage;
        					$limit_balance = $sqlbuylimit->limit_balance;
        					$limit_datenow = $sqlbuylimit->daterecord;
        					$datenow = date("Y-m-d H:i:s");
        					$lbalance2 = $limit_balance - $myr_amount;

        					$count = Pinkexcsell::where('uid', $id)->where('crypto',$crypto)->count();

        					$lmyr = 100;
        					if($id==26441){$lmyr = 2;}
        					if($id==680){$lmyr = 2;}
						if($id==29285){$lmyr = 1;}

        					if($count != 0 && $count != ''){

        						$getdate =Pinkexcsell::where('uid', $id)->where('crypto',$crypto)->orderBy('id', 'desc')->first()->created_at;
        						$newtimestamp = strtotime($getdate.'+10 minutes');

        						$currentdate = date('Y-m-d H:i:s');
        						$tmp = strtotime($currentdate);

        						$test = ($newtimestamp - $tmp)/60;
        						$newdate = date('Y-m-d H:i:s', $newtimestamp);

        						$new = number_format($test,0);

        						if($currentdate <= $newdate ){
        							$msg = array("text"=>"Please wait for $new minutes to make a new sell order for $crypto. Thank you.");
        							$datamsg = response()->json([
        								'error' => $msg
        							]);

        							return $datamsg->content();

        						}
        					}

        					if($crypto == 'XRP' && ($total_amount <= 20) ){
        						$msg = array("text"=>'You must leave 20 XRP reserve in the account you are sending from.');
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();

        					}
        					elseif( (round($crypto_balance,5) == 0) || ($amount > round($crypto_balance,5))){

        						$msg = array("text"=>"Insufficient Balance");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();

        					}elseif ($myr_amount < $lmyr) {

        						$msg = array("text"=>"Must be more than 100 MYR");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();

        					} elseif ($myr_amount > 100000) {

        						$msg = array("text"=>"Sorry, The maximum limit for direct sell is 100000 MYR per month.  ");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();

        					} elseif ($limit_balance == 0) {

        						$msg = array("text"=>"You reach the limitation in this month");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();

        					} elseif ($myr_amount > $limit_balance) {

        						$msg = array("text"=>"Sorry, your limit balance is " . $limit_balance . " MYR");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();

        					} else {


        					}
               //MOVE TO WALLET ADMIN
				//$move = move_crypto($crypto,$label,'usr_admin',$amount);
        					$move = move_crypto_comment($crypto, $label,'usr_admin', $amount,'sell');

        					if ($move == null || $move == '') {

                        //INSERT PINKEXCSELL (to record the failed)
        						$insert_verify = Pinkexcsell::create([
        							'uid' => $id,
        							'username' => $get_address->label,
        							'currentbal' => $crypto_balance,
        							'crypto_amount' => $amount,
        							'afterbal' => $afterbal,
        							'myr_amount' => $myr_amount,
        							'sender' => $get_address->address,
        							'status' => 'failed',
        							'refnumber' => $refnum,
        							'crypto_release' => '0',
        							'rate' => $sell_price,
        							'paymethod' => $payment_method,
        							'crypto' => $crypto,
        							'bankname'=> $bankname,
        							'accname'=>$account_name,
        							'trans_no'=>$trans_no,
        							'accnum' => $banknum,
        							'current_rate' => $current_price
        						]);

							$trans_no = refforbuy('mobile', $crypto, $insert_verify->id);

                                			$updt = Pinkexcsell::where('id',$insert_verify->id)->update([
                                    					'trans_no'=>$trans_no
                                				]);


        						$msg = array("text"=>"#3 Request failed. Please try again");
        						$datamsg = response()->json([
        							'error' => $msg
        						]);

        						return $datamsg->content();


        					}else{

                         //UPDATE AVAILABLE BALANCE
        						$available_bal = $crypto_balance - $amount;
        						$update_balance = WalletAddress::where('uid', $id)->where('crypto', $crypto)
        						->update([
        							'available_balance' => $available_bal
        						]);


                    //INSERT PINKEXCSELL
        						$insert_verify = Pinkexcsell::create([
        							'uid' => $id,
        							'username' => $get_address->label,
        							'currentbal' => $crypto_balance,
        							'crypto_amount' => $amount,
        							'afterbal' => $afterbal,
        							'myr_amount' => $myr_amount,
        							'sender' => $get_address->address,
        							'status' => 'process',
        							'refnumber' => $refnum,
        							'crypto_release' => '0',
        							'rate' => $sell_price,
        							'paymethod' => $payment_method,
        							'crypto' => $crypto,
        							'bankname'=> $bankname,
        							'accname'=>$account_name,
        							'trans_no'=>$trans_no,
        							'accnum' => $banknum,
        							'current_rate' => $current_price,
        							'txid' => $move
        						]);

							$trans_no = refforbuy('mobile', $crypto, $insert_verify->id);

                                			$updt = Pinkexcsell::where('id',$insert_verify->id)->update([
                                    					'trans_no'=>$trans_no
                                				]);

                        //insert into limitation table

        						if (date("Y-m", strtotime($limit_datenow)) == date("Y-m", strtotime($datenow))) {

                                           //INSERT LIMITATION
        							$insert_limit = Limitation::create([
        								'uid' => $id,
        								'fullname' => $fullname,
        								'limit_level' => 'Level 2',
        								'limit_amount' => '100000',
        								'daterecord' => $datenow,
        								'limit_usage' => $myr_amount,
        								'limit_balance' => $lbalance2,
        								'category' => 'sell'
        							]);
        						} else {
        							$newbalance = 100000 - $myr_amount;

                                                 //INSERT LIMITATION
        							$insert_limit = Limitation::create([
        								'uid' => $id,
        								'fullname' => $fullname,
        								'limit_level' => 'Level 2',
        								'limit_amount' => '100000',
        								'daterecord' => $datenow,
        								'limit_usage' => $myr_amount,
        								'limit_balance' => $newbalance,
        								'category' => 'sell'
        							]);
        						}

        						if($insert_limit){
        							$username = User::where('id', $id)->first()->username;
	                        		//notification
        							$content = "Dear " . $username . ", you have requested for instant sell " . $crypto . " with amount " . $new_crypto_amount . $crypto . " ( " . $myr_amount . " MYR ). The process will take within 24 until 48 hours on working days. We will notify and send an email to you after the process is complete. Thank you for your business. ";
        							$sendnotify = Notification::create([
        								'uid' => $id,
        								'title' => 'Instant Sell Request',
        								'content' => $content,
        								'read' => '0'
        							]);
        						}

                        ////

        						$msg = array("text"=>"Your request successfully submitted. Your request is in queue and will be process on 48 hours in working days.");
        						$datamsg = response()->json([
        							'success' => $msg
        						]);

        						return $datamsg->content();

        					}
        				}
        			}
        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);
        			return $datamsg->content();

        		}

        	}

        	/*###### RECENT ORDER SELL ######*/
        	public function listsell(Request $request)
        	{
        		$id = $request->id;
        		$token = $request->token;
        		$crypto = $request->crypto;

        		$systemToken = apiToken($id);

        		if($token == $systemToken)
        		{
        			if($crypto=='XLM'){
        				$pinkexcbuyc = StellarPinkexcsell::where('uid', $id)->count();
        				if($pinkexcbuyc)
        				{
        					$pinkexcbuy = StellarPinkexcsell::where('uid', $id)->orderBy('id', 'desc')->get();
        					$datamsg = response()->json([
        						'sellData' => $pinkexcbuy
        					]);
        					return $datamsg->content();

        				}
        				else{
        					$msg = array("text"=>"You do not have transaction yet.");
        					$datamsg = response()->json([
        						'error' => $msg
        					]);

        					return $datamsg->content();
        				}

        			}
        			else{

        				$pinkexcsell = Pinkexcsell::where('uid', $id)->where('crypto',$crypto)->count();
        				if($pinkexcsell != 0)
        				{
        					$pinkexcbuy = Pinkexcsell::where('uid', $id)->where('crypto',$crypto)->orderBy('id', 'desc')->get();
        					$datamsg = response()->json([
        						'sellData' => $pinkexcbuy
        					]);

        					return $datamsg->content();

        				}
        				else
        				{

        					$msg = array("text"=>"You do not have transaction yet.");
        					$datamsg = response()->json([
        						'error' => $msg
        					]);

        					return $datamsg->content();

        				}
        			}
        		}
        		else
        		{
        			$msg = array("text"=>"No access");
        			$datamsg = response()->json([
        				'error' => $msg
        			]);

        			return $datamsg->content();
        		}


        	}

        	/*############################## GENERAL SETTING ##################################*/

        	/*###### SEND MOBILE VERIFICATION ######*/

        	public function sendmobile(Request $request) {

        		$uid = $request->id;
        		$id = $request->country;
        		$mobile_number = $request->mobile_number;

        		$checkmobile = preg_match("/^[0-9]+$/", $mobile_number);

        		if(!$checkmobile){ echo '{"error":{"text":"Please enter valid mobile number."}}'; }
        		elseif(strlen($mobile_number<9)){ echo '{"error":{"text":"Mobile number cant be below 9 digit."}}'; }
        		else{ 

        			$cu = Currency::where('id',$id)->first();
        			$ccode = $cu->ccode;
        			$ch = curl_init();

        			curl_setopt($ch, CURLOPT_URL, "https://api.authy.com/protected/json/phones/verification/start?api_key=7CTtkR3ZQ82iDhqSyEcepeVkfodpD9Y5");
        			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        			curl_setopt($ch, CURLOPT_POSTFIELDS, "via=sms&phone_number=$mobile_number&country_code=$ccode");
        			curl_setopt($ch, CURLOPT_POST, 1);

        			$headers = array();
        			$headers[] = "Content-Type: application/x-www-form-urlencoded";
        			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        			$result = curl_exec($ch);

        			$updt1 = User::where('id', $uid)
        			->update([
        				'phone' => $mobile_number
        			]);

        			echo '{"success":{"text":"Your mobile number has been added. Now you can verify it in the form provided."}}';

        			if (curl_errno($ch)) {
        				echo '{"error":{"text":"'.curl_error($ch).'"}}';
        			}
        			curl_close ($ch);	
        		}

        	}

        	/*###### CONFIRM MOBILE VERIFICATION ######*/

        	public function confirmmobile(Request $request) {

        		$uid = $request->id;
        		$mobile_number = $request->mobile_number;
        		$id = $request->country;
        		$sms_code = $request->sms_code;

        		$checksmscode = preg_match("/^[0-9]+$/",$sms_code);
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        		if(!$checksmscode){ echo '{"error":{"text":"Please enter valid sms code."}}'; }
        		elseif(strlen($sms_code)<4){ echo '{"error":{"text":"Sms code must be more than 4 numbers."}}'; }
        		else{

        			$cu = Currency::where('id',$id)->first();
        			$ccode = $cu->ccode;
        			$ch = curl_init();

        			$ch = curl_init();
        			curl_setopt($ch, CURLOPT_URL, "https://api.authy.com/protected/json/phones/verification/check?api_key=7CTtkR3ZQ82iDhqSyEcepeVkfodpD9Y5&phone_number=$mobile_number&country_code=$ccode&verification_code=$sms_code");
        			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        			$result = curl_exec($ch);
        			$arr = json_decode($result);
        			if($arr->success == true)
        			{
        				$updt1 = User::where('id', $uid)
        				->update([
        					'mobile_verified' => '1'
        				]);

        				$check = User::where('id', $uid)->first()->email_verified;
        				$check1 = User::where('id', $uid)->first()->mobile_verified;

        				if ($check1 == 1 || $check == 1){
				//$updt1 = User::where('id', $uid)->update(['Level_1_status' => '1']);
        					echo '{"success":{"text":"You have successfully register under level 1!"}}';}
        					else {echo '{"error":{"text":"Your mobile has been verified. Please verify your email."}}';}
        				}
        				else
        				{
        					echo '{"error":{"text":"Sms code entered was wrong or took too long to verify."}}';
        				}

        			}
        		}              

        		/*###### RESEND MOBILE VERIFICATION ######*/

        		public function resendmobile(Request $request){

        			$uid = $request->id;
        			$id = $request->country;
        			$mobile_number = $request->mobile_number;

        			$cu = Currency::where('id',$id)->first();
        			$ccode = $cu->ccode;

        			$ch = curl_init();

        			curl_setopt($ch, CURLOPT_URL, "https://api.authy.com/protected/json/phones/verification/start?api_key=7CTtkR3ZQ82iDhqSyEcepeVkfodpD9Y5");
        			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        			curl_setopt($ch, CURLOPT_POSTFIELDS, "via=sms&phone_number=$mobile_number&country_code=$ccode");
        			curl_setopt($ch, CURLOPT_POST, 1);

        			$headers = array();
        			$headers[] = "Content-Type: application/x-www-form-urlencoded";
        			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        			$result = curl_exec($ch);
        			$arr = json_decode($result);
        			if($arr->success == true){echo '{"success":{"text":"We have resend the sms code"}}';}
        			else{echo '{"error":{"text":"'.curl_error($ch).'"}}';}
        			curl_close ($ch);	  
        		}

        		/*###### NEW MOBILE NUMBER VERIFICATION ######*/

        		public function newmobilenumber(Request $request){

        			$uid = $request->id;
        			$id = $request->country;
        			$newmobile_number = $request->mobile_number;

        			$checkmobile = preg_match("/^[0-9]+$/", $newmobile_number);

        			if(!$checkmobile){ echo '{"error":{"text":"Please enter valid mobile number."}}'; }
        			elseif(strlen($newmobile_number<9)){ echo '{"error":{"text":"Please enter valid mobile number."}}'; }
        			else{

        				$mainCount = User::where('phone',$newmobile_number)->count();
        				if($mainCount==0){

        					$cu = Currency::where('id',$id)->first();
        					$ccode = $cu->ccode;

        					$ch = curl_init();

        					curl_setopt($ch, CURLOPT_URL, "https://api.authy.com/protected/json/phones/verification/start?api_key=7CTtkR3ZQ82iDhqSyEcepeVkfodpD9Y5");
        					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        					curl_setopt($ch, CURLOPT_POSTFIELDS, "via=sms&phone_number=$newmobile_number&country_code=$ccode");
        					curl_setopt($ch, CURLOPT_POST, 1);

        					$headers = array();
        					$headers[] = "Content-Type: application/x-www-form-urlencoded";
        					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        					$result = curl_exec($ch);
        					$arr = json_decode($result);
        					if($arr->success == true){
        						$updt1 = Kyc::where('id', $uid)
        						->update(['phone' => $newmobile_number]);
        						echo '{"success":{"text":"Your new mobile number has been updated. Now you can verify back it in the form provided."}}';
        					}
        					else {
        						echo '{"error":{"text":"Server cannot connect. Please enter again."}}';
        					}
        					curl_close ($ch);	
        				}
        				else{echo '{"error":{"text":"This mobile number is already been used. Please enter another."}}';}
        			}
        		}

        		/*###### PERSONAL VERIFICATION ######*/
        		public function personalverify2(Request $request){
        			echo '{"error":{"text":"This service is currently not available."}}';

        		}


        		public function personalverify(Request $request){

        			$touseremail =  $request->email;
        			$personalid =  $request->id;
        			$icname = $request->icname;
        			$icaddress1 = $request->icaddress1;
        			$icaddress2 = $request->icaddress2;
        			$icnum = $request->icnum;
        			$icstate = $request->icstate;
        			$iccity = $request->iccity;
        			$icpostal = $request->icpostal;
        			$icbirth = $request->icbirth;
        			$gender = $request->gender;
        			$mothername = $request->mothername;

        			$checkname = preg_match('/^[a-zA-Z\s@]+$/', trim($icname));
        			$checkaddress = preg_match('#^[a-zA-Z0-9-,/\s]+$#', trim($icaddress1));
        			$checkgender = preg_match('/^[a-zA-Z]+$/', trim($gender));
        			$checkic = preg_match('/^[a-zA-Z0-9\s]+$/', trim($icnum));
        			$checkstate = preg_match('/^[a-zA-Z\s,]+$/', trim($icstate));
        			$checkcity = preg_match('/^[a-zA-Z\s,]+$/', trim($iccity));
        			$checkpc = preg_match('/^[a-zA-Z0-9\s]+$/', trim($icpostal));
        			$checkdate = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$icbirth);
        			$checkaddress1 = preg_match('#^[a-zA-Z0-9-,/\s]+$#', trim($icaddress2));

				$user = User::where('id',$personalid)->first()->created_at;
				$year = Carbon::createFromFormat('Y-m-d H:i:s', $user)->year;
				//dd($year);
				if($year >2018){
				$msg = array("text"=>'This service is currently unavailable.');
     						$datamsg = response()->json([
     						'error' => $msg
     					]);
				}




        			if(!$checkname){echo '{"error":{"text":"Please enter valid fullname."}}';}
        			elseif(!$checkaddress){echo '{"error":{"text":"Please enter valid address."}}';}
        			elseif(!$checkaddress1){echo '{"error":{"text":"Invalid address 2."}}';}
        			elseif(strlen($icname)<6){echo '{"error":{"text":"Please enter valid fullname"}}';}
        			elseif(!$checkdate){echo '{"error":{"text":"Please follow the format YYYY-MM-DD"}}';}
        			elseif(!$checkic){ echo '{"error":{"text":"Please enter valid IC or passport number."}}';}
        			elseif(strlen($icnum)<6){echo '{"error":{"text":"Please enter valid IC or passport number."}}';}
        			elseif(!$checkpc){echo '{"error":{"text":"Please enter valid postal code."}}';}
        			elseif(!$checkcity){echo '{"error":{"text":"Please enter valid city."}}';}
        			elseif(!$checkstate){echo '{"error":{"text":"Please enter valid state."}}';}  
        			elseif($gender == ''){echo '{"error":{"text":"Please enter gender."}}';}
        			elseif($mothername == ''){echo '{"error":{"text":"Please enter mother name."}}';}
        			elseif(!$checkgender){echo '{"error":{"text":"Please enter gender."}}';}    
        			else{
        				$msg = array("id"=>$personalid,"email"=>$touseremail,"icname"=>$icname,"icaddress1"=>$icaddress1,"icaddress2"=>$icaddress2,"icnum"=>$icnum,"icstate"=>$icstate,"iccity"=>$iccity,"icpostal"=>$icpostal,"icbirth"=>$icbirth,"gender"=>$gender,"mothername"=>$mothername);
        				$datamsg = response()->json(['personalData' => $msg]);

        				return $datamsg->content();
        			}	

        		}
        		/*###### Reenter PERSONAL VERIFICATION ######*/

        		public function repersonalverify(Request $request){

        			$touseremail =  $request->email;
        			$personalid =  $request->id;
        			$icname = $request->icname;
        			$icaddress1 = $request->icaddress1;
        			$icaddress2 = $request->icaddress2;
        			$icnum = $request->icnum;
        			$icstate = $request->icstate;
        			$iccity = $request->iccity;
        			$icpostal = $request->icpostal;
        			$icbirth = $request->icbirth;
        			$gender = $request->gender;
        			$level = $request->level;
        			$mothername = $request->mothername;

        			$checkname = preg_match('/^[a-zA-Z\s@]+$/', trim($icname));
        			$checkaddress = preg_match('#^[a-zA-Z0-9-,/\s]+$#', trim($icaddress1));
        			$checkgender = preg_match('/^[a-zA-Z]+$/', trim($gender));
        			$checkic = preg_match('/^[a-zA-Z0-9\s]+$/', trim($icnum));
        			$checkstate = preg_match('/^[a-zA-Z\s,]+$/', trim($icstate));
        			$checkcity = preg_match('/^[a-zA-Z\s,]+$/', trim($iccity));
        			$checkpc = preg_match('/^[a-zA-Z0-9\s]+$/', trim($icpostal));
        			$checkdate = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$icbirth);
        			$checkaddress1 = preg_match('#^[a-zA-Z0-9-,/\s]+$#', trim($icaddress2));

        			if(!$checkaddress){echo '{"error":{"text":"Please enter valid address."}}';}
        			elseif(!$checkaddress1){echo '{"error":{"text":"Invalid address 2."}}';}
        			elseif(!$checkname){echo '{"error":{"text":"Please enter valid fullname."}}';}
        			elseif(strlen($icname)<6){echo '{"error":{"text":"Please enter valid fullname"}}';}
        			elseif(!$checkdate){echo '{"error":{"text":"Please follow the format YYYY-MM-DD"}}';}
        			elseif(!$checkic){ echo '{"error":{"text":"Please enter valid IC or passport number."}}';}
        			elseif(strlen($icnum)<6){echo '{"error":{"text":"Please enter valid IC or passport number."}}';}
        			elseif(!$checkpc){echo '{"error":{"text":"Please enter valid postal code."}}';}
        			elseif(!$checkcity){echo '{"error":{"text":"Please enter valid city."}}';}
        			elseif(!$checkstate){echo '{"error":{"text":"Please enter valid state."}}';}  
        			elseif($gender == ''){echo '{"error":{"text":"Please enter gender."}}';}
        			elseif($mothername == ''){echo '{"error":{"text":"Please enter mother name."}}';}
        			elseif(!$checkgender){echo '{"error":{"text":"Please enter gender."}}';}    
        			else{

        				$stmt1 = Kyc::where('uid', $personalid)
        				->update([
        					'uid' => $personalid,
        					'name' => $icname,
        					'mothername' => $mothername,
        					'icnum' => $icnum,
        					'gender' => $gender,
        					'dob' => $icbirth,
        					'require_agree' => 'yes',
        					'address1' => $icaddress1,
        					'address2' => $icaddress2,
        					'city' => $iccity,
        					'state' => $icstate,
        					'zipcode' => $icpostal,
        					'status' => 'uncompleted',
        					'level' => 'Level 2'
        				]);

        				$updt1 = User::where('id', $personalid)
        				->update([
        					'personal_verified' => 1					
        				]);

        				if($level == '1'){
        					$updt1 = Kyc::where('uid', $personalid)
        					->update([
        						'status' => 'pending for review'					
        					]);
        				}else{

        					$updt1 = Kyc::where('uid', $personalid)
        					->update([
        						'status' => 'uncompleted'					
        					]);
        				}

        				$msg = array("text"=>"Your personal verification have been send to be verified.");
        				$datamsg = response()->json([
        					'success' => $msg
        				]);

        				return $datamsg->content();			

        			}	
        		}                    


        		/*###### BANK VERIFICATION ######*/

        		public function bankverify(Request $request){
        			$touseremail =  $request->email;
        			$personalid =  $request->id;
        			$icname = $request->icname;
        			$icaddress1 = $request->icaddress1;
        			$icaddress2 = $request->icaddress2;
        			$icnum = $request->icnum;
        			$icstate = $request->icstate;
        			$iccity = $request->iccity;
        			$icpostal = $request->icpostal;
        			$icbirth = $request->icbirth;
        			$gender = $request->gender;
        			$mothername = $request->mothername;

        			$bankname1 = $request->bankname1;
        			$banknum1 = $request->banknum1;
        			$bankname2 = $request->bankname2;
        			$banknum2 = $request->banknum2;


        			$checkbank1 = preg_match('/^[a-zA-Z\s]+$/', trim($bankname1));
        			$checkbank1acc = preg_match('/^[a-zA-Z0-9\s]+$/', trim($banknum1));

        			if(!$checkbank1){echo '{"error":{"text":"Invalid bank 1 name."}}';}
        			elseif(!$checkbank1acc){echo '{"error":{"text":"Please enter valid bank 1 account number."}}';}
        			else{
        				if(!empty($bankname2) || !empty($banknum2)){
        					$checkbank2 = preg_match('/^[a-zA-Z\s]+$/', trim($bankname2));
        					$checkbank2acc = preg_match('/^[a-zA-Z0-9\s]+$/', trim($banknum2));
        				}
        				else{
        					$checkbank2 = true;
        					$checkbank2acc = true;
        				}

        				if(!$checkbank2){echo '{"error":{"text":"Invalid bank 2 name."}}';}
        				elseif(!$checkbank2acc){echo '{"error":{"text":"Please enter valid bank 2 account number."}}';}
        				else{

        					$mainCount = Kyc::where('uid',$personalid)->count();

        					if($mainCount!=0) { echo '{"error":{"text":"This account already exist."}}';}                    
        					else
        					{
        						$stmt1 = Kyc::create([
        							'uid' => $personalid,
        							'name' => $icname,
        							'mothername' => $mothername,
        							'icnum' => $icnum,
        							'gender' => $gender,
        							'bankname1' => $bankname1,
        							'banknumber1' => $banknum1,
        							'bankname2' => $bankname2,
        							'banknumber2' => $banknum2,
        							'dob' => $icbirth,
        							'require_agree' => 'yes',
        							'address1' => $icaddress1,
        							'address2' => $icaddress2,
        							'city' => $iccity,
        							'state' => $icstate,
        							'zipcode' => $icpostal,
        							'status' => 'uncompleted',
        							'level' => 'Level 2'
        						]);

        						$updt1 = User::where('id', $personalid)
        						->update([
        							'personal_verified' => 1					
        						]);


        						$msg = array("text"=>"Your personal verification have been send to be verified. Please continue to documents verification.");
        						$datamsg = response()->json([
        							'success' => $msg]);

        						return $datamsg->content();			
        					}

        				}                    
        			}
        		}

        		/************************* COINVATA HISTORY ************************************/

        		public function coinvatahistory(Request $request){

        			$uid = $request->id;

        			$mainCount = ConvertPinkexc::where('memberID',$uid)->count();

        			if($mainCount==0) { 
        				$msg = array("text"=>"This account has not made any convertions.");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);

        				return $datamsg->content();	
        			} 
        			else
        			{
        				$userData = ConvertPinkexc::where('memberID',$uid)->orderBy('id','desc')->get();

        				$datamsg = response()->json([
        					'userData' => $userData
        				]);

        				return $datamsg->content();
        			}

        		}

        		/************************* ALL BALANCE ************************************/
        		public function allbalance(Request $request)
        		{

        			$id = $request->id;
        			$token = $request->token;
        			$country = $request->country;
    //echo $id;
        			$systemToken = apiToken($id);
    //echo $systemToken;
        			$check_verification = Kyc::where('uid', $id)->first();

//CALL FROM DATABASE
        			$btc_price = PriceAPI::where('crypto', 'BTC')->first();
        			$bch_price = PriceAPI::where('crypto', 'BCH')->first();
        			$ltc_price = PriceAPI::where('crypto', 'LTC')->first();
        			$dash_price = PriceAPI::where('crypto', 'DASH')->first();
        			$doge_price = PriceAPI::where('crypto', 'DOGE')->first();
        			$eth_price = PriceAPI::where('crypto', 'ETH')->first();
        			$xrp_price = PriceAPI::where('crypto', 'XRP')->first();
        			$xlm_price = PriceAPI::where('crypto', 'XLM')->first();
        			$life_price = PriceAPI::where('crypto', 'LIFE')->first();

        			$btc_myrbalance = '';
        			$ltc_myrbalance = '';
        			$doge_myrbalance = '';
        			$dash_myrbalance = '';
        			$xlm_myrbalance = '';
        			$xrp_myrbalance = '';
        			$bch_myrbalance = '';
        			$eth_myrbalance = '';
        			$life_myrbalance = '';
        			$btc_balance = 0;
        			$bch_balance = 0;
        			$ltc_balance = 0;
        			$dash_balance = 0;
        			$doge_balance = 0;
        			$xlm_balance = 0;
        			$xrp_balance = 0;
        			$eth_balance = 0;
        			$life_balance = 0;
        			$status_btc = '0';
        			$status_ltc = '0';
        			$status_doge = '0';
        			$status_dash = '0';
        			$status_xlm = '0';
        			$status_xrp = '0';
        			$status_bch = '0';
        			$status_eth = '0';
        			$status_life = '0';
        			$btc_address = '';
        			$ltc_address = '';
        			$doge_address = '';
        			$dash_address = '';
        			$xlm_address = '';
        			$xrp_address = '';
        			$bch_address = '';
        			$eth_address = '';
        			$life_address = '';



        			$btc = WalletAddress::where('uid', $id)->where('crypto', 'BTC')->first();

        			if($btc){
        				btc_getbalance($id);
        				$btc_balance = $btc->available_balance;
					$btc_balance = str_replace("\n", '', $btc_balance);
					$btc_balance = bcdiv($btc_balance, 1, 5);
        				$btc_myrbalance =$btc_price->price * $btc_balance;           
        				$btc_address = $btc->address;
        				$status_btc = '1';
        			}

        			$bch = WalletAddress::where('uid', $id)->where('crypto', 'BCH')->first();
        			if($bch){
        				bch_getbalance($id);
        				$bch_balance = $bch->available_balance;
					$bch_balance = bcdiv($bch_balance, 1, 5);
        				$bch_myrbalance = $bch_price->price * $bch_balance;
        				$bch_address = $bch->address;
        				$status_bch = '1';
        			}

        			$ltc = WalletAddress::where('uid', $id)->where('crypto', 'LTC')->first();
        			if($ltc){
        				ltc_getbalance($id);
        				$ltc_balance = $ltc->available_balance;
					$ltc_balance = bcdiv($ltc_balance, 1, 5);
        				$ltc_myrbalance = $ltc_price->price * $ltc_balance;
        				$ltc_address = $ltc->address;
        				$status_ltc = '1';
        			}

        			$dash = WalletAddress::where('uid', $id)->where('crypto', 'DASH')->first();
        			if($dash){   
        				dash_getbalance($id);    
        				$dash_balance = $dash->available_balance;	
					$dash_balance = bcdiv($dash_balance, 1, 5);		
        				$dash_myrbalance = $dash_price->price * $dash_balance;
        				$dash_address = $dash->address;
        				$status_dash = '1';
        			}

        			$doge = WalletAddress::where('uid', $id)->where('crypto', 'DOGE')->first();
        			if($doge){
        				doge_getbalance($id);
        				$doge_balance = $doge->available_balance;
					$doge_balance = bcdiv($doge_balance, 1, 5);
        				$doge_myrbalance = $doge_price->price * $doge_balance;
        				$doge_address = $doge->address;
        				$status_doge = '1';
        			}


        			$xlm = WalletAddress::where('uid', $id)->where('crypto', 'XLM')->first();
        			if($xlm){
        				$xlm_balance = xlm_getbalance_pod($id);
        				$xlm_myrbalance = $xlm_price->price * $xlm_balance;
        				$status_xlm = '1';
        			}

        			$xrp = WalletAddress::where('uid', $id)->where('crypto', 'XRP')->first();
        			if($xrp){
        				xrp_getbalance($id);
        				$xrp_balance = $xrp->available_balance;
        				$xrp_myrbalance = $xrp_price->price * $xrp_balance;
        				$xrp_address = $xrp->address;
        				$status_xrp = '1';
        			}

        			$eth = WalletAddress::where('uid', $id)->where('crypto', 'ETH')->first();
        			if($eth){
        				eth_getbalance($id);
        				$eth_balance = $eth->available_balance;
			//$eth_balance = 'Under maintenance';
        				$eth_myrbalance = $eth_price->price* $eth_balance;
			//$eth_myrbalance = 0;
        				$eth_address = $eth->address;
        				$status_eth = '1';

        			}

        			$life = WalletAddress::where('uid', $id)->where('crypto', 'LIFE')->first();
        			if($life){
        				//life_getbalance($id);
        				$life_balance = $life->available_balance;
			//$life_balance = 'Under maintenance';
        				$life_myrbalance = $life_price->price * $life_balance;
			//$life_myrbalance = 0;
        				$life_address = $life->address;
        				$status_life = '1';
        			}

        			if($btc_myrbalance == ''){$btc_myrbalance = 0;}
        			if($ltc_myrbalance == ''){$ltc_myrbalance = 0;}
        			if($doge_myrbalance == ''){$doge_myrbalance = 0;}
        			if($dash_myrbalance == ''){$dash_myrbalance = 0;}
        			if($xlm_myrbalance == ''){$xlm_myrbalance = 0;}
        			if($bch_myrbalance == ''){$bch_myrbalance = 0;}
        			if($eth_myrbalance == ''){$eth_myrbalance = 0;}
        			if($xrp_myrbalance == ''){$xrp_myrbalance = 0;}
        			if($life_myrbalance == ''){$life_myrbalance = 0;}

        			$total_status = $status_btc + $status_ltc + $status_doge + $status_dash + $status_xlm + $status_bch + $status_life + $status_eth;

        			if($total_status == 8){$total_status = '1';}
        			else{$total_status = '0';}

        			$totalAssets = $btc_myrbalance + $ltc_myrbalance + $doge_myrbalance + $dash_myrbalance + $xlm_myrbalance + 
        			$bch_myrbalance+ $life_myrbalance+ $eth_myrbalance;

        			$msg = array("btc_amount"=>number_format($btc_balance,5),
        				"btc_myr_amount"=>number_format($btc_myrbalance,2),
        				"ltc_amount"=>number_format($ltc_balance,5),
        				"ltc_myr_amount"=>number_format($ltc_myrbalance,2),
        				"doge_amount"=>number_format($doge_balance,2),
        				"doge_myr_amount"=>number_format($doge_myrbalance,2),
        				"dash_amount"=>number_format($dash_balance,5),
			//"dash_amount"=>$dash_balance,
        				"dash_myr_amount"=>number_format($dash_myrbalance,2),
        				"xlm_amount"=>$xlm_balance,
        				"xlm_myr_amount"=>number_format($xlm_myrbalance,2),
        				"bch_amount"=>number_format($bch_balance,5),
        				"bch_myr_amount"=>number_format($bch_myrbalance,2),
        				"eth_amount"=>number_format($eth_balance,5),
			//"eth_myr_amount"=>number_format($eth_myrbalance,2),
			//"eth_amount"=>$eth_balance,
        				"eth_myr_amount"=>number_format($eth_myrbalance,2),
        				"life_amount"=>number_format($life_balance,5),
			//"life_myr_amount"=>number_format($life_myrbalance,2),
			//"life_amount"=>$life_balance,
        				"life_myr_amount"=>number_format($life_myrbalance,2),
        				"total_Assets" =>number_format($totalAssets,2),
        				"xrp_amount"=>number_format($xrp_balance,5),
        				"xrp_myr_amount"=>number_format($xrp_myrbalance,2),
        				"currency"=>"MYR",
        				"status_btc"=>$status_btc,
        				"status_ltc"=>$status_ltc,
        				"status_bch"=>$status_bch,
        				"status_doge"=>$status_doge,
        				"status_dash"=>$status_dash,
        				"status_xlm"=>$status_xlm,
        				"status_eth"=>$status_eth,
        				"status_xrp"=>$status_xrp,
        				"status_life"=>$status_life,
        				"wallet_btc"=>$btc_address,
        				"wallet_ltc"=>$ltc_address,
        				"wallet_bch"=>$bch_address,
        				"wallet_doge"=>$doge_address,
        				"wallet_dash"=>$dash_address,
        				"wallet_eth"=>$eth_address,
        				"wallet_xrp"=>$xrp_address,
        				"wallet_life"=>$life_address);
        			$datamsg = response()->json([
        				'balance' => $msg
        			]);

        			return $datamsg->content();
        		}

//***************CURRENT RATE**********************//
        		public function currentrate(Request $request)
        		{

        			$id = $request->id;
        			$token = $request->token;

        			$systemToken = apiToken($id);
        			if($token == $systemToken)
        			{
        				$count_coinvata = CoinvataUsage::where('uid',$id)->count();

        				if($count_coinvata != 0)
        				{
        					$coinvata_level = CoinvataUsage::where('uid',$id)->first()->level_id;

        					$rate = CoinvataLevel::where('id',$coinvata_level)->first()->fee;

        				}
        				else
        				{
        					$rate = CoinvataLevel::where('id',1)->first()->fee;
        				}

        				$datamsg = response()->json([
        					'rate' => $rate
        				]);
        				return $datamsg->content();


        			}
        			else
        			{
        				$msg = array("text"=>"No access");
        				$datamsg = response()->json([
        					'error' => $msg
        				]);
        				return $datamsg->content();

        			}


        		}

//***************CONVERSION**********************//

        		public function conversion(Request $request)
        		{

        			$id = $request->id;
        			$token = $request->token;
        			$crypto_from = $request->amountFrom;
        			$crypto_to = $request->amountTo;
        			$from = $request->coins;
        			$to = $request->coinsto;

        			$systemToken = apiToken($id);

        			if($token == $systemToken)
        			{
        				$check_verification = Kyc::where('uid', $id)->first();
        				$check_user_level2 = User::where('id', $id)->first();

        				if($check_verification->level == "Level 2")
        				{
        					$level1 = '1';
        					$level2 = '1';
        				}
        				else
        				{
        					$level1 = '1';
        					$level2 = '0';
        				}

        				if($check_verification == null || $check_verification->status != "completed")
        				{
        					$msg = array("text"=>"Sorry, Please upgrade to level 2");
        					$datamsg = response()->json([
        						'error' => $msg
        					]);

        					return $datamsg->content();

        				}
        				elseif($check_verification->status == 'completed' && $level2 == '0')
        				{  

        					$msg = array("text"=>"Sorry, Please upgrade to level 2");
        					$datamsg = response()->json([
        						'error' => $msg
        					]);

        					return $datamsg->content();

        				}
        				else
        				{

        					$convertFrom =$from;
        					$convertTo = $to;

        					$get_user = User::where('id', $id)->first();
        					$label = 'usr_' . $get_user->username;

        					$chec_usr_from = WalletAddress::where('uid', $id)->where('crypto', $convertFrom)->first();

        //if doesn't exist
        					if ($chec_usr_from == null) {

        						$crypto = $convertFrom;

        						$create_nodes = addCrypto($crypto,$label);

           //insert into table wallet
        						WalletAddress::create([
        							'uid' => $id,
        							'label' => $label,
        							'address' => $create_nodes,
        							'available_balance' => 0.0000,
        							'crypto' => $crypto
        						]);

            //insert into table limitation
        						if($get_user->country == 130)
        						{
            //buy
        							Limitation::create([
        								'uid' => $id,
        								'fullname' => $label,
        								'limit_usage' => 0.0000,
        								'limit_balance' => 100000,
        								'category' => 'buy'
        							]);

            //sell
        							Limitation::create([
        								'uid' => $id,
        								'fullname' => $label,
        								'limit_usage' => 0.0000,
        								'limit_balance' => 100000,
        								'category' => 'sell'
        							]);
        						}
        						else
        						{
	//buy
        							Limitation::create([
        								'uid' => $id,
        								'fullname' => $label,
        								'limit_usage' => 0.0000,
        								'limit_balance' => 50000,
        								'category' => 'buy'
        							]);

            //sell
        							Limitation::create([
        								'uid' => $id,
        								'fullname' => $label,
        								'limit_usage' => 0.0000,
        								'limit_balance' => 50000,
        								'category' => 'sell'
        							]);
        						}



        					}

        					$chec_usr_to = WalletAddress::where('uid', $id)->where('crypto', $convertTo)->first();

        //if doesn't exist
        					if ($chec_usr_to == null) {

        						$crypto = $convertTo;

        						$create_nodes = addCrypto($crypto,$label);

           //insert into table wallet
        						WalletAddress::create([
        							'uid' => $id,
        							'label' => $label,
        							'address' => $create_nodes,
        							'available_balance' => 0.0000,
        							'crypto' => $crypto
        						]);

            //insert into table limitation
        						if($get_user->country == 130)
        						{
            //buy
        							Limitation::create([
        								'uid' => $id,
        								'fullname' => $label,
        								'limit_usage' => 0.0000,
        								'limit_balance' => 100000,
        								'category' => 'buy'
        							]);

            //sell
        							Limitation::create([
        								'uid' => $id,
        								'fullname' => $label,
        								'limit_usage' => 0.0000,
        								'limit_balance' => 100000,
        								'category' => 'sell'
        							]);
        						}
        						else
        						{
					//buy
        							Limitation::create([
        								'uid' => $id,
        								'fullname' => $label,
        								'limit_usage' => 0.0000,
        								'limit_balance' => 50000,
        								'category' => 'buy'
        							]);

            //sell
        							Limitation::create([
        								'uid' => $id,
        								'fullname' => $label,
        								'limit_usage' => 0.0000,
        								'limit_balance' => 50000,
        								'category' => 'sell'
        							]);
        						}

        					}

        					$count_coinvata = CoinvataUsage::where('uid',$id)->count();

        					if($count_coinvata != 0)
        					{
        						$coinvata_level = CoinvataUsage::where('uid',$id)->first()->level_id;

        						$rate = CoinvataLevel::where('id',$coinvata_level)->first()->fee;

        					}
        					else
        					{
        						$rate = CoinvataLevel::where('id',1)->first()->fee;
        					}


        					$coinvata_price = coinvata_price($convertFrom,$convertTo,$rate);
        					$data_coinvata = json_decode($coinvata_price->content());

        					$current_price = round($data_coinvata->current_price,8);
        					$displayprice = round($data_coinvata->displayprice,8);
        					$minimum_price = round($data_coinvata->minimum_price,8);
        					$maximum_price = round($data_coinvata->maximum_price,8);

  $crypto_from = round($request->amountFrom,8); //Amount From
  $crypto_to = round($request->amountTo,8); // Amount To

  $amountTo = round(($crypto_from * $current_price),8);

  $max = getbalance($from,'usr_'.$check_user_level2->username); // Balance From

  $admin_balance = getbalance($to,'usr_coinvata'); // Balance Admin

  $currentBalTo = getbalance($to,'usr_'.$check_user_level2->username); // Balance To

  if ($crypto_from > $max)
  {
  	$msg = array("text"=>'Sorry, your '.$from.' is not enough to process the conversion.');
  	$datamsg = response()->json([
  		'error' => $msg
  	]);

  	return $datamsg->content();


  } 
  else if($crypto_from < $minimum_price)
  {
  	$msg = array("text"=>'Sorry, The minimum amount for '.$from.' is '.$minimum_price.' '.$from.' per transaction. Please try again. Thank you.');
  	$datamsg = response()->json([
  		'error' => $msg
  	]);

  	return $datamsg->content();

  }
  elseif($crypto_from > $maximum_price)
  {

  	$msg = array("text"=>'Sorry, your conversion amount is exceed the limitation per transaction. Limit conversion per transaction is '.$maximum_price.' '.$from);
  	$datamsg = response()->json([
  		'error' => $msg
  	]);

  	return $datamsg->content();

  } 
  elseif($amountTo > $admin_balance)
  {

  	$msg = array("text"=>'Sorry, maximum amount you can convert is '.$admin_balance.' '.$to);
  	$datamsg = response()->json([
  		'error' => $msg
  	]);

  	return $datamsg->content();

  }
  else
  {
  // //SENT FUNDS FROM USER TO ADMIN

  	//$sendto_admin = move_crypto($from, 'usr_'.$check_user_level2->username, 'usr_coinvata', $crypto_from);
  	$sendto_admin = move_crypto_comment($from, 'usr_'.$check_user_level2->username, 'usr_coinvata', $crypto_from, 'sell_coinvata');

  // //SENT FUNDS FROM ADMIN TO USER

  	if($from != "ETH")
  	{
		//$sendto_user = move_crypto($to, 'usr_coinvata', 'usr_'.$check_user_level2->username, $amountTo);
  		$sendto_user = move_crypto_comment($to, 'usr_coinvata', 'usr_'.$check_user_level2->username, $amountTo, 'buy_coinvata');

  $balToAfter = getbalance($to,'usr_'.$check_user_level2->username); // Balance To

  $coinvatapinkexc = ConvertPinkexc::create([

  	'memberID' => $id,
  	'username' => 'usr_'.$check_user_level2->username,
  	'email' => $check_user_level2->email,
  	'currentBalFrom' => $max,
  	'currentBalTo' => $currentBalTo,
  	'amountFrom' => $crypto_from,
  	'amountTo' => $amountTo,
  	'balToAfter' => $balToAfter,
  	'currencyFrom' => $from,
  	'currencyTo' => $to,
  	'date' => Carbon::now(),
  	'status' => 'Completed',
  	'rate' => round($current_price,8),
  	'current_rate' => round($displayprice,8)
  ]);

  $coinvataData = ConvertPinkexc::where('memberID',$id)->orderBy('id', 'desc')->first();

  $countCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->take(1)->count();

  if($countCoinvata != 0)
  {
  	$dataCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->first();

  	$total_usage = $dataCoinvata->usage + round($crypto_from,8);

  	if($total_usage < 50000)
  	{
      //Classic
  		$coinvata_level = CoinvataLevel::where('id',1)->first();
  		$level_id = $coinvata_level->id;
  	}
  	else if($total_usage >= 50001 && $total_usage < 100000)
  	{
      //Premier
  		$coinvata_level = CoinvataLevel::where('id',2)->first();
  		$level_id = $coinvata_level->id;
  	}
  	else
  	{
      //Infinite
  		$coinvata_level = CoinvataLevel::where('id',3)->first();
  		$level_id = $coinvata_level->id;
  	}

  	$usage_coinvata = CoinvataUsage::where('id',$id)
  	->update([
  		'level_id' => 1,
  		'usage' => round($crypto_from,8)
  	]);
  }
  else
  {
  	$usage_coinvata = CoinvataUsage::create([
  		'uid' => $id,
  		'level_id' => 1,
  		'usage' => round($crypto_from,8),
  	]);
  }

  send_email_coinvata($coinvataData->id,$from,$to,$displayprice,$crypto_from,$amountTo,$rate,$id);

  $msg = array("text"=>'Successfully convert.');
  $datamsg = response()->json([
  	'success' => $msg
  ]);

  return $datamsg->content();
}
else
{

  $balToAfter = getbalance($to,'usr_'.$check_user_level2->username); // Balance To

  $coinvatapinkexc = ConvertPinkexc::create([

  	'memberID' => $id,
  	'username' => 'usr_'.$check_user_level2->username,
  	'email' => $check_user_level2->email,
  	'currentBalFrom' => $max,
  	'currentBalTo' => $currentBalTo,
  	'amountFrom' => $crypto_from,
  	'amountTo' => $amountTo,
  	'balToAfter' => $balToAfter,
  	'currencyFrom' => $from,
  	'currencyTo' => $to,
  	'date' => Carbon::now(),
  	'status' => 'Process',
  	'rate' => round($current_price,8),
  	'current_rate' => round($displayprice,8),
  	'txhash' => $sendto_admin
  ]);

  $coinvataData = ConvertPinkexc::where('memberID',$id)->orderBy('id', 'desc')->first();

  $countCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->take(1)->count();

  if($countCoinvata != 0)
  {
  	$dataCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->first();

  	$total_usage = $dataCoinvata->usage + round($crypto_from,8);

  	if($total_usage < 50000)
  	{
      //Classic
  		$coinvata_level = CoinvataLevel::where('id',1)->first();
  		$level_id = $coinvata_level->id;
  	}
  	else if($total_usage >= 50001 && $total_usage < 100000)
  	{
      //Premier
  		$coinvata_level = CoinvataLevel::where('id',2)->first();
  		$level_id = $coinvata_level->id;
  	}
  	else
  	{
      //Infinite
  		$coinvata_level = CoinvataLevel::where('id',3)->first();
  		$level_id = $coinvata_level->id;
  	}

  	$usage_coinvata = CoinvataUsage::where('id',$id)
  	->update([
  		'level_id' => 1,
  		'usage' => round($crypto_from,8)
  	]);
  }
  else
  {
  	$usage_coinvata = CoinvataUsage::create([
  		'uid' => $id,
  		'level_id' => 1,
  		'usage' => round($crypto_from,8),
  	]);
  }

  send_email_process_coinvata($coinvataData->id,$from,$to,$id);

  $msg = array("text"=>'Your conversion is under process.');
  $datamsg = response()->json([
  	'success' => $msg
  ]);

  return $datamsg->content();

}

}


}


}
else
{
	$msg = array("text"=>"No access");
	$datamsg = response()->json([
		'error' => $msg
	]);
} 

}


//***************CONVERSION V3**********************//

public function conversionv3(Request $request)
{

	$id = $request->id;
	$token = $request->token;
	$crypto_from = $request->amountFrom;
	$crypto_to = $request->amountTo;
	$from = $request->coins;
	$to = $request->coinsto;
	$address = $request->address;
	$secret = $request->secret;

	$systemToken = apiToken($id);

	if($token == $systemToken)
	{
		
		$check_verification = Kyc::where('uid', $id)->first();
		$check_user_level2 = User::where('id', $id)->first();

		if($check_verification->level == "Level 2")
		{
			$level1 = '1';
			$level2 = '1';
		}
		else
		{
			$level1 = '1';
			$level2 = '0';
		}

		if($check_verification == null || $check_verification->status != "completed")
		{
			$msg = array("text"=>"Sorry, Please upgrade to level 2");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}
		elseif($check_verification->status == 'completed' && $level2 == null)
		{  

			$msg = array("text"=>"Sorry, Please upgrade to level 2");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}
		else
		{

			$convertFrom =$from;
			$convertTo = $to;

			$get_user = User::where('id', $id)->first();
			$label = 'usr_' . $get_user->username;

			$chec_usr_from = WalletAddress::where('uid', $id)->where('crypto', $convertFrom)->first();

        //if doesn't exist
			if ($chec_usr_from == null) {

				if($convertFrom != "XRP")
				{

					$crypto = $convertFrom;

					$create_nodes = addCrypto($crypto,$label);

           //insert into table wallet
					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => $create_nodes,
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);
				}
				else if($convertFrom != "XLM")
				{
					$crypto = $convertFrom;

					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => '1',
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);
				}
				else
				{
					$crypto = $convertFrom;

					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => $address,
						'secret' => $secret,
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);


				}

            //insert into table limitation
				if($get_user->country == 130)
				{
            //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'sell'
					]);
				}
				else
				{
  //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'sell'
					]);
				}



			}

			$chec_usr_to = WalletAddress::where('uid', $id)->where('crypto', $convertTo)->first();

        //if doesn't exist
			if ($chec_usr_to == null) {

				if($convertTo != "XRP")
				{

					$crypto = $convertTo;

					$create_nodes = addCrypto($crypto,$label);

           //insert into table wallet
					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => $create_nodes,
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);

				}
				else if($convertTo != "XLM")
				{
					$crypto = $convertTo;

					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => '1',
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);
				}
				else
				{

					$crypto = $convertTo;

					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => $address,
						'secret' => $secret,
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);


				}


            //insert into table limitation
				if($get_user->country == 130)
				{
            //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'sell'
					]);
				}
				else
				{
          //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'sell'
					]);
				}

			}

			$count_coinvata = CoinvataUsage::where('uid',$id)->count();

			if($count_coinvata != 0)
			{
				$coinvata_level = CoinvataUsage::where('uid',$id)->first()->level_id;

				$rate = CoinvataLevel::where('id',$coinvata_level)->first()->fee;

			}
			else
			{
				$rate = CoinvataLevel::where('id',1)->first()->fee;
			}


			$coinvata_price = coinvata_price($convertFrom,$convertTo,$rate);
			$data_coinvata = json_decode($coinvata_price->content());

			$current_price = round($data_coinvata->current_price,8);
			$displayprice = round($data_coinvata->displayprice,8);
			$minimum_price = round($data_coinvata->minimum_price,8);
			$maximum_price = round($data_coinvata->maximum_price,8);

  $crypto_from = round($request->amountFrom,8); //Amount From
  $crypto_to = round($request->amountTo,8); // Amount To

  $amountTo = round(($crypto_from * $current_price),8);

  if($from != "XLM")
  {
  	if($from == "ETH")
  	{
			$gas = gaspriceData();
			$decode = json_decode($gas);
			$converter = new \Bezhanov\Ethereum\Converter();
			$normal = $converter->toWei($decode->normal, 'gwei');
			$fast = $converter->toWei($decode->fast, 'gwei');

			        //Get Data ETH
			$current_price = PriceAPI::where('name', 'Ethereum')->first()->price;
			$withdraw_commision = Setting::first()->withdrawal_commission;
			$fee = $withdraw_commision / $current_price;      
			$gasL = '100000';
			$gasP = $fast;
			$address_fee = WalletAddress::where('uid', 888)->where('crypto','ETH')->first()->address;
			$check_users = User::where('id', $id)->first();

		            //Check Address ETH (From)
		            //Address User
			$address_from = WalletAddress::where('uid', $id)->where('crypto', 'ETH')->first()->address;
			$converter = new \Bezhanov\Ethereum\Converter();
		            //$balance_from = Ethereum::eth_getBalance($address_from, 'latest', TRUE);
			$balance_from =0;
			$balance_fromWei = $converter->fromWei($balance_from, 'ether');

			if ($balance_fromWei != 0) {
				$estFee = $converter->fromWei($gasP * $gasL, 'ether');
				$bal = $balance_fromWei - (($estFee * 2) + $fee);

				if (strpos($bal, '-') !== false) {
					$bal = 0;
				}
			} 
			else {
				$estFee = $converter->fromWei($gasP * $gasL, 'ether');
				$bal = 0;
			}

			$total_fee_crypto = ($estFee * 2) + $fee;
			$max_send_crypto = round(($crypto_balance - $total_fee_crypto),5);
		

		if ($crypto_balance <= $total_fee_crypto) {
			$max = 0;
		} 
		else {
			$max = $max_send_crypto;
		}
  	}
  	else
  	{
  		$max = getbalance($from,'usr_'.$check_user_level2->username); // Balance From
  	}
}
else
{
	$max = xlm_getbalance_pod($id);
}

if($to != "XLM")
{
  $admin_balance = getbalance($to,'usr_coinvata'); // Balance Admin

  $currentBalTo = getbalance($to,'usr_'.$check_user_level2->username); // Balance To
}
else
{
    $admin_balance = xlm_getbalance(3); // Balance Admin

  $currentBalTo = xlm_getbalance_pod($id); // Balance To
}
if($convertFrom == 'XRP')
{
	if($crypto_balance > 20)
	{
		$max = round(($crypto_balance - 20), 5);
	}
	else
	{
		$max = 0;
	}
}

if ($crypto_from > $max)
{
	$msg = array("text"=>'Sorry, your '.$from.' is not enough to process the conversion.');
	$datamsg = response()->json([
		'error' => $msg
	]);

	return $datamsg->content();


}
else if($crypto_from < $minimum_price)
{
  $msg = array("text"=>'Sorry, The minimum amount for '.$from.' is '.$minimum_price.' '.$from.' per transaction. Please try again. Thank you.');
  $datamsg = response()->json([
    'error' => $msg
  ]);

  return $datamsg->content();

}
elseif($crypto_from > $maximum_price)
{

	$msg = array("text"=>'Sorry, your conversion amount is exceed the limitation per transaction. Limit conversion per transaction is '.$maximum_price.' '.$from);
	$datamsg = response()->json([
		'error' => $msg
	]);

	return $datamsg->content();

} 
elseif($amountTo > $admin_balance)
{

	$msg = array("text"=>'Sorry, maximum amount you can convert is '.$admin_balance.' '.$to);
	$datamsg = response()->json([
		'error' => $msg
	]);

	return $datamsg->content();

}
else
{
  // //SENT FUNDS FROM USER TO ADMIN
	if($from != "XLM")
	{
		if($to != "XLM")
		{
    //$sendto_admin = move_crypto($from, 'usr_'.$check_user_level2->username, 'usr_coinvata', $crypto_from);
			$sendto_admin = move_crypto_comment($from, 'usr_'.$check_user_level2->username, 'usr_coinvata', $crypto_from, 'sell_coinvata');
		}
	}
	else
	{
		$type = "sell coinvata";
		$account_id = StellarInfo::where('id',3)->first()->account_id;
		$memo = '4;'.time();
		$txtmemo = $to;
		$num = $crypto_from;
		$sender_id = StellarInfo::where('id',2)->first()->account_id;
		$seed_id = StellarInfo::where('id',2)->first()->seed_id;
		$platform = 'mobile';

		$sendto_admin = coinvata_stellar($type,$memo,$txtmemo,$num,$id,$current_price,$displayprice,$platform);

		$data_json = json_decode($sendto_admin);

		if($data_json[0]->msj == "None error stellar POD")
		{
			$server = Server::publicNet();

			$sourceKeypair = Keypair::newFromSeed($seed_id);

			$destinationAccountId = $account_id;

			$destinationAccount = $server->getAccount($destinationAccountId);

    // Build the payment transaction
			$transaction = \ZuluCrypto\StellarSdk\Server::publicNet()
			->buildTransaction($sourceKeypair->getPublicKey())
			->addOperation(
				PaymentOp::newNativePayment($destinationAccountId, $num)
			)
			->setTextMemo($memo)
			;  
    // Sign and submit the transaction
			$response = $transaction->submit($sourceKeypair->getSecret());
		}
		else
		{
			$msg = array("text"=>'Coinvata failed, please try again.');
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();
		}
	}
  // //SENT FUNDS FROM ADMIN TO USER

	if($from != "ETH")
	{
		if($to != "XLM")
		{
      //$sendto_user = move_crypto($to, 'usr_coinvata', 'usr_'.$check_user_level2->username, $amountTo);
			$sendto_user = move_crypto_comment($to, 'usr_coinvata', 'usr_'.$check_user_level2->username, $amountTo, 'buy_coinvata');

        $balToAfter = getbalance($to,'usr_'.$check_user_level2->username); // Balance To
    }
    else
    {
    	$type = "buy coinvata";
    	$account_id = StellarInfo::where('id',2)->first()->account_id;
    	$memo = '5;'.time();
    	$txtmemo = $from;
    	$num = $crypto_to;
    	$sender_id = StellarInfo::where('id',3)->first()->account_id;
    	$seed_id = StellarInfo::where('id',3)->first()->seed_id;
    	$platform = 'mobile';

    	$sendto_user = coinvata_stellar($type,$memo,$txtmemo,$num,$id,$current_price,$displayprice,$platform);


    	$data_json = json_decode($sendto_user);

    	if($data_json[0]->msj == "None error stellar POD")
    	{
    		$server = Server::publicNet();

    		$sourceKeypair = Keypair::newFromSeed($seed_id);

    		$destinationAccountId = $account_id;

    		$destinationAccount = $server->getAccount($destinationAccountId);

    // Build the payment transaction
    		$transaction = \ZuluCrypto\StellarSdk\Server::publicNet()
    		->buildTransaction($sourceKeypair->getPublicKey())
    		->addOperation(
    			PaymentOp::newNativePayment($destinationAccountId, $num)
    		)
    		->setTextMemo($memo)
    		;  
    // Sign and submit the transaction
    		$response = $transaction->submit($sourceKeypair->getSecret());

    		$sendto_admin = move_crypto_comment($from, 'usr_'.$check_user_level2->username, 'usr_coinvata', $crypto_from, 'sell_coinvata');
    	}
    	else
    	{

    		$msg = array("text"=>'Coinvata failed, please try again.');
    		$datamsg = response()->json([
    			'error' => $msg
    		]);

    		return $datamsg->content();
    	}

    	$balToAfter = xlm_getbalance_pod($id);
    }

    if($from != "DOGE")
    {
    	$coinvatapinkexc = ConvertPinkexc::create([

    		'memberID' => $id,
    		'username' => 'usr_'.$check_user_level2->username,
    		'email' => $check_user_level2->email,
    		'currentBalFrom' => $max,
    		'currentBalTo' => $currentBalTo,
    		'amountFrom' => $crypto_from,
    		'amountTo' => $amountTo,
    		'balToAfter' => $balToAfter,
    		'currencyFrom' => $from,
    		'currencyTo' => $to,
    		'date' => Carbon::now(),
    		'status' => 'Completed',
    		'rate' => round($current_price,8),
    		'current_rate' => round($displayprice,5)
    	]);
    }
    else
    {
    	$coinvatapinkexc = ConvertPinkexc::create([

    		'memberID' => $id,
    		'username' => 'usr_'.$check_user_level2->username,
    		'email' => $check_user_level2->email,
    		'currentBalFrom' => $max,
    		'currentBalTo' => $currentBalTo,
    		'amountFrom' => $crypto_from,
    		'amountTo' => $amountTo,
    		'balToAfter' => $balToAfter,
    		'currencyFrom' => $from,
    		'currencyTo' => $to,
    		'date' => Carbon::now(),
    		'status' => 'Completed',
    		'rate' => round($current_price,8),
    		'current_rate' => round($displayprice,2)
    	]);

    }

    $coinvataData = ConvertPinkexc::where('memberID',$id)->orderBy('id', 'desc')->first();

    $countCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->take(1)->count();

    if($countCoinvata != 0)
    {
    	$dataCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->first();

    	$total_usage = $dataCoinvata->usage + round($crypto_from,8);

    	if($total_usage < 50000)
    	{
      //Classic
    		$coinvata_level = CoinvataLevel::where('id',1)->first();
    		$level_id = $coinvata_level->id;
    	}
    	else if($total_usage >= 50001 && $total_usage < 100000)
    	{
      //Premier
    		$coinvata_level = CoinvataLevel::where('id',2)->first();
    		$level_id = $coinvata_level->id;
    	}
    	else
    	{
      //Infinite
    		$coinvata_level = CoinvataLevel::where('id',3)->first();
    		$level_id = $coinvata_level->id;
    	}

    	$usage_coinvata = CoinvataUsage::where('id',$id)
    	->update([
    		'level_id' => 1,
    		'usage' => round($crypto_from,8)
    	]);
    }
    else
    {
    	$usage_coinvata = CoinvataUsage::create([
    		'uid' => $id,
    		'level_id' => 1,
    		'usage' => round($crypto_from,8),
    	]);
    }

    send_email_coinvata($coinvataData->id,$from,$to,$displayprice,$crypto_from,$amountTo,$rate,$id);

    $msg = array("text"=>'Successfully convert.');
    $datamsg = response()->json([
    	'success' => $msg
    ]);

    return $datamsg->content();
}
else
{
	if($to != "XLM")
	{
    $balToAfter = getbalance($to,'usr_'.$check_user_level2->username); // Balance To
}
else
{
	$balToAfter = xlm_getbalance_pod($id);
}


$coinvatapinkexc = ConvertPinkexc::create([

	'memberID' => $id,
	'username' => 'usr_'.$check_user_level2->username,
	'email' => $check_user_level2->email,
	'currentBalFrom' => $max,
	'currentBalTo' => $currentBalTo,
	'amountFrom' => $crypto_from,
	'amountTo' => $amountTo,
	'balToAfter' => $balToAfter,
	'currencyFrom' => $from,
	'currencyTo' => $to,
	'date' => Carbon::now(),
	'status' => 'Process',
	'rate' => round($current_price,8),
	'current_rate' => round($displayprice,5),
	'txhash' => $sendto_admin
]);

$coinvataData = ConvertPinkexc::where('memberID',$id)->orderBy('id', 'desc')->first();

$countCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->take(1)->count();

if($countCoinvata != 0)
{
	$dataCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->first();

	$total_usage = $dataCoinvata->usage + round($crypto_from,8);

	if($total_usage < 50000)
	{
      //Classic
		$coinvata_level = CoinvataLevel::where('id',1)->first();
		$level_id = $coinvata_level->id;
	}
	else if($total_usage >= 50001 && $total_usage < 100000)
	{
      //Premier
		$coinvata_level = CoinvataLevel::where('id',2)->first();
		$level_id = $coinvata_level->id;
	}
	else
	{
      //Infinite
		$coinvata_level = CoinvataLevel::where('id',3)->first();
		$level_id = $coinvata_level->id;
	}

	$usage_coinvata = CoinvataUsage::where('id',$id)
	->update([
		'level_id' => 1,
		'usage' => round($crypto_from,8)
	]);
}
else
{
	$usage_coinvata = CoinvataUsage::create([
		'uid' => $id,
		'level_id' => 1,
		'usage' => round($crypto_from,8),
	]);
}

send_email_process_coinvata($coinvataData->id,$from,$to,$id);

$msg = array("text"=>'Your conversion is under process.');
$datamsg = response()->json([
	'success' => $msg
]);

return $datamsg->content();

}

}

}


}
else
{
	$msg = array("text"=>"No access");
	$datamsg = response()->json([
		'error' => $msg
	]);
} 

}

######################### CONVERSION V2 ############################
public function conversionv2(Request $request)
{
	$msg = array("text"=>"Service is currently unavailable.");
	$datamsg = response()->json([
		'error' => $msg
	]);

}

public function conversionv21(Request $request)
{

	$id = $request->id;
	$token = $request->token;
	$crypto_from = $request->amountFrom;
	$crypto_to = $request->amountTo;
	$from = $request->coins;
	$to = $request->coinsto;
	$address = $request->address;
	$secret = $request->secret;

	$systemToken = apiToken($id);

	if($token == $systemToken)
	{
		
		$check_verification = Kyc::where('uid', $id)->first();
		$check_user_level2 = User::where('id', $id)->first();

		if($check_verification->level == "Level 2")
		{
			$level1 = '1';
			$level2 = '1';
		}
		else
		{
			$level1 = '1';
			$level2 = '0';
		}

		/*
		if($from == 'BTC' || $to == 'BTC')
		{
			$msg = array("text"=>"Sorry, BTC Coinvata under maintenance.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}*/
		


		if($check_verification == null || $check_verification->status != "completed")
		{
			$msg = array("text"=>"Sorry, Please upgrade to level 2");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}
		elseif($check_verification->status == 'completed' && $level2 == null)
		{  

			$msg = array("text"=>"Sorry, Please upgrade to level 2");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}
		else
		{

			$convertFrom =$from;
			$convertTo = $to;

			$get_user = User::where('id', $id)->first();
			$label = 'usr_' . $get_user->username;

			$chec_usr_from = WalletAddress::where('uid', $id)->where('crypto', $convertFrom)->first();

        //if doesn't exist
			if ($chec_usr_from == null) {

				if($convertFrom != "XRP")
				{

					$crypto = $convertFrom;

					$create_nodes = addCrypto($crypto,$label);

           //insert into table wallet
					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => $create_nodes,
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);
				}
				else if($convertFrom != "XLM")
				{
					$crypto = $convertFrom;

					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => '1',
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);
				}
				else
				{
					$crypto = $convertFrom;

					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => $address,
						'secret' => $secret,
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);


				}

            //insert into table limitation
				if($get_user->country == 130)
				{
            //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'sell'
					]);
				}
				else
				{
  //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'sell'
					]);
				}



			}

			$chec_usr_to = WalletAddress::where('uid', $id)->where('crypto', $convertTo)->first();

        //if doesn't exist
			if ($chec_usr_to == null) {

				if($convertTo != "XRP")
				{

					$crypto = $convertTo;

					$create_nodes = addCrypto($crypto,$label);

           //insert into table wallet
					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => $create_nodes,
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);

				}
				else if($convertTo != "XLM")
				{
					$crypto = $convertTo;

					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => '1',
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);
				}
				else
				{

					$crypto = $convertTo;

					WalletAddress::create([
						'uid' => $id,
						'label' => $label,
						'address' => $address,
						'secret' => $secret,
						'available_balance' => 0.0000,
						'crypto' => $crypto
					]);


				}


            //insert into table limitation
				if($get_user->country == 130)
				{
            //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'sell'
					]);
				}
				else
				{
          //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'sell'
					]);
				}

			}

			$count_coinvata = CoinvataUsage::where('uid',$id)->count();

			if($count_coinvata != 0)
			{
				$coinvata_level = CoinvataUsage::where('uid',$id)->first()->level_id;

				$rate = CoinvataLevel::where('id',$coinvata_level)->first()->fee;

			}
			else
			{
				$rate = CoinvataLevel::where('id',1)->first()->fee;
			}


			$coinvata_price = coinvata_price($convertFrom,$convertTo,$rate);
			$data_coinvata = json_decode($coinvata_price->content());

			$current_price = round($data_coinvata->current_price,8);
			$displayprice = round($data_coinvata->displayprice,8);
			$minimum_price = round($data_coinvata->minimum_price,8);
			$maximum_price = round($data_coinvata->maximum_price,8);

  $crypto_from = round($request->amountFrom,8); //Amount From
  $crypto_to = round($request->amountTo,8); // Amount To

  $amountTo = round(($crypto_from * $current_price),8);

  if($from != "XLM")
  {
  	if($from == "ETH")
  	{
			$gas = gaspriceData();
			$decode = json_decode($gas);
			$converter = new \Bezhanov\Ethereum\Converter();
			$normal = $converter->toWei($decode->normal, 'gwei');
			$fast = $converter->toWei($decode->fast, 'gwei');

			        //Get Data ETH
			$current_price = PriceAPI::where('name', 'Ethereum')->first()->price;
			$withdraw_commision = Setting::first()->withdrawal_commission;
			$fee = $withdraw_commision / $current_price;      
			$gasL = '100000';
			$gasP = $fast;
			$address_fee = WalletAddress::where('uid', 888)->where('crypto','ETH')->first()->address;
			$check_users = User::where('id', $id)->first();

		            //Check Address ETH (From)
		            //Address User
			$address_from = WalletAddress::where('uid', $id)->where('crypto', 'ETH')->first()->address;
			$converter = new \Bezhanov\Ethereum\Converter();
		            $balance_from = Ethereum::eth_getBalance($address_from, 'latest', TRUE);
			//$balance_from =0;
			$balance_fromWei = $converter->fromWei($balance_from, 'ether');

			if ($balance_fromWei != 0) {
				$estFee = $converter->fromWei($gasP * $gasL, 'ether');
				$bal = $balance_fromWei - (($estFee * 2) + $fee);

				if (strpos($bal, '-') !== false) {
					$bal = 0;
				}
			} 
			else {
				$estFee = $converter->fromWei($gasP * $gasL, 'ether');
				$bal = 0;
			}

			$total_fee_crypto = ($estFee * 2) + $fee;
			$max_send_crypto = round(($bal - $total_fee_crypto),5);
		

		if ($max_send_crypto <= $total_fee_crypto) {

			$max = 0;


		} 
		else {
			$max = $max_send_crypto;
		}
  	}
  	else
  	{
  		$max = getbalance($from,'usr_'.$check_user_level2->username); // Balance From
  	}
}
else
{
	$max = xlm_getbalance_pod($id);
}

if($to != "XLM")
{
  $admin_balance = getbalance($to,'usr_coinvata'); // Balance Admin

  $currentBalTo = getbalance($to,'usr_'.$check_user_level2->username); // Balance To
}
else
{
    $admin_balance = xlm_getbalance(3); // Balance Admin

  $currentBalTo = xlm_getbalance_pod($id); // Balance To
}
if($convertFrom == 'XRP')
{
	if($crypto_balance > 20)
	{
		$max = round(($crypto_balance - 20), 5);
	}
	else
	{
		$max = 0;
	}
}
if ($crypto_from > $max)
{
	$msg = array("text"=>'Sorry, your '.$from.' is not enough to process the conversion.');
	$datamsg = response()->json([
		'error' => $msg
	]);

	return $datamsg->content();


}
else if($crypto_from < $minimum_price)
{
  $msg = array("text"=>'Sorry, The minimum amount for '.$from.' is '.$minimum_price.' '.$from.' per transaction. Please try again. Thank you.');
  $datamsg = response()->json([
    'error' => $msg
  ]);

  return $datamsg->content();

}
elseif($crypto_from > $maximum_price)
{

	$msg = array("text"=>'Sorry, your conversion amount is exceed the limitation per transaction. Limit conversion per transaction is '.$maximum_price.' '.$from);
	$datamsg = response()->json([
		'error' => $msg
	]);

	return $datamsg->content();

} 
elseif($amountTo > $admin_balance)
{

	$msg = array("text"=>'Sorry, maximum amount you can convert is '.$admin_balance.' '.$to);
	$datamsg = response()->json([
		'error' => $msg
	]);

	return $datamsg->content();

}
else
{
  // //SENT FUNDS FROM USER TO ADMIN
	if($from != "XLM")
	{
		if($to != "XLM")
		{
    //$sendto_admin = move_crypto($from, 'usr_'.$check_user_level2->username, 'usr_coinvata', $crypto_from);
			$sendto_admin = move_crypto_comment($from, 'usr_'.$check_user_level2->username, 'usr_coinvata', $crypto_from, 'sell_coinvata');
		}
	}
	else
	{
		$type = "sell coinvata";
		$account_id = StellarInfo::where('id',3)->first()->account_id;
		$memo = '4;'.time();
		$txtmemo = $to;
		$num = $crypto_from;
		$sender_id = StellarInfo::where('id',2)->first()->account_id;
		$seed_id = StellarInfo::where('id',2)->first()->seed_id;
		$platform = 'mobile';

		$sendto_admin = coinvata_stellar($type,$memo,$txtmemo,$num,$id,$current_price,$displayprice,$platform);

		$data_json = json_decode($sendto_admin);

		if($data_json[0]->msj == "None error stellar POD")
		{
			$server = Server::publicNet();

			$sourceKeypair = Keypair::newFromSeed($seed_id);

			$destinationAccountId = $account_id;

			$destinationAccount = $server->getAccount($destinationAccountId);

    // Build the payment transaction
			$transaction = \ZuluCrypto\StellarSdk\Server::publicNet()
			->buildTransaction($sourceKeypair->getPublicKey())
			->addOperation(
				PaymentOp::newNativePayment($destinationAccountId, $num)
			)
			->setTextMemo($memo)
			;  
    // Sign and submit the transaction
			$response = $transaction->submit($sourceKeypair->getSecret());
		}
		else
		{
			$msg = array("text"=>'Coinvata failed, please try again.');
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();
		}
	}
  // //SENT FUNDS FROM ADMIN TO USER

	if($from != "ETH")
	{
		if($to != "XLM")
		{
      //$sendto_user = move_crypto($to, 'usr_coinvata', 'usr_'.$check_user_level2->username, $amountTo);
			$sendto_user = move_crypto_comment($to, 'usr_coinvata', 'usr_'.$check_user_level2->username, $amountTo, 'buy_coinvata');

        $balToAfter = getbalance($to,'usr_'.$check_user_level2->username); // Balance To
    }
    else
    {
    	$type = "buy coinvata";
    	$account_id = StellarInfo::where('id',2)->first()->account_id;
    	$memo = '5;'.time();
    	$txtmemo = $from;
    	$num = $crypto_to;
    	$sender_id = StellarInfo::where('id',3)->first()->account_id;
    	$seed_id = StellarInfo::where('id',3)->first()->seed_id;
    	$platform = 'mobile';

    	$sendto_user = coinvata_stellar($type,$memo,$txtmemo,$num,$id,$current_price,$displayprice,$platform);


    	$data_json = json_decode($sendto_user);

    	if($data_json[0]->msj == "None error stellar POD")
    	{
    		$server = Server::publicNet();

    		$sourceKeypair = Keypair::newFromSeed($seed_id);

    		$destinationAccountId = $account_id;

    		$destinationAccount = $server->getAccount($destinationAccountId);

    // Build the payment transaction
    		$transaction = \ZuluCrypto\StellarSdk\Server::publicNet()
    		->buildTransaction($sourceKeypair->getPublicKey())
    		->addOperation(
    			PaymentOp::newNativePayment($destinationAccountId, $num)
    		)
    		->setTextMemo($memo)
    		;  
    // Sign and submit the transaction
    		$response = $transaction->submit($sourceKeypair->getSecret());

    		$sendto_admin = move_crypto_comment($from, 'usr_'.$check_user_level2->username, 'usr_coinvata', $crypto_from, 'sell_coinvata');
    	}
    	else
    	{

    		$msg = array("text"=>'Coinvata failed, please try again.');
    		$datamsg = response()->json([
    			'error' => $msg
    		]);

    		return $datamsg->content();
    	}

    	$balToAfter = xlm_getbalance_pod($id);
    }

    if($from != "DOGE")
    {
    	$coinvatapinkexc = ConvertPinkexc::create([

    		'memberID' => $id,
    		'username' => 'usr_'.$check_user_level2->username,
    		'email' => $check_user_level2->email,
    		'currentBalFrom' => $max,
    		'currentBalTo' => $currentBalTo,
    		'amountFrom' => $crypto_from,
    		'amountTo' => $amountTo,
    		'balToAfter' => $balToAfter,
    		'currencyFrom' => $from,
    		'currencyTo' => $to,
    		'date' => Carbon::now(),
    		'status' => 'Completed',
    		'rate' => round($current_price,8),
    		'current_rate' => round($displayprice,5)
    	]);
    }
    else
    {
    	$coinvatapinkexc = ConvertPinkexc::create([

    		'memberID' => $id,
    		'username' => 'usr_'.$check_user_level2->username,
    		'email' => $check_user_level2->email,
    		'currentBalFrom' => $max,
    		'currentBalTo' => $currentBalTo,
    		'amountFrom' => $crypto_from,
    		'amountTo' => $amountTo,
    		'balToAfter' => $balToAfter,
    		'currencyFrom' => $from,
    		'currencyTo' => $to,
    		'date' => Carbon::now(),
    		'status' => 'Completed',
    		'rate' => round($current_price,8),
    		'current_rate' => round($displayprice,2)
    	]);

    }

    $coinvataData = ConvertPinkexc::where('memberID',$id)->orderBy('id', 'desc')->first();

    $countCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->take(1)->count();

    if($countCoinvata != 0)
    {
    	$dataCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->first();

    	$total_usage = $dataCoinvata->usage + round($crypto_from,8);

    	if($total_usage < 50000)
    	{
      //Classic
    		$coinvata_level = CoinvataLevel::where('id',1)->first();
    		$level_id = $coinvata_level->id;
    	}
    	else if($total_usage >= 50001 && $total_usage < 100000)
    	{
      //Premier
    		$coinvata_level = CoinvataLevel::where('id',2)->first();
    		$level_id = $coinvata_level->id;
    	}
    	else
    	{
      //Infinite
    		$coinvata_level = CoinvataLevel::where('id',3)->first();
    		$level_id = $coinvata_level->id;
    	}

    	$usage_coinvata = CoinvataUsage::where('id',$id)
    	->update([
    		'level_id' => 1,
    		'usage' => round($crypto_from,8)
    	]);
    }
    else
    {
    	$usage_coinvata = CoinvataUsage::create([
    		'uid' => $id,
    		'level_id' => 1,
    		'usage' => round($crypto_from,8),
    	]);
    }

    send_email_coinvata($coinvataData->id,$from,$to,$displayprice,$crypto_from,$amountTo,$rate,$id);

    $msg = array("text"=>'Successfully convert.');
    $datamsg = response()->json([
    	'success' => $msg
    ]);

    return $datamsg->content();
}
else
{
	if($to != "XLM")
	{
    $balToAfter = getbalance($to,'usr_'.$check_user_level2->username); // Balance To
}
else
{
	$balToAfter = xlm_getbalance_pod($id);
}


$coinvatapinkexc = ConvertPinkexc::create([

	'memberID' => $id,
	'username' => 'usr_'.$check_user_level2->username,
	'email' => $check_user_level2->email,
	'currentBalFrom' => $max,
	'currentBalTo' => $currentBalTo,
	'amountFrom' => $crypto_from,
	'amountTo' => $amountTo,
	'balToAfter' => $balToAfter,
	'currencyFrom' => $from,
	'currencyTo' => $to,
	'date' => Carbon::now(),
	'status' => 'Process',
	'rate' => round($current_price,8),
	'current_rate' => round($displayprice,5),
	'txhash' => $sendto_admin
]);

$coinvataData = ConvertPinkexc::where('memberID',$id)->orderBy('id', 'desc')->first();

$countCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->take(1)->count();

if($countCoinvata != 0)
{
	$dataCoinvata = CoinvataUsage::where('uid',$id)->orderBy('id', 'desc')->first();

	$total_usage = $dataCoinvata->usage + round($crypto_from,8);

	if($total_usage < 50000)
	{
      //Classic
		$coinvata_level = CoinvataLevel::where('id',1)->first();
		$level_id = $coinvata_level->id;
	}
	else if($total_usage >= 50001 && $total_usage < 100000)
	{
      //Premier
		$coinvata_level = CoinvataLevel::where('id',2)->first();
		$level_id = $coinvata_level->id;
	}
	else
	{
      //Infinite
		$coinvata_level = CoinvataLevel::where('id',3)->first();
		$level_id = $coinvata_level->id;
	}

	$usage_coinvata = CoinvataUsage::where('id',$id)
	->update([
		'level_id' => 1,
		'usage' => round($crypto_from,8)
	]);
}
else
{
	$usage_coinvata = CoinvataUsage::create([
		'uid' => $id,
		'level_id' => 1,
		'usage' => round($crypto_from,8),
	]);
}

send_email_process_coinvata($coinvataData->id,$from,$to,$id);

$msg = array("text"=>'Your conversion is under process.');
$datamsg = response()->json([
	'success' => $msg
]);

return $datamsg->content();

}

}

}


}
else
{
	$msg = array("text"=>"No access");
	$datamsg = response()->json([
		'error' => $msg
	]);
} 

}
######################Level2#########################
public function uploadlevel2image(){
	$upload_dir = "../0f7d1f94da98b6836fdf1e14b1a7b6e7";
	if(!is_dir($upload_dir)) { mkdir($upload_dir,0777); }
	$user_dir = current(explode('-',$_FILES['myFile']['name']));
	if(!is_dir($upload_dir."/".$user_dir)) { mkdir(($upload_dir."/".$user_dir),0777); }

	$target_path = $upload_dir."/".$user_dir."/".basename($_FILES['myFile']['name']);

	if (move_uploaded_file($_FILES['myFile']['tmp_name'], $target_path)) {
		echo "Upload and move success".$_FILES['myFile']['tmp_name']; 	
	} else {
		echo '{"error": {"text":"There was an error uploading the file, please try again!"}}';		 
	}

	$user_folder_name = '\user_'.current(explode('-',$request->file('myFile')->getClientOriginalName()));
            //$path = public_path().'/storage/0f7d1f94da98b6836fdf1e14b1a7b6e7'.$user_folder_name;
	$path = realpath(base_path().'\..\assets\0f7d1f94da98b6836fdf1e14b1a7b6e7').$user_folder_name;


//check directory exists or not
	if (!File::exists($path))
	{
            //if not exists    
            //create new directory
		File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

	}

	$cover1 = $request->file('myFile');

	if(realpath(base_path().'/../assets/0f7d1f94da98b6836fdf1e14b1a7b6e7/'.$user_folder_name.'/'.$cover1->getClientOriginalName())){


		$cover1->move(realpath(base_path().'/../assets/0f7d1f94da98b6836fdf1e14b1a7b6e7/').$user_folder_name, '(2)'.$cover1->getClientOriginalName());     
		$document = 'assets/0f7d1f94da98b6836fdf1e14b1a7b6e7/user_'.$id.'/(2)'.$cover1->getClientOriginalName();

	}else{



		$cover1->move(realpath(base_path().'/../assets/0f7d1f94da98b6836fdf1e14b1a7b6e7/').$user_folder_name, $cover1->getClientOriginalName());     
		$document = 'assets/0f7d1f94da98b6836fdf1e14b1a7b6e7/user_'.$id.'/'.$cover1->getClientOriginalName();
	}

	if ($document) {
		$msg = array("text"=>"Upload and move success".$cover1->getClientOriginalName());
		$datamsg = response()->json([
			'success' => $msg
		]);	
	} else {
		$msg = array("text"=>"There was an error uploading the file, please try again!");
		$datamsg = response()->json([
			'error' => $msg
		]);	 
	}

}

public function proceeduploadlevel2(Request $request)
{

	$document_1 = '';
	$document_1_1 = '';
	$document_2 = '';
	$document_2_2 = '';
	$document_3 = '';
	
	$id = $request->id;
	$touseremail = $request->email;
	$fullname = $request->fullname;
	$target = $request->target;

	$gg1= explode('_',$target);
	$gg = end($gg1);
	$id = current(explode('-',$gg)); 
	$ez1=explode('-',$target);
	$ez=end($ez1);
	$check = current(explode('.',$ez));
	$target_path = "0f7d1f94da98b6836fdf1e14b1a7b6e7/user_".$id."/".$target; 
	
	if($check == 'IC')
	{
		$document_1 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_ic_front' => $document_1
		]);
	}
	elseif($check == 'ICend')
	{
		$document_1_1 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_ic_end' => $document_1_1
		]);

	}
	elseif($check == 'bank1')
	{
		$document_2 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_bank1' => $document_2
		]);
		
	}
	elseif($check == 'bank2')
	{
		$document_2_2 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_bank2' => $document_2_2
		]);

	}
	elseif($check == 'selfie')
	{
		$document_3 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_userpic' => $document_3
		]);
	}


	$userData = Kyc::where('uid',$id)->first();

	/*send email notification to user */
	if(!empty($userData->document_ic_front) && !empty($userData->document_bank1) && !empty($userData->document_ic_end) && !empty($userData->document_userpic) ){
		
		$updt2 = Kyc::where('uid', $id)
		->update([
			'level' => 'Level 2',
			'status' => 'pending for review'
		]);
		
		echo '{"success":{"text":"Your files was uploaded! Will be reviewed soon as possible."}}';
	}
	else{echo '{"error":{"text":"Continue with KYC."}}';}
}

public function reuploadlevel2(Request $request)
{

	$document_1 = '';
	$document_1_1 = '';
	$document_2 = '';
	$document_2_2 = '';
	$document_3 = '';

	$id = $request->id;
	$touseremail = $request->email;
	$fullname = $request->fullname;
	$target = $request->target;

	$gg1= explode('_',$target);
	$gg = end($gg1);
	$id = current(explode('-',$gg)); 
	$ez1=explode('-',$target);
	$ez=end($ez1);
	$check = current(explode('.',$ez));
	$target_path = "0f7d1f94da98b6836fdf1e14b1a7b6e7/user_".$id."/".$target; 
	if($check == 'IC')
	{
		$document_1 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_ic_front' => $document_1,
			'reupload_ic_front' => 0 
		]);
	}
	elseif($check == 'ICend')
	{
		$document_1_1 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_ic_end' => $document_1_1,
			'reupload_ic_end' => 0
		]);

	}
	elseif($check == 'bank1')
	{
		$document_2 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_bank1' => $document_2,
			'reupload_bank1' => 0
		]);

	}
	elseif($check == 'bank2')
	{
		$document_2_2 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_bank2' => $document_2_2,
			'reupload_bank2' => 0
		]);

	}
	elseif($check == 'selfie')
	{
		$document_3 = $target_path;
		$updt1 = Kyc::where('uid', $id)
		->update([
			'document_userpic' => $document_3,
			'reupload_userpic' => 0
		]);
	}


	$userData = Kyc::where('uid',$id)->first();

	/*send email notification to user */
	if($userData->reupload_ic_front != 1 && $userData->reupload_bank1 != 1 && $userData->reupload_ic_end != 1 && $userData->reupload_bank2 != 1 && $userData->reupload_userpic != 1 ){

		$updt2 = Kyc::where('uid', $id)
		->update([
			'level' => 'Level 2',
			'status' => 'pending for review'
		]);

		echo '{"success":{"text":"Your files was uploaded! Will be reviewed soon as possible."}}';
	}
	else{echo '{"error":{"text":"Continue with KYC."}}';}

}

/*###### SENANG PAY ######*/
public function test(Request $request) {
	$id = $request->id;
	$token = $request->token;
	$amountMyr = $request->amount;
	$currentPrice = $request->currentPrice;
	$limit = $request->limit;
	$dataLimit = $request->dataLimit;
	$secretkey = '11117-831';
	$detail = $request->detail;
	$crypto = $request->coin;
	$limit_balance = $request->limit_balance;
	
	$count = Pinkexcbuy::where('uid', $id)->where('crypto',$crypto)->count();
	if($count != 0|| $count != ''){

		$getdate =Pinkexcbuy::where('uid', $id)->where('crypto',$crypto)->orderBy('id', 'desc')->first()->created_at;
		$newtimestamp = strtotime($getdate.'+10 minutes');

		$currentdate = date('Y-m-d H:i:s');
		$tmp = strtotime($currentdate);

		$test = ($newtimestamp - $tmp)/60;
		$newdate = date('Y-m-d H:i:s', $newtimestamp);

		$new = number_format($test,0);

		if($currentdate <= $newdate ){
			$msg = array("text"=>"Please wait for $new minutes to make a new buy order for $crypto. Thank you.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}
		else{
			$msg = array("text"=>"No.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

		}
	}

}
public function senangpay(Request $request){
	$msg = array("text"=>"This service is currently under maintenance.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

}
/*###### ORDER BUY ######*/
public function orderbuy(Request $request){
	$msg = array("text"=>"This service is currently under maintenance.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

}

public function orderbuy1(Request $request)
{

	$id = $request->id;
	$token = $request->token;
	$amountMyr = $request->amount;
	$crypto = $request->crypto;
	$systemToken = apiToken($id);



	if($token == $systemToken)
	{
		$current_price = PriceAPI::where('crypto', $crypto)->first()->price;
		$buyeremail = User::where('id',$id)->first()->email;
		$buyaddress = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->address;
		$buyusername = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->label;
		if($crypto == 'XLM'){$crypto_balance = xlm_getbalance_pod($id);}
		else{
			$crypto_balance = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance;
		}
		$crypto_balance_myr = round(($current_price * $crypto_balance), 2);
		$limit_balance = Limitation::where('uid',$id)->where('category','buy')->orderBy('id', 'desc')->limit(1)->first()->limit_balance;
                //CALCULATION
		$tableSetting = Setting::where('id', '1')->first();
		$percentage_buy = $tableSetting->buy_comission;
		if($crypto =='DOGE'){
			$buy_price = round(($current_price + ($current_price * $percentage_buy)), 6);
		}
		else{
			$buy_price = round(($current_price + ($current_price * $percentage_buy)), 2);
		}

		$new_crypto_amount = round($amountMyr / $buy_price, 5);
		$afterbal = round(($crypto_balance + $new_crypto_amount), 8);

		$row_buy = Kyc::where('uid', $id)->first();

		$status = $row_buy->status;
		$level = $row_buy->level;
		$icname = $row_buy->name;
		$phone = Kyc::where('id',$id)->first()->phone;

		$refusr =  User::where('id',$id)->first()->username;
		
     		//else 
		if($level == "Level 2" && $status == "completed"){
			if($crypto=='XLM'){
				$check = StellarPod::where('str_status','pending')->where(function ($query) use ($id) {
					$query->where('source_id', $id)
					->orWhere('destination_id', $id);
				})->count();
			}
			else{$check = 0;}	
			
			$count = Pinkexcbuy::where('uid', $id)->where('crypto',$crypto)->count();
			if($count != 0 && $count != ''){
				$getdate =Pinkexcbuy::where('uid', $id)->where('crypto',$crypto)->orderBy('id', 'desc')->first()->created_at;
				$newtimestamp = strtotime($getdate.'+10 minutes');

				$currentdate = date('Y-m-d H:i:s');
				$tmp = strtotime($currentdate);

				$test = ($newtimestamp - $tmp)/60;
				$newdate = date('Y-m-d H:i:s', $newtimestamp);

				$new = number_format($test,0);
				if($id=='26441'){$newdate = 0;}
				if($currentdate <= $newdate ){
					$msg = array("text"=>"Please wait for $new minutes to make a new buy order for $crypto. Thank you.");
					$datamsg = response()->json([
						'error' => $msg
					]);

					return $datamsg->content();

				}
			}

			if($id == '26441'){$adele=2;}
			else{$adele=100;}			

			if($check != 0){echo '{"error": {"text":"Sorry, there is a stellar transaction still pending. Please contact our support team."}}';}
			elseif($amountMyr < $adele){echo '{"error": {"text":"Sorry, The minimum limit for instant buy is 100 MYR per day. Please try again. Thank you."}}';}
			elseif($amountMyr > 100000){echo '{"error": {"text":"Sorry, The maximum limit for instant buy is 100000 MYR per day. Please try again. Thank you."}}';}
			elseif($amountMyr > $limit_balance){echo '{"error": {"text":"Sorry, limit balance now is RM '.$limit_balance.'"}}';}
			elseif($limit_balance == 0){ echo '{"error": {"text":"You are reach the limitation in this month"}}';}
			else
			{
				$process_time = 15;      
				$timeout = $process_time * 60;
				$timeout = time() + $timeout;
				$time = time();
				//$refnum = refforbuy('mobile', $crypto);

				if($crypto == "XLM"){
					$check = StellarPinkexcbuy::where('uid',$id)->where('status', 'unpaid')->first();
				}
				else{
					$check = Pinkexcbuy::where('uid', $id)->where('crypto', $crypto)->where('status', 'unpaid')->first();
				}
				if($check != null){
					if($crypto == "XLM"){
						$updt1 = StellarPinkexcbuy::where('id',$check->id)->update([
							'status' => 'cancel'
						]);
					}
					else{
						$updt1 = Pinkexcbuy::where('id', $check->id)
						->update([
							'status' => 'cancel'
						]);
					}
				}
				if($crypto == "XLM"){
                        			//NO uNPAID HISTORY
						//INSERT PINKEXCBUY
						$insert_verify = StellarPinkexcbuy::create([
						'uid' => $id,
						'username' => $buyusername,
						'currentbal' => $crypto_balance,
						'crypto_amount' => $new_crypto_amount,
						'afterbal' => $afterbal,
						'myr_amount' => $amountMyr,
						'process_time' => $process_time,
						'timeout' => $timeout,
						'start_time' => $time,
						'status' => 'unpaid',
						'crypto_release' => '0',
						'rate' => $buy_price,
						'current_rate' => $current_price,
						'pay_type' => 'Online Banking',
						'memo' => '3;' . time()
					]);
				}
				else{
					$insert_verify = Pinkexcbuy::create([
						'uid' => $id,
						'username' => $buyusername,
						'currentbal' => $crypto_balance,
						'crypto_amount' => $new_crypto_amount,
						'afterbal' => $afterbal,
						'myr_amount' => $amountMyr,
						'walladdress' => $buyaddress,
						'process_time' => $process_time,
						'timeout' => $timeout,
						'start_time' => $time,
						'status' => 'unpaid',
						'crypto_release' => '0',
						'rate' => $buy_price,
						'pay_type' => 'Online Banking',
						'crypto' => $crypto,
						'current_rate'=>$current_price
					]);
                        //INSERT LIMITATION
				}

				if ($insert_verify == null) {

					$msg = array("text"=>"#1 Error");
					$datamsg = response()->json([
						'error' => $msg
					]);

					return $datamsg->content();
				} else {
					$refnum = refforbuy('mobile', $crypto, $insert_verify->id);
					if($crypto != 'XLM'){
						$updt1 = Pinkexcbuy::where('id', $insert_verify->id)
						->update([
							'refnumber' => $refnum,
						]);
					}
					else{
						$updt1 = StellarPinkexcbuy::where('id', $insert_verify->id)
						->update([
							'refnumber' => $refnum,
						]);
					}

					$msg = array("text"=>"Your $crypto order has been made.");
					$datamsg = response()->json([
						'success' => $msg
					]);

					return $datamsg->content();
				}
			}
		}
		else
		{
			$msg = array("text"=>"Sorry, Please upgrade to level 2");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();
		}
	}
	else{

		$msg = array("text"=>"No access.");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}

}

/*####### Cancel Order ######*/
public function cancelorder(Request $request)
{

	$id = $request->id;
	$token = $request->token;
	$check = $request->crypto;
	$systemToken = apiToken($id);

	if($token == $systemToken)
	{
		if($check == 'XLM'){
			$data_pinkexc = StellarPinkexcbuy::where('uid',$id)->where('status', 'unpaid')->orderBy('id','desc')->limit(1)->first();
			$updt2 = StellarPinkexcbuy::where('id',$data_pinkexc->id)->update([
				'status' => 'cancel'
			]);
		}
		else{
			$data_pinkexc = Pinkexcbuy::where('uid',$id)->where('crypto',$check)->where('status', 'unpaid')->orderBy('id','desc')->limit(1)->first();
			$updt2 = Pinkexcbuy::where('id', $data_pinkexc->id)
			->update([
				'status' => 'cancel',
			]);
		}

		echo '{"success":{"text": "Transaction was canceled."}}';

	}
	else{echo '{"error":{"text": "No access"}}';}

}

/*###### CHECK ORDER ######*/
public function checkorder(Request $request)
{

	$id = $request->id;
	$token = $request->token;
	$check = $request->crypto;
	$systemToken = apiToken($id);

	if($token == $systemToken)
	{
		if($check == 'XLM'){
			$data_pinkexc = StellarPinkexcbuy::where('uid',$id)->where('status','unpaid')->orderBy('id','desc')->count();
		}
		else{
			$data_pinkexc = Pinkexcbuy::where('uid',$id)->where('crypto',$check)->where('status','unpaid')->orderBy('id','desc')->count();
		}
		
		if($data_pinkexc != 0){
			if($check == 'XLM'){
				$data_pinkexc = StellarPinkexcbuy::where('uid',$id)->where('status','unpaid')->orderBy('id','desc')->limit(1)->first();
			}
			else{
				$data_pinkexc = Pinkexcbuy::where('uid',$id)->where('crypto',$check)->where('status','unpaid')->orderBy('id','desc')->limit(1)->first();
			}
			$times = Carbon::parse($data_pinkexc->start_time)->format('M d, H:i:s');
			
			$msg = array("id"=>$data_pinkexc->id,"refnumber"=>$data_pinkexc->refnumber,"myr_amount"=>$data_pinkexc->myr_amount,"start_time"=>$times);
			$datamsg = response()->json([
				'OrderData' => $msg
			]);
	
			return $datamsg->content();
		}
		else{
			$msg = array("text"=>"No order currently pending.");
			$datamsg = response()->json([
				'error' => $msg
			]);
	
			return $datamsg->content();
		}
	}
	else{
		$msg = array("text"=>"No access.");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}

}


######################Secret Pin##################################
public function secretpinlogin(Request $request){

	$id = $request->id;
	$secret_pin = $request->secret_pin;

	$token = $request->token;
	$systemToken = apiToken($id);

	if($token == $systemToken)
	{
		$check = User::where('id',$id)->first()->secret_pin;

		if(Hash::check($secret_pin, $check)){
			$msg = array("text"=>"Successfully login.");
			$datamsg = response()->json([
			'success' => $msg
			]);

		return $datamsg->content();
		}
		else{
		$msg = array("text"=>"Incorrect secret pin.");
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
public function secretpinenable(Request $request){

	$id = $request->id;
	$password = $request->password;
	$token = $request->token;
	$systemToken =apiToken($id);
	if($token == $systemToken){
		$check = User::where('id',$id)->first()->password;
		if(Hash::check($password, $check)){echo'{"success":{"text":"Successfully enable secretpin login."}}';}
		else{echo '{"error":{"text":"Incorrect password."}}';}
	}
	else{echo '{"error":{"text": "No access"}}';}
}

########################2FA Enable###########################
public function create2fa(Request $request){

	$id = $request->id;
	$token = $request->token;
	$systemToken = apiToken($id);

	if($token == $systemToken)
	{
		$user = User::where('id',$id)->first();

		if($user->googleauth_status != '0')
		{
			$ga = new GoogleAuthenticator();
			$secret = $user->google_auth_code;
			$qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username.'@Colony', $secret);

		}
		else
		{
			$ga = new GoogleAuthenticator();
			$secret = $ga->createSecret();
			$qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username.'@Colony', $secret);

			$updt = User::where('id',$user->id)
			->update([
				'google_auth_code' => $secret
			]);


		}

		$datamsg = response()->json([
			'qrCodeUrl' => $qrCodeUrl,
			'secret'=> $secret
		]);

		return $datamsg->content();
	}
	else{echo '{"error":{"text": "No access"}}';}



}

########################2FA Enable###########################
public function enable2fa(Request $request){

	$id = $request->id;
	$code = $request->code;
	$password = $request->password;
	$secret = $request->google_auth_code;
	$token = $request->token;

	$systemToken = apiToken($id);

	if($token == $systemToken)
	{
		$userData = User::where('id',$id)->first();

		if(Hash::check($password, $userData->password))
		{
			$ga = new GoogleAuthenticator();

			$checkResult = $ga->getCode($secret);
			if($checkResult == $code)
			{
				$updt2 = User::where('id', $id)
				->update([
					'googleauth_status' => 1,
				]);

				echo '{"success":{"text":"Your Two-Factor Authentication have been successfully activated."}}';
			}
			else{echo '{"error":{"text": "Code did not match"}}';}
		}
		else{echo '{"error":{"text": "Password was wrong."}}';}
	}
	else{echo '{"error":{"text": "No access"}}';}

}
########################2FA Login###########################
public function login2fa(Request $request){

	$id = $request->id;
	$code = $request->code;
	$secret = $request->google_auth_code;


	$ga = new GoogleAuthenticator();
	$checkResult = $ga->getCode($secret);
	if($checkResult == $code){

		echo '{"success":{"text":"Successfully login with Two-Factor Authentication."}}';
	}
	else{echo '{"error":{"text": "Code did not match"}}';}

}
########################2FA Disable###########################
public function disable2fa(Request $request){

	$id = $request->id;
	$password = $request->password;
	$token = $request->token;

	$systemToken = apiToken($id);

	if($token == $systemToken)
	{

		$userData = User::where('id',$id)->first();

		if(Hash::check($password, $userData->password)){

			$updt2 = User::where('id', $id)
			->update([
				'googleauth_status' => 0,
			]);

			echo '{"success":{"text":"Your Two-Factor Authentication have been deactivated."}}';
		}
		else{echo '{"error":{"text": "Password was wrong."}}';}
	}
	else{echo '{"error":{"text": "No access"}}';}

}
##########################Disable Finger#########################

public function disablefinger(Request $request){
	
	$id = $request->id;
	$secretpin = $request->secretpin;
	$token = $request->token;
	$systemToken = apiToken($id);

	if($token == $systemToken){
		$userData = User::where('id',$id)->first();

		if(Hash::check($secretpin, $userData->secret_pin)){
			echo '{"success":{"text":"Fingerprint has been disable."}}';
		}
		else{echo '{"error":{"text": "Secret PIN was wrong."}}';}
	}
	else{echo '{"error":{"text": "No access"}}';}
}

public function coinpercentage(){

	$percentage = PriceAPI::all();

	echo '{"btc": "'.$percentage[0]->percentage.'" , "bch":"'.$percentage[1]->percentage.'" , "eth":"'.$percentage[2]->percentage.'" , "dash":"'.$percentage[3]->percentage.'", "ltc":"'.$percentage[4]->percentage.'", "xrp":"'.$percentage[5]->percentage.'", "xlm":"'.$percentage[6]->percentage.'", "doge":"'.$percentage[7]->percentage.'"}'; 	
}

public function market(){

	$percentage = PriceAPI::all();

	echo '{"btc": "'.number_format($percentage[0]->price, 2, '.', '').'" , "bch":"'.number_format($percentage[1]->price, 2, '.', '').'" , "eth":"'.number_format($percentage[2]->price, 2, '.', '').'" , "dash":"'.number_format($percentage[3]->price, 2, '.', '').'", "ltc":"'.number_format($percentage[4]->price, 2, '.', '').'", "xrp":"'.number_format($percentage[5]->price, 2, '.', '').'", "xlm":"'.number_format($percentage[6]->price, 2, '.', '').'", "doge":"'.number_format($percentage[7]->price, 4, '.', '').'", "life":"'.number_format($percentage[8]->price, 2, '.', '').'"}';  
}
#################Resend email####################

public function resendemail(Request $request){
	$email = $request->email;
	$username = $request->username;
	$hash = sha1($email);
	send_email_verify($email,'Colony Account Verification',$username,'To unlock Colony full features need to activate your account with link below.',$hash);
	echo '{"success":{"text":"The email has been resend."}}';
}

#################Verify email###################

public function emailverify(Request $request){
	$email = $request->email;
	$check = Kyc::where('email',$email)->first()->email_verified;
	if($check == 1){echo '{"success":{"text":"Your email has been verified."}}';}
	else{echo '{"error":{"text":"Your email still have not been verified."}}';}
}


#################State #########################
public function state()
{
	$state = State::all();

	$datamsg = response()->json([
		'state' => $state
	]);

	return $datamsg->content();
}

 #################Bank Details #########################
public function bankList()
{
	$state = Banklist::all();

	$datamsg = response()->json([
		'banklist' => $state
	]);

	return $datamsg->content();
}

#################App version#########################
public function appversion()
{
	$appver = Appver::where('id',1)->first();

	$datamsg = response()->json([
		'version' => $appver->version,
		'iosversion' => $appver->ios_version,
		'iosversion2' => $appver->ios_version2
	]);

	return $datamsg->content();
}


     #################Generate Address #########################
public function generate_address(Request $request)
{
	$id = $request->id;
	$crypto = $request->crypto;
	$token = $request->token;
	$address = $request->address;
	$secret = $request->secret; 

	$systemToken = apiToken($id);
	if($token == $systemToken)
	{
		$check_verification = Kyc::where('uid', $id)->first();

		
     		//if($check_verification == null || ( $check_verification->level == 'Level 1' && $check_verification->status == 'uncompleted')){

     			//$msg = array("text"=>"Sorry, Please upgrade to level 2");
     			//$datamsg = response()->json([
     				//'error' => $msg
     			//]);

     			//return $datamsg->content();
     		//}
     		//else
     		//{

            //  dd($request->wallet_type);
		/*if ($crypto == 'BTC'){
			$msg = array("text"=>"ETH currently under maintenance.");
     			$datamsg = response()->json([
     				'error' => $msg
     			]);

     			return $datamsg->content();

		}*/


		$get_user = User::where('id', $id)->first();
		$label = 'usr_' . $get_user->username;

		$chec_usr = WalletAddress::where('uid', $id)->where('crypto', $request->crypto)->first();

        //if doesn't exist

		if ($chec_usr == null) {

			$crypto = $request->crypto;

			if ($crypto == 'XLM') {$create_nodes = 1;$secret = null;} 
			elseif ($crypto == 'XRP'){$create_nodes = $address;$secret = base64_encode($secret);}
			else {$create_nodes = addCrypto($crypto, $label);$secret = null;}

			if($create_nodes){
           //insert into table wallet
				WalletAddress::create([
					'uid' => $id,
					'label' => $label,
					'address' => $create_nodes,
					'secret' => $secret,      						
					'available_balance' => 0.0000,
					'crypto' => $crypto
				]);

            //insert into table limitation

				if($get_user->country == 130)
				{
            //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 100000,
						'category' => 'sell'
					]);
				}
				else
				{
     					            //buy
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'buy'
					]);

            //sell
					Limitation::create([
						'uid' => $id,
						'fullname' => $label,
						'limit_usage' => 0.0000,
						'limit_balance' => 50000,
						'category' => 'sell'
					]);
				}

				$msg = array("text"=>"Your new wallet have been successfully added !");
				$datamsg = response()->json([
					'success' => $msg
				]);

				return $datamsg->content();

			}
			else{


				$msg = array("text"=>"#1 Failed add wallet");
				$datamsg = response()->json([
					'error' => $msg
				]);

				return $datamsg->content();
			}


		}else{

			$msg = array("text"=>"#2 This crypto is already created");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();


		}
     		//}
	}
	else{echo '{"error":{"text": "No access"}}';}
}

####################gasprice#########################
public function gasprice(){
	$gasprice = Gasprice::where('id',1)->first();
	
	$normal = $gasprice->average;
	$fast =$gasprice->rapid;

	if($normal == '0' || $normal == ''){$normal=15;}
	if($fast == '0' || $fast == ''){$fast=50;}

	$datamsg = response()->json([
		'normal' => $normal,
		'fast' => $fast
	]);

	return $datamsg->content();

}
public function ethfee(Request $request){
	$converter = new \Bezhanov\Ethereum\Converter();

	$id = $request->id;
	$gprice = $request->trans;
	$gasprice = Gasprice::where('id',1)->first();
	
	$normal = $gasprice->average;
	$fast =$gasprice->rapid;
	if($fast == '0'){
		$fast = 50;
	}
	if($normal == '0'){
		$normal = 15;
	}

	if($gprice == 'normal'){
		$gprice = $normal;
	}
	else{
		$gprice = $fast;
	}
	
	//$balance = WalletAddress::where('uid',$id)->where('crypto','ETH')->first()->available_balance;

	//$balance = 15.20475;

	$address_from = WalletAddress::where('uid',$id)->where('crypto','ETH')->first()->address;
				//$converter = new \Bezhanov\Ethereum\Converter();

	$balance_from = Ethereum::eth_getBalance($address_from,'latest',TRUE);
	$balance_from = number_format($balance_from, 0, '', '');
	$balance = $converter->fromWei($balance_from, 'ether');
//dd($balance);
	
	$current_price = PriceAPI::where('name', 'Ethereum')->first()->price;
	$withdraw_commision = Setting::first()->withdrawal_commission;
	$fee = $withdraw_commision / $current_price;	
	//dd($converter->fromWei($converter->toWei($gprice, 'gwei')*100000), 'ether');
	$network = ($converter->toWei($gprice, 'gwei')*100000)*2;
	$network = ($converter->fromWei($network, 'ether'));

	$total = $network + $fee;
	
	$spend = $balance - $total;

	$datamsg = response()->json([
		'total_fee' => $total,
		'balance' => $balance,
		'amount_can_spend' => $spend
	]);

	return $datamsg->content();
	
}

/*######################## COINVATA DETAILS ######################*/
public function coinvataDetails(Request $request) {

	$id = $request->id;
	$token = $request->token;
	$convertFrom = $request->coinvataFrom;
	$convertTo = $request->coinvataTo;

	$logo_btc = PriceAPI::where('crypto', 'BTC')->first()->logo2;
	$logo_bch = PriceAPI::where('crypto', 'BCH')->first()->logo2;
	$logo_ltc = PriceAPI::where('crypto', 'LTC')->first()->logo2;
	$logo_dash = PriceAPI::where('crypto', 'DASH')->first()->logo2;
	$logo_doge = PriceAPI::where('crypto', 'DOGE')->first()->logo2;
	$logo_xlm = PriceAPI::where('crypto', 'XLM')->first()->logo2;
	$logo_xrp = PriceAPI::where('crypto', 'XRP')->first()->logo2;
	$logo_eth = PriceAPI::where('crypto', 'ETH')->first()->logo2;

	$count_coinvata = CoinvataUsage::where('uid',$id)->count();

	if($count_coinvata != 0)
	{
		$coinvata_level = CoinvataUsage::where('uid',$id)->first()->level_id;

		$rate = CoinvataLevel::where('id',$coinvata_level)->first()->fee;

	}
	else
	{
		$rate = CoinvataLevel::where('id',1)->first()->fee;
	}


	$coinvata_price = coinvata_price($convertFrom,$convertTo,$rate);
	$data_coinvata = json_decode($coinvata_price->content());

	if($convertFrom == "DOGE")
	{
		$current_price = round($data_coinvata->current_price, 8);
		$displayprice = number_format($data_coinvata->displayprice, 8);
		$minimum_price = round($data_coinvata->minimum_price, 8);
		$maximum_price = round($data_coinvata->maximum_price, 8);
	}
	else
	{
		$current_price = round($data_coinvata->current_price, 5);
		$displayprice = round($data_coinvata->displayprice, 5);
		$minimum_price = round($data_coinvata->minimum_price, 5);
		$maximum_price = round($data_coinvata->maximum_price, 5);
	}

	$datamsg = response()->json([
		'convertFrom' => $convertFrom,
		'convertTo' => $convertTo,
		'logo_btc' => $logo_btc,
		'logo_bch' => $logo_bch,
		'logo_ltc' => $logo_ltc,
		'logo_dash' => $logo_dash,
		'logo_doge' => $logo_doge,
		'logo_xlm' => $logo_xlm,
		'logo_eth' => $logo_eth,
		'current_price' => $current_price,
		'displayprice' => $displayprice,
		'minimum_price' => $minimum_price,
		'maximum_price' => $maximum_price,
		'rate' => $rate

	]);

	return $datamsg->content();
}
public function resetsecretpin(Request $request) {
	$id = $request->id;
	$secret_pin = $request->secretpin;
	$secretpin = bcrypt($secret_pin);
	$secret_pin2 = preg_match('/^[0-9]{6}$/', trim($secret_pin));
	if(strlen($secret_pin)!=6) { echo '{"error":{"text":"Secret PIN must be 6 digits."}}'; }
	elseif(!$secret_pin2){ echo '{"error":{"text":"Secret PIN must be digits only."}}'; }
	else{
		$updt2 = User::where('id', $id)
		->update([
			'secret_pin_status' => '1',
			'secret_pin' => $secretpin
		]);
		echo '{"success":{"text":"Secret PIN has been changed."}}';
	}
}
##################Stellar####################

public function check_balanceXLMuser(Request $request){
	
	$id_user = $request->id;
	$num = $request->amount;
	$accname = $request->accname;
	$bankname = $request->bankname;
	$memo = $request->memo;
	$token = $request->token;		

	$systemToken = apiToken($id_user);

	if($token == $systemToken)
	{ 
		$rows_price = PriceApi::where('crypto','XLM')->first(); 
		$rate_pricemyr = $rows_price->price;

		$price2 = round($rate_pricemyr,2);
		$price_instantbuy = ($price2 * 0.05) + $price2;
		$price_instantsell =  ( $price2-($price2 * 0.05));

		$rowsx = Limitation::where('uid',$id_user)->orderBY('id','DESC')->first();

		$rows = StellarInfo::where('id',2)->first(); 

    //horizon
		$horizon = $rows->str_horizon.'accounts/';

		$setting = Setting::where('id',1)->first(); 

		$total_fee =  settings('network_fee_xlm');	 	

		$amount = number_format($num, 7, '.', '');

		$fix_limit = $rows->fix_limit;
		$balance_amt = $amount + $total_fee;  
		$stellar_address = $rows->seed_id;
		$acc_id = $rows->account_id; 


		$rate = $price_instantsell;
		$rate1 = round($rate,2);
		$myrAmount = round(($rate1 * $amount),0); 

		$lbalance2 = $rowsx->limit_balance - $myrAmount;

		$getbal = xlm_getbalance_pod($id_user);

		$wait_str = 0;  

		$rowsy = StellarPod::where('str_status','pending')->where('source_id',$id_user)->first();
		if(isset($rowsy)){$wait_str = 1;}  

		$rowsw = StellarPod::where('str_status','pending')->where('destination_id',$id_user)->first();
		if(isset($rowsw)){$wait_str = 1;} 

		$rowss = User::where('xlm_block','1')->where('id',$id_user)->first();
		if(isset($rowss)){$wait_str = 1;} 

		if($wait_str == 1){$check_err = "Sorry, wait for the second. Please try again later";}
		elseif($myrAmount > $rowsx->limit_amount){$check_err = "Sorry, your cash out amount is exceed the limitation per month. Limit cash out per month is ".$rowsx->limit_amount." MYR";}
		elseif($myrAmount > $rowsx->limit_balance){$check_err = "Sorry, limit balance is now is RM ".$rowsx->limit_balance;}
		elseif($myrAmount < 100 && $id_user!='26441'){$check_err = "Sorry, The minimum amount for Instant Sell is 100 MYR";}
		elseif($lbalance2 < 0){$check_err = "Sorry, your balance cash out amount is exceed the limitation per month. Limit cash out per month is 100 000 MYR.";}
		elseif($getbal < $balance_amt){$check_err = "Sorry, your stellar is not enough to process the Instant Sell. You must left at least '$total_fee' stellar for transaction fee.";}
		elseif($rowsx->limit_balance == 0){$check_err = "You are reach the limitation in this month.";}
		else{$check_err = '';}

		/* check lumen sender */ 

		$ids = $id_user; 
		$rows = StellarPod::where('str_status','!=','cancel')->where(function ($query) use ($ids) {
			$query->where('source_id', $ids)
			->orWhere('destination_id', $ids);
		})->orderBY('id','desc')->first();


		if($check_err!='')
		{
			$arr = array( 
				"stellar_address"=> $stellar_address,
				"account_id"=> $acc_id,
				"msj"=> $check_err); 
		}
		elseif(!isset($rows)){
			$arr = array( 
				"stellar_address"=> $stellar_address,
				"account_id"=> $acc_id,
				"msj"=> "Sorry, your stellar is not enough to process the Instant Sell.");

		}
		elseif($rows->destination_id==$ids)
		{
			$balance =  $amount + $total_fee;
        if($rows->balance_destination <= $balance) // lumen not enough for user in database
        {
        	$arr = array( 
        		"stellar_address"=> $stellar_address,
        		"account_id"=> $acc_id,
        		"msj"=> "Sorry, your stellar is not enough to process the Instant Sell.");

        }else{  // send lumen success
            //$e = $this->save_instantsellXLM($amount,$memo,$id_user,$bankname,$accname);

        	$arr = array( 
        		"stellar_address"=> $stellar_address,
        		"account_id"=> $acc_id,
        		"msj"=> "None error stellar POD.");
        }
    }
    elseif($rows->source_id==$ids)
    {
    	$balance =  $amount + $total_fee;
        if($rows->balance_source <= $balance) // lumen not enough for user in database
        {
        	$arr = array( 
        		"stellar_address"=> $stellar_address,
        		"account_id"=> $acc_id,
        		"msj"=> "Sorry, your stellar is not enough to process the Instant Sell.");

        }else{	 // send lumen success
          //$e = $this->save_instantsellXLM($amount,$memo,$id_user,$bankname,$accname);

        	$arr = array( 
        		"stellar_address"=> $stellar_address,
        		"account_id"=> $acc_id,
        		"msj"=> "None error stellar POD.");
        }
    }else{	  
    	if($rows->source_id!=$ids && $rows->destination_id!=$ids) 
    	{
    		$arr = array( 
    			"stellar_address"=> $stellar_address,
    			"account_id"=> $acc_id,
    			"msj"=> "Sorry, your stellar amount not available.");
    	}
    	else{
                //$e = $this->save_instantsellXLM($amount,$memo,$id_user,$bankname,$accname);

    		$arr = array( 
    			"stellar_address"=> $stellar_address,
    			"account_id"=> $acc_id,
    			"msj"=> "None error stellar POD.");
    	}
    } 
    

    $datamsg = response()->json([
    	'success' => $arr
    ]);

    return $datamsg->content();        

}
else
{
	$msg = array("text"=>"No access");
	$datamsg = response()->json([
		'error' => $msg
	]);

	return $datamsg->content();
}

}

public function save_instantsellXLM(Request $request)
{  
	$id_user = $request->id;
	$amount = $request->amount;
	$accname = $request->accname;
	$bankname = $request->bankname;
	$memo = $request->memo;
	$token = $request->token;

	$systemToken = apiToken($id_user);

	if($token == $systemToken)
	{ 
		$rows_price = PriceApi::where('crypto','XLM')->first(); 

		$limit_xlm = Limitation::where('uid', $id_user)->where('category','sell')->orderby('id','desc')->first();
		$user_xlm = User::where('id', $id_user)->first();

		$rate_pricemyr = $rows_price->price;

		$price2 = round($rate_pricemyr,2);

		$price_instantbuy = ($price2 * 0.05) + $price2;
		$price_instantsell =  ( $price2-($price2 * 0.05));

		$amount = $amount;

		$memo = $memo;  
		$source_id = $id_user;
		$datenow = date("Y-m-d H:i:s");

		$limit_datenow = $limit_xlm->daterecord;

		if($limit_xlm->resident=='yes'){$limit_amount = '100000';}else{$limit_amount = '50000';}
		$limit_balance = $limit_xlm->limit_balance;
		$fullname = $user_xlm->username;    

		$rate = $price_instantsell;
		$rate1 = round($rate,2);
		$myrAmount = round(($rate1 * $amount),0);
		$limit_usage = $myrAmount;		   
		$lbalance2 = $limit_balance - $myrAmount;

		$rows = StellarInfo::where('id',1)->first(); 
		$admin_accountID = $rows->account_id;

		$rows2 = StellarInfo::where('id',2)->first();

		$user_accountID = $rows2->account_id;
		$total_fee =  settings('network_fee_xlm');

		$rowsx = User::where('id',$source_id)->first();

		$username_sender = $rowsx->username;

		$rowsy = Kyc::where('uid',$source_id)->first();
		$bankname = $bankname;
		$accname =  $accname;

		if(isset($rowsy)){

			if($rowsy->bankname1==$bankname){$banknumber = $rowsy->banknumber1;}
			if($rowsy->bankname2==$bankname){$banknumber = $rowsy->banknumber2;}

		}else{$banknumber = '';}

		//$refnum = 'MPODS'.$username_sender;

		$bal_sourC = xlm_getbalance_pod($source_id);
		$bal_sour = $bal_sourC - $amount - $total_fee;
		$bal_sourA = round($bal_sour,7);

    // $sql1 = new StellarPinkexcsell;
    // $sql1->uid = $source_id;
    // $sql1->username = $username_sender;
    // $sql1->currentbal = $bal_sourC;
    // $sql1->crypto_amount = $amount;
    // $sql1->afterbal = $bal_sourA;
    // $sql1->myr_amount = $myrAmount;
    // $sql1->paymethod = 'ATM';
    // $sql1->bankname = $bankname;
    // $sql1->accnum = $banknumber;
    // $sql1->accname = $accname;
    // $sql1->trans_no = $refnum;
       // $sql1->status = 'unpaid'; 
    // $sql1->recipient = '';
    // $sql1->rate = $rate1;
    // $sql1->current_rate = $rate_pricemyr;
    // $sql1->memo = $memo;
    // $sql1->save();

		$sql1 = StellarPinkexcsell::create([
			'uid' => $source_id,
			'username' => $username_sender,
			'currentbal' => $bal_sourC,
			'crypto_amount' => $amount,
			'afterbal' => $bal_sourA,
			'myr_amount' => $myrAmount,
			'paymethod' => 'Online Banking',
			'bankname'=> $bankname,
			'accnum' => $banknumber,
			'accname'=>$accname,
			'status' => 'process',
			'recipient' => '',
			'rate' => $rate1,
			'current_rate' => $rate_pricemyr,
			'memo' => $memo
		]);

		$id_new = $sql1->id;

		$trans_no = refforbuy('mobile', 'XLM', $id_new);

	        $updt = StellarPinkexcsell::where('id',$id_new)->update([
                    'trans_no'=>$trans_no
                ]);


    // $ins_pod = new StellarPod;
    // $ins_pod->type = 'instant sell';
    // $ins_pod->pod_id = $id_new;
    // $ins_pod->source_id = $source_id;
    // $ins_pod->balance_source = $bal_sourA;
    // $ins_pod->destination_id = 'admin';
    // $ins_pod->balance_destination = '0';
    // $ins_pod->send_token = $amount;
    // $ins_pod->memo = $memo;
    // $ins_pod->txtmemo = '';
    // $ins_pod->status = 'send';
    // $ins_pod->str_status = 'pending';
    // $ins_pod->str_transaction_id = ''; 
    // $ins_pod->save();

		$ins_pod = StellarPod::create([
			"type" => 'instant sell',
			"pod_id" => $id_new,
			"source_id" => $source_id,
			"balance_source" => $bal_sourA,
			"destination_id" => 'admin',
			"probBy" => 'mobile',
			"balance_destination" => '0',
			"send_token" => $amount,
			"myr_amount" => $myrAmount,
			"rate" => $rate1,
			"current_price" => $price2,
			"memo" => $memo,
			"txtmemo" => '',
			"status" => 'send',
			"str_status" => 'pending',
			"str_transaction_id" => '' 
		]);

		$sets = Limitation::where('uid',$source_id)->orderBy('id','desc')->first();
		$limit_level = $sets->limit_level;
		$resident = $sets->resident;

		if( date("Y-m",strtotime($limit_datenow)) == date("Y-m",strtotime($datenow))){$limit_use = $limit_usage;}
		else{$limit_use='0.0000';}

    // $sql2 = new Limitation;
    // $sql2->uid = $source_id;
    // $sql2->fullname = $fullname;
    // $sql2->limit_level = $limit_level;
    // $sql2->limit_amount = $limit_amount;
    // $sql2->daterecord = $datenow;
    // $sql2->limit_usage = $limit_use;
    // $sql2->resident = $resident;
    // $sql2->limit_balance = $lbalance2;
    // $sql2->category = 'sell';
    // $sql2->save();

		$sql2 = Limitation::create([
			"uid" => $source_id,
			"fullname" => $fullname,
			"limit_level" => $limit_level,
			"limit_amount" => $limit_amount,
			"daterecord" => $datenow,
			"limit_usage" => $limit_use,
			"resident" => $resident,
			"limit_balance" => $lbalance2,
			"category" => 'sell'
		]);


    //notification
		$price = PriceApi::where('crypto','XLM')->first()->price;
		$username = User::where('id',$source_id)->first()->username;
		$myrAmount = round(($price * $amount),2);
		$xlm_amnt = round($amount,5);
		$content = "Dear ".$username.", you have requested for instant sell XLM with amount ".$xlm_amnt." XLM ( ".$myrAmount." MYR ). The process will take within 24 until 48 hours on working days. We will notify and send an email to you after the process is complete. Thank you for your business. ";
		$sql3 = new Notification;
		$sql3->uid = $source_id;
		$sql3->title = 'Instant Sell Request';
		$sql3->content = $content;
		$sql3->read = '0';
		$sql3->save();

		$msg = array("text"=>"Process completed");
		$datamsg = response()->json([
			'success' => $msg
		]);
		return $datamsg->content();
	}
	else
	{
		$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}

}
public function check_balanceUser(Request $request){


	$rows_price = PriceApi::where('crypto','XLM')->first(); 
	$rate_pricemyr = $rows_price->price;

	$price2 = round($rate_pricemyr,2);

	$rows = StellarInfo::where('id',2)->first(); 

    //horizon
	$horizon = $rows->str_horizon.'accounts/';

	$setting = Setting::where('id',1)->first(); 

	$total_fee =  $setting->network_fee_xlm;	
	$comm_fee =  $setting->withdrawal_commission; 	

	$account_id = $request->text1;
	$memo = $request->text4;
	$txtmemo = $request->text6;
	$secretpin = $request->text5;
	$num = $request->text2; 
	$amount = number_format($num, 7, '.', '');

	$sender_id = $request->text3; 
	$acc_id = '';
	$rows = StellarInfo::where('id',2)->first(); 
	$fix_limit = $rows->fix_limit;
	$balance = $fix_limit + $amount; 
	$pop = 0;
	$ids =  $request->id;

	$balusr = round(xlm_getbalance_pod($ids),7); 
	$fee = $total_fee;
	$fixed_comm = round(($comm_fee/$price2),7);
	$amt_can_withdraw =  $balusr - ($fee + $fixed_comm); 


	$parts = explode('*', $account_id);


	if(isset($parts[1])){
		$domain = $parts[1];  

		$dom = "https://".$parts[1]."/.well-known/stellar.toml";

		if (@file_get_contents($dom)) {

			$file = fopen($dom,"r");

			while(! feof($file))
			{
				$will = fgets($file). "<br />";

				if(strpos($will, 'FEDERATION_SERVER=') !== false){
					$pass1 = str_replace('"', "", $will);
					if(strpos($pass1, '/<br />') !== false){
						$pass2 = str_replace('/<br />', "", $pass1); 
					}else{
						$pass2 = str_replace('<br />', "", $pass1); 
					}
					$pass3 = str_replace('FEDERATION_SERVER=', "", $pass2);

				}

			}
			fclose($file);	

		}else{
			$pop = 2; 
		}

		$str = $pass3.'?q='.$account_id.'&type=name';
		$url = preg_replace('/\\s/','',$str);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

		$data = curl_exec($ch);
		curl_close($ch);
           // print_r($data);
		$arr_data = json_decode($data);


          if(isset($arr_data->detail)){  // username in domain does not exist
          	$msj = $arr_data->detail;
          	$stellar_address = 'xx';
          	$acc_id = 'xx';  
          }elseif($pop==2){ 
          	$msj = 'Domain does not have federation stellar';
          	$stellar_address = 'xx';
          	$acc_id = 'xx';   
          }else{ 
          	$msj = '';
          	$stellar_address = $arr_data->stellar_address;
          	$acc_id = $arr_data->account_id;  
          } 

      }else{ 
      	$acc_id = $account_id;
      	$stellar_address = 'xx';
      	$msj = ''; 
      }

      /* check lumen sender */


      $i = 0;
      $l = 0;
      if($acc_id != ''){ 
      	$url = $horizon.''.$sender_id;
      	$ch = curl_init();

      	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
      	curl_setopt($ch, CURLOPT_HEADER, 0);
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      	curl_setopt($ch, CURLOPT_URL, $url);
      	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

      	$data = curl_exec($ch);
      	curl_close($ch);
// print_r($data);
      	$arr_data = json_decode($data);

      	foreach($arr_data->balances as $arr_datas){  
      		if($arr_datas->asset_type === 'native'){
      			$l = $i;
      		} 
      		$i++;
      	}  

      }


if($rows->account_id!=$account_id){  /// checking external 

	$wait_str = 0;  

	$rowsy = StellarPod::where('str_status','pending')->where('source_id',$ids)->first();
	if(isset($rowsy)){$wait_str = 1;}  

	$rowsw = StellarPod::where('str_status','pending')->where('destination_id',$ids)->first();
	if(isset($rowsw)){$wait_str = 1;} 

	$rowss = User::where('xlm_block','1')->where('id',$ids)->first();
	if(isset($rowss)){$wait_str = 1;} 

	$check_users = User::where('id', $ids)->first();


	if(!(Hash::check($secretpin, $check_users->secret_pin))){
		$arr = '[{  
			"stellar_address": "'.$stellar_address.'",
			"account_id": "'.$acc_id.'",
			"msj": "Secret PIN did not match. Try again. "
    }]';  //exist ' ; '

}
elseif(strlen($memo) > 25) {
	$arr = '[{  
		"stellar_address": "'.$stellar_address.'",
		"account_id": "'.$acc_id.'",
		"msj": "Length text for memo up to 25 character only "
    }]';  //exist ' ; '
}
elseif(strpos($memo, ';') !== false) {
	$arr = '[{  
		"stellar_address": "'.$stellar_address.'",
		"account_id": "'.$acc_id.'",
		"msj": "Please does not use character semicolon (;) in memo "
    }]';  //exist ' ; '
}
elseif($msj!=''){
	$arr = '[{  
		"stellar_address": "'.$stellar_address.'",
		"account_id": "'.$acc_id.'",
		"msj": "'.$msj.'" 
	}]';
}
elseif($wait_str == 1){ 
	$arr = '[{  
		"stellar_address": "'.$stellar_address.'",
		"account_id": "'.$acc_id.'",
		"msj": "Sorry, wait for the second. Please try again later.Please contact Administrator." 
	}]'; 
}	  
elseif($amount>$amt_can_withdraw){
	if($amt_can_withdraw<=0){$amt_can_withdraw = 0;}else{$amt_can_withdraw = $amt_can_withdraw;}
	$arr = '[{  
		"stellar_address": "'.$stellar_address.'",
		"account_id": "'.$acc_id.'",
		"msj": "Insufficient funds. Your maximum amount you can withdraw is '.$amt_can_withdraw.'" 
	}]'; 
}	
elseif($arr_data->balances[$l]->balance < $balance) // lumen stellar not enough
{
	$arr = '[{  
		"stellar_address": "'.$stellar_address.'",
		"account_id": "'.$acc_id.'",
		"msj": "Sorry, your stellar is not enough to process the withdrawal."
	}]'; 
} 
else // success
{   
	$rows = StellarPod::where('destination_id',$ids)->orwhere('source_id',$ids)->orderBY('id','desc')->first();
	if(!isset($rows)){
		$arr = '[{  
			"stellar_address": "'.$stellar_address.'",
			"account_id": "'.$acc_id.'",
			"msj": "Sorry, your stellar is not enough to process the withdrawal"
		}]'; 
	}
	elseif($rows->destination_id==$ids)
	{
		$balance =  $amount + $total_fee;
        if($rows->balance_destination <= $balance) // lumen not enough for user in database
        {
        	$arr = '[{  
        		"stellar_address": "'.$stellar_address.'",
        		"account_id": "'.$acc_id.'",
        		"msj": "Sorry, your stellar is not enough to process the withdrawal"
        	}]'; 
        }else{  // send lumen success	
        	$e = $this->insert_withdrawpod($account_id,$amount,$sender_id,$memo,$secretpin,$ids,$txtmemo,$fixed_comm,$total_fee);

        	$arr = '[{  
        		"stellar_address": "'.$stellar_address.'",
        		"account_id": "'.$acc_id.'",
        		"msj": "None error stellar POD"
        	}]'; 
        }
    }
    elseif($rows->source_id==$ids)
    {
    	$balance =  $amount + $total_fee;
        if($rows->balance_source <= $balance) // lumen not enough for user in database
        {
        	$arr = '[{  
        		"stellar_address": "'.$stellar_address.'",
        		"account_id": "'.$acc_id.'",
        		"msj": "Sorry, your stellar is not enough to process the withdrawal"
        	}]'; 
        }else{	 // send lumen success
        	$e = $this->insert_withdrawpod($account_id,$amount,$sender_id,$memo,$secretpin,$ids,$txtmemo,$fixed_comm,$total_fee);

        	$arr = '[{  
        		"stellar_address": "'.$stellar_address.'",
        		"account_id": "'.$acc_id.'",
        		"msj": "None error stellar POD"
        	}]'; 
        }
    }
    else{ 
    	$arr = '[{  
    		"stellar_address": "'.$stellar_address.'",
    		"account_id": "'.$acc_id.'",
    		"msj": "Sorry, your stellar is not enough to process the withdrawal"
    	}]'; 
    }
}


}else{ /// checking internal

	$rows = StellarPod::where('destination_id',$ids)->orwhere('source_id',$ids)->orderBY('id','desc')->first();

	$wait_str = 0;

	$rowsy = StellarPod::where('str_status','pending')->where('source_id',$ids)->first();
	if(isset($rowsy)){$wait_str = 1;}  

	$rowsw = StellarPod::where('str_status','pending')->where('destination_id',$ids)->first();
	if(isset($rowsw)){$wait_str = 1;} 

	$rowss = User::where('xlm_block','1')->where('id',$ids)->first();
	if(isset($rowss)){$wait_str = 1;} 

	$check_users = User::where('id', $ids)->first();


	if(!(Hash::check($secretpin, $check_users->secret_pin))){
		$arr = '[{  
			"stellar_address": "'.$stellar_address.'",
			"account_id": "'.$acc_id.'",
			"msj": "Secret PIN did not match. Try again. "
    }]';  //exist ' ; '

}
elseif(strlen($memo) > 25) {
	$arr = '[{  
		"stellar_address": "'.$stellar_address.'",
		"account_id": "'.$acc_id.'",
		"msj": "Length text for memo up to 25 character only "
        }]';  //exist ' ; '
    }
    elseif(strpos($memo, ';') !== false) {
    	$arr = '[{  
    		"stellar_address": "'.$stellar_address.'",
    		"account_id": "'.$acc_id.'",
    		"msj": "Please does not use character semicolon (;) in memo "
        }]';  //exist ' ; '
    } 
    elseif($wait_str == 1){ 
    	$arr = '[{  
    		"stellar_address": "'.$stellar_address.'",
    		"account_id": "'.$acc_id.'",
    		"msj": "Sorry, wait for the second. Please try again later. Please contact Administrator."
    	}]'; 
    }	  
    elseif($amount>$amt_can_withdraw){
    	if($amt_can_withdraw<=0){$amt_can_withdraw = 0;}else{$amt_can_withdraw = $amt_can_withdraw;}
    	$arr = '[{  
    		"stellar_address": "'.$stellar_address.'",
    		"account_id": "'.$acc_id.'",
    		"msj": "Insufficient funds. Your maximum amount you can withdraw is '.$amt_can_withdraw.'" 
    	}]'; 
    }	
    elseif(!isset($rows)){
    	$arr = '[{  
    		"stellar_address": "'.$stellar_address.'",
    		"account_id": "'.$acc_id.'",
    		"msj": "Sorry, your stellar is not enough to process the withdrawal"
    	}]'; 
    }
    elseif($rows->source_id==$ids)
    {
    	$balance =  $amount + $total_fee;
        if($rows->balance_source <= $balance) // lumen not enough for user in database
        {

        	$arr = '[{  
        		"stellar_address": "'.$stellar_address.'",
        		"account_id": "'.$acc_id.'",
        		"msj": "Sorry, your stellar is not enough to process the withdrawal"
        	}]'; 

                // $result = 1;
        }else{	 // send lumen success
        	$e = $this->save_withdrawXLMinternal($account_id,$amount,$sender_id,$memo,$secretpin,$ids,$txtmemo,$fixed_comm,$total_fee);

        	$arr = '[{  
        		"stellar_address": "'.$stellar_address.'",
        		"account_id": "'.$acc_id.'",
        		"msj": "None error stellar POD"
        	}]'; 
                // $result = 1;
        }
        
    }
    elseif($rows->destination_id==$ids)
    {
    	$balance =  $amount + $total_fee;
        if($rows->balance_destination <= $balance) // lumen not enough for user in database
        {
        	$arr = '[{  
        		"stellar_address": "'.$stellar_address.'",
        		"account_id": "'.$acc_id.'",
        		"msj": "Sorry, your stellar is not enough to process the withdrawal"
        	}]'; 
                // $result = 1;
        }else{  // send lumen success 
        	$e = $this->save_withdrawXLMinternal($account_id,$amount,$sender_id,$memo,$secretpin,$ids,$txtmemo,$fixed_comm,$total_fee);

        	$arr = '[{  
        		"stellar_address": "'.$stellar_address.'",
        		"account_id": "'.$acc_id.'",
        		"msj": "None error stellar POD"
        	}]'; 
                // $result = 1;
        }
    } 
    else // success
    {   
    	$arr = '[{  
    		"stellar_address": "'.$stellar_address.'",
    		"account_id": "'.$acc_id.'",
    		"msj": "Sorry, your lumen is not enough to process the withdrawal"
    	}]'; 
    }

}

return $arr;

}


public function insert_withdrawpod($account_id,$amount,$sender_id,$memo,$secretpin,$ids,$txtmemo,$fixed_comm,$total_fee){

	$info_admin = StellarInfo::where('id',1)->first();
	$add_admin = $info_admin->account_id;

	$info_user = StellarInfo::where('id',2)->first();
	$add_user = $info_user->account_id;

	$fee = settings('network_fee_xlm');

	if($account_id==$add_admin){$receive_id = 'admin';}else{$receive_id = 'wallet';}

	$ins_wdraw = new StellarWithdrawal;
	$ins_wdraw->uid = $ids;
	$ins_wdraw->stellar_pod_id = '';
	$ins_wdraw->destination_acc = $account_id;
	$ins_wdraw->destination_id = $receive_id;
	$ins_wdraw->fee = $total_fee;
	$ins_wdraw->com_withdrawal = $fixed_comm;
	$ins_wdraw->token = 'native';
	$ins_wdraw->send_token = $amount;
	$ins_wdraw->status = 'completed';
	$ins_wdraw->memo = $txtmemo;
	$ins_wdraw->txtmemo = $memo;
	$ins_wdraw->save();

	$new_wdraw = $ins_wdraw->id;

	$balurC = xlm_getbalance_pod($ids);
	$balur = $balurC - $amount - $fixed_comm - $fee;
	$balurA = round($balur,7);

	$current_price = PriceAPI::where('crypto', 'XLM')->first()->price;

	$myr_withdrawal = round(($current_price*($amount+settings('withdrawal_commission')))+settings('withdrawal_commission'),2);

	$rate = settings('withdrawal_commission');

	$current_price = $current_price;

	$ins_pod = new StellarPod;
	$ins_pod->type = 'withdraw';
	$ins_pod->pod_id = $new_wdraw;
	$ins_pod->source_id = $ids;
	$ins_pod->balance_source = $balurA;
	$ins_pod->destination_id = $receive_id;
	$ins_pod->balance_destination = '0';
	$ins_pod->send_token = $amount;
	$ins_pod->memo = $txtmemo;
	$ins_pod->txtmemo = $memo;
	$ins_pod->status = 'send';
	$ins_pod->probBy = 'mobile';
	$ins_pod->str_status = 'pending';
	$ins_pod->str_transaction_id = '';
	$ins_pod->myr_amount = $myr_withdrawal;
	$ins_pod->rate = $rate;
	$ins_pod->current_price = $current_price;
	$ins_pod->save();

	$new_pod = $ins_pod->id;

	$upd_wdraw = StellarWithdrawal::findOrFail($new_wdraw);
	$upd_wdraw->stellar_pod_id = $new_pod;
	$upd_wdraw->save();

    //notification
	$price = PriceApi::where('crypto','XLM')->first()->price;
	$username = User::where('id',$ids)->first()->username;
	$myrAmount = round(($price * $amount),2);
	$xlm_amnt = round($amount,5);

	$content = "Dear ".$username.", your XLM has been withdraw successfully with amount ".$xlm_amnt." XLM ( ".$myrAmount." MYR )";
	$sql_notify = new Notification;
	$sql_notify->uid = $ids;
	$sql_notify->title = 'XLM Withdraw';
	$sql_notify->content = $content;
	$sql_notify->read = '0';
	$sql_notify->save();


}
public function save_withdrawXLMinternal($account_id,$amount,$sender_id,$memo,$secretpin,$ids,$txtmemo,$fixed_comm,$total_fee)
{ 		
	$des_id = $account_id;    
	$amount = $amount; 
	$memo = $memo; 
	$txtmemo = $txtmemo;   
	$source_id = $ids;
	
	$rows = StellarInfo::where('id',1)->first();
	$admin_accountID = $rows->account_id;

	$rows2 = StellarInfo::where('id',2)->first(); 
	$user_accountID = $rows2->account_id;

	$rowsx = User::where('username',$memo)->first();

	if(isset($rowsx->id)){$dest_id = $rowsx->id;}else{$dest_id = 0;}
	
	if($user_accountID==$des_id && $dest_id!=0){
		$destination_id = $dest_id; 
	}  
	else{
		$destination_id = 'user'; 
	}  

	$queryd4x = new StellarWithdrawal;	
	$queryd4x->stellar_pod_id = '';
	$queryd4x->uid = $source_id;
	$queryd4x->status = 'completed';
	$queryd4x->send_token = $amount; 
	$queryd4x->destination_id = $destination_id;
	$queryd4x->destination_acc = $des_id;
	$queryd4x->fee = '0';
	$queryd4x->com_withdrawal = '0';
	$queryd4x->token = 'native';
	$queryd4x->memo = $txtmemo;
	$queryd4x->txtmemo = $memo;
	$queryd4x->save();

	$id_new2 = $queryd4x->id;

	$bal_sourceC = xlm_getbalance_pod($source_id);
	$bal_source = $bal_sourceC - $amount;
	$bal_sourceA = round($bal_source,7);

	$bal_destinationC = xlm_getbalance_pod($destination_id);
	$bal_destination = $bal_destinationC + $amount;
	$bal_destinationA = round($bal_destination,7);

	$current_price = PriceAPI::where('crypto', 'XLM')->first()->price;

	$myr_withdrawal = round(($current_price*$amount),2);

	$rate = settings('withdrawal_commission');

	$current_price = $current_price;

	$queryd4 = new StellarPod;	
	$queryd4->type = 'withdraw';
	$queryd4->str_transaction_id = '';
	$queryd4->pod_id = $id_new2;
	$queryd4->source_id = $source_id;
	$queryd4->balance_source = $bal_sourceA; 
	$queryd4->destination_id = $destination_id;
	$queryd4->balance_destination = $bal_destinationA;
	$queryd4->send_token = $amount; 
	$queryd4->memo = $txtmemo;
	$queryd4->txtmemo = $memo;
	$queryd4->status = 'internal';
	$queryd4->probBy = 'mobile';
	$queryd4->str_status = 'completed';
	$queryd4->myr_amount = $myr_withdrawal; 
	$queryd4->rate = $rate; 
	$queryd4->current_price = $current_price; 
	$queryd4->save();

	$id_new = $queryd4->id; 

	$sql_up = StellarWithdrawal::findOrFail($id_new2);	
	$sql_up->stellar_pod_id = $id_new; 
	$sql_up->save();


                    //notification
	$price = PriceApi::where('crypto','XLM')->first()->price;
	$username = User::where('id',$source_id)->first()->username;
	$myrAmount = round(($price * $amount),2);
	$xlm_amnt = round($amount,5);

	$content = "Dear ".$username.", your XLM has been withdraw successfully with amount ".$xlm_amnt." XLM ( ".$myrAmount." MYR )";
	$sql_notify = new Notification;
	$sql_notify->uid = $source_id;
	$sql_notify->title = 'XLM Withdraw';
	$sql_notify->content = $content;
	$sql_notify->read = '0';
	$sql_notify->save();


	return 'Transaction Successful';
}


public function news(){
	$news = News::where('category','news')->orderBy('id','desc')->limit(5)->get();

	foreach($news as $key => $new){
		$title = str_limit(strip_tags($new['title']),35);
		$content = html_entity_decode($new['content']);
		$arr_data[] = array('title'=>$title, 'full_title'=>$new['title'],'content'=>strip_tags($content), 'date'=>$new['created_at']);
	}
	
	$datamsg = response()->json([
		'news' => $arr_data
	]);
	
	return $datamsg->content();
}
public function notification(Request $request){

	$id = $request->id;
	$token = $request->token;
	$count = Notification::where('uid',$id)->orderBy('id','desc')->count();
	$systemToken = apiToken($id);
	if($token == $systemToken)
	{
		if($count=='' || $count==0){
			$msg = array("text"=>"No notification found.");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}
		else{
			$news = Notification::where('uid',$id)->orderBy('id','desc')->get();
			foreach($news as $key => $new){
				$title = str_limit(strip_tags($new['title']),35);
				$content = html_entity_decode($new['content']);
				$date=date_create($new['created_at']);

				$arr_data[] = array('id'=>$new['id'],'title'=>$title, 'full_title'=>$new['title'],'read'=>$new['read'],'content'=>strip_tags($content), 'date'=>date_format($date,"d-m-Y"));
			}

			$datamsg = response()->json([
				'news' => $arr_data
			]);
			return $datamsg->content();	


		}
	}
	else{
		$msg = array("text"=>"No access.");
		$datamsg = response()->json([
			'error' => $msg
		]);
		return $datamsg->content();
	}
}
public function withdrawXrp(Request $request)
{

	$id = $request->id;
	$token = $request->token;

	$destination = $request->crypto_address;
	$destination_tag = $request->destination_tag;
	$myr_amount = $request->myr_amount;
	$crypto = $request->crypto_type;
	$amount = $request->crypto_amount;

	$current_price = PriceApi::where('crypto', $crypto)->first()->price;

	$price_myr = round($current_price, 2);
	xrp_getbalance($id);
	$crypto_balance = round((WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance), 5);
	$crypto_balance_myr = round(($current_price * $crypto_balance), 2);

	    //NETWORK FEE RM0.5
	$network_fee_myr = Setting::where('id', '1')->first()->network_fee;
	$network_fee_crypto = round(($network_fee_myr / $price_myr), 5);
	    //WITHDRAWAL COMMISSION RM0.5
	$withdrawal_comm_myr = Setting::where('id', '1')->first()->withdrawal_commission;
	$withdrawal_comm_crypto = round(($withdrawal_comm_myr / $price_myr), 5);


	    //re-calculate crypto amount
	$total_fee_crypto = $network_fee_crypto + $withdrawal_comm_crypto;
	$crypto_amount = round(($myr_amount / $current_price), 8);
	$total_amount = $crypto_balance - $crypto_amount;
	$totalAll = $crypto_amount + $total_fee_crypto;

	$total_fee_myr = round(($current_price * $total_fee_crypto), 2);
	$max_send_crypto = $crypto_balance - $total_fee_crypto;
	$max_send_myr = $crypto_balance_myr - $total_fee_myr;

	    //END CALCULATION
	$check_users = User::where('id', $id)->first();

	if (Hash::check($request->secretpin, $check_users->secret_pin)) 
	{

		if (($crypto_balance == 0) || ($totalAll > $crypto_balance )) {

			$msg = array("text"=>"Insufficient Balance.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();
		} 
		else if($total_amount <= 20)
		{
			$msg = array("text"=>"You must leave 20 XRP reserve in the account you are sending from.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();
		}
		else if($amount < 0.001)
		{
			$msg = array("text"=>"Minimum amount you can withdraw is 0.001 XRP.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();
		}
		else {

			$fee = $withdrawal_comm_crypto;

			$withdraw = withdraw_xrp($id,$amount,$crypto,$destination,$destination_tag,$fee);

			Withdrawal::create([
				'uid' => $id,
				'status' => 'success',
				'amount' => $amount,
				'recipient' => $destination,
				'fee' => round($fee, 8),
				'txid' => $txid,
				'date' => Carbon::now(),
				'crypto' => 'XRP',
			]);

			$update_bal2 = xrp_getbalance($id);

			notify()->flash('Success!', 'success', [
				'timer' => 3000,
				'text' => 'Successfully withdraw',
				'buttons' => true
			]);
			$msg = array("text"=>"Successfully withdraw.");
			$datamsg = response()->json([
				'success' => $msg
			]);

			return $datamsg->content();
		}
	} 
	else 
	{
		$msg = array("text"=>"Secret PIN do not match.");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}
}

public function newsfeed()
{
	$news = News::where('category','news')->where('category_url','pinkexc')->orWhere('category_url','both')->selectRaw('title , content,images, DATE_FORMAT(created_at, "%d %b %Y") as date')->orderBy('created_at','desc')->limit(5)->get();
	$datamsg = response()->json([
		$news
	]);
	return $datamsg->content();

}

public function newsfeed_main()
{
	$news = News::where('category','news')->where('category_url','pinkexc')->orWhere('category_url','both')->selectRaw('title , content,images, DATE_FORMAT(created_at, "%d %b %Y") as date')->orderBy('created_at','desc')->limit(3)->get();
	$datamsg = response()->json([
		$news
	]);
	return $datamsg->content();

}



public function newsall()
{
	$news = News::where('category','news')->where('category_url','pinkexc')->orWhere('category_url','both')->selectRaw('title , content,images,DATE_FORMAT(created_at, "%d %b %Y") as date')->orderBy('created_at','desc')->get();
	$datamsg = response()->json([
		$news
	]);
	return $datamsg->content();

}

public function maxbalance(Request $request)
{

	$id = $request->id;
	$token = $request->token;
	$crypto = $request->crypto;


	$getbalance = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first();

	if($crypto == "ETH")
	{
//dd('sd');
		$converter = new \Bezhanov\Ethereum\Converter();
		$gas = 100000;
		$gasprice = Gasprice::where('id',1)->first();
		$normal = $gasprice->average;
		$gasPriceData = $converter->toWei($normal, 'gwei');
		$fee = $converter->fromWei($gas * $gasPriceData,'ether');
		$balanceData = $getbalance->available_balance - $fee;
		$balance = round($getbalance->available_balance, 5);
	}
	else
	{
		$balance = round($getbalance->available_balance, 5);
	}

	$msg = array("crypto_amount"=>$balance);
	$datamsg = response()->json([
		'cryptoBalance' => $msg
	]);


	return $datamsg->content();

}

public function get_ref(Request $request)
{

	$id = $request->id;
	$txid = $request->txid;
	$token = $request->token;
	$systemToken = apiToken($id);
	$crypto = $request->crypto;


	if($token == $systemToken)
	{ 
		$refcount = Ref_detail::where('uid',$id)->where('txid',$txid)->where('crypto',$crypto)->count();
		if($refcount !=0){
			$refdetail = Ref_detail::where('uid',$id)->where('txid',$txid)->where('crypto',$crypto)->first();

			$msg = array("payee"=>$refdetail->payee,
				"reference_detail"=>$refdetail->reference_detail, "category"=>$refdetail->category);
			$datamsg = response()->json([
				'success' => $msg
			]);
		}
		else{
			$msg = array("payee"=>'',"reference_detail"=>'', "category"=>'');
			$datamsg = response()->json([
				'success' => $msg
			]);
		}
		return $datamsg->content();
	}
	else
	{
		$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}

}

public function edit_ref(Request $request)
{

	$id = $request->id;
	$txid = $request->txid;
	$token = $request->token;
	$systemToken = apiToken($id);
	$crypto = $request->crypto;
	$refdetail = '';

	if($token == $systemToken)
	{ 
		$refcount = Ref_detail::where('uid',$id)->where('txid',$txid)->where('crypto',$crypto)->count();
		if($refcount !=0){
			$refdetail = Ref_detail::where('uid',$id)->where('txid',$txid)->where('crypto',$crypto)->first();
			if($request->refdetail != ''){
				$refdetail = $request->refdetail;
			}
			Ref_detail::where('uid',$id)->where('txid',$txid)->where('crypto',$crypto)
			->update([
				"payee"=>$request->payee,
				"reference_detail"=>$request->refdetail,
				"category"=>$request->category,
			]);

			$msg = array("text"=>'Successfully Update Reference Detail.');
			$datamsg = response()->json([
				'success' => $msg
			]);
		}
		else{
			$ref = Ref_detail::create([
				"uid"=>$id,
				"txid"=>$txid,
				"payee"=>$request->payee,
				"reference_detail"=>$request->refdetail,
				"category"=>$request->category,
				"crypto"=>$request->crypto,
			]);

			$msg = array("text"=>'Successfully Added Reference Detail.');
			$datamsg = response()->json([
				'success' => $msg
			]);
		}
		return $datamsg->content();
	}
	else
	{
		$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}

}
public function checkredeem(Request $request){
	$id = $request->id;
	$token = $request->token;
	$username = $request->username;
	$systemToken = apiToken($id);

	if($token == $systemToken)
	{
		$url = 'https://colony.pinkexc.com/register?url_code='.base64_encode($username);

		$appurl = str_rot13($username);

		//$totusr_all = User::where('urlcode', $username)->where('Level_2_status', '1')->count();
		$totusr_all= DB::table('users')
		->select('users.id','users.urlcode','kyc.level' )
		->join('kyc', 'users.id', '=', 'kyc.id')
		->where('users.urlcode', $username)
		->where(function ($query) {
			$query->where('kyc.level', 'Level 1')
			->orWhere('kyc.level', 'Level 2'); })
		->get()->count();


            //TOTAL UNREDEEM
		//$totusr_unredeem = User::where('urlcode', $username)->where('Level_2_status', '1')->where('urlcode_status', '0')->count();
		$totusr_unredeem = DB::table('users')
		->select('users.id','users.urlcode','kyc.level' )
		->join('kyc', 'users.id', '=', 'kyc.id')
		->where('users.urlcode', $username)
		->where(function ($query) {
			$query->where('kyc.level', 'Level 1')
			->orWhere('kyc.level', 'Level 2'); })
		->where('users.urlcode_status', '0')
		->get()->count();


            //TOTAL REDEEM 
		//$totusr_redeem = User::where('urlcode', $username)->where('Level_2_status', '1')->where('urlcode_status', '1')->count();
		$totusr_redeem = DB::table('users')
		->select('users.id','users.urlcode','kyc.level' )
		->join('kyc', 'users.id', '=', 'kyc.id')
		->where('users.urlcode', $username)
		->where(function ($query) {
			$query->where('kyc.level', 'Level 1')
			->orWhere('kyc.level', 'Level 2'); })
		->where('users.urlcode_status', '1')
		->get()->count();

		$price_myr = PriceApi::where('crypto', 'BTC')->first()->price;

		$satoshi = 3000 * $totusr_all;
		$satoshimyr = round((0.00001 * $price_myr), 2);
		$totsatoshimyr = round(($satoshimyr * $totusr_all), 2);
		$getredeem = Redeem::where('username', $username)->where('status', 'pending')->count();

		if ($getredeem == 1) {
			$status = 'Pending';
		} 
		else {
			$status = '-';
		}

		$msg = array("url"=>$url, "appurl"=>$appurl,"totalusr"=>$totusr_all, "unredeem"=>$totusr_unredeem, "redeem"=>$totusr_redeem, "satoshi"=>$satoshi, "curr_satoshi"=>$satoshimyr, "satoshimyr"=>$totsatoshimyr, "status"=>$status);

		$datamsg = response()->json(['userRedeem' => $msg]);
	}
	else{
		$msg = array("text"=>"No access.");
		$datamsg = response()->json(['error' => $msg]);
	}
	return $datamsg->content();
}

public function redeem(Request $request){
		//redeem
	$redeem_uid = $request->id;
	$token = $request->token;
	$usrnm = $request->username;
	$systemToken = apiToken($id);

	if($token == $systemToken)
	{
		//TOTAL ALL URLCODE
		$get_all = User::join('kyc', 'kyc.uid', '=', 'user.id')->select('user.*','kyc.*')->where('user.urlcode', $usrnm)->where('kyc.level', 'Level 1')->first();
		$totusr_all = User::join('kyc', 'kyc.uid', '=', 'user.id')->select('user.*','kyc.*')->where('user.urlcode', $usrnm)->where('kyc.level', 'Level 1')->count();


		//TOTAL REDEEM URLCODE
		// $get_limit = User::where('urlcode', $usrnm)->where('urlcode_status', '0')->where('Level_2_status', '1')->first();
		// $totusr_limit = User::where('urlcode', $usrnm)->where('urlcode_status', '0')->where('Level_2_status', '1')->count();

		$get_limit = User::join('kyc', 'kyc.uid', '=', 'user.id')->select('user.*','kyc.*')->where('user.urlcode', $usrnm)->where('user.urlcode_status', '0')->where('kyc.level', 'Level 1')->first();
		$totusr_limit = User::join('kyc', 'kyc.uid', '=', 'user.id')->select('user.*','kyc.*')->where('user.urlcode', $usrnm)->where('user.urlcode_status', '0')->where('kyc.level', 'Level 1')->count();


		//TOTAL REDEEM URLCODE WITH LIMIT
		// $get_url = User::where('urlcode', $usrnm)->where('urlcode_status', '0')->where('Level_2_status', '1')->limit($totusr_limit)->first();
		// $totusr = User::where('urlcode', $usrnm)->where('urlcode_status', '0')->where('Level_2_status', '1')->limit($totusr_limit)->count();

		$get_url = User::join('kyc', 'kyc.uid', '=', 'user.id')->select('user.*','kyc.*')->where('user.urlcode', $usrnm)->where('user.urlcode_status', '0')->where('kyc.level', 'Level 1')->limit($totusr_limit)->first();
		$totusr = User::join('kyc', 'kyc.uid', '=', 'user.id')->select('user.*','kyc.*')->where('user.urlcode', $usrnm)->where('user.urlcode_status', '0')->where('kyc.level', 'Level 1')->limit($totusr_limit)->count();
		$price_myr = PriceApi::where('crypto', 'BTC')->first()->price;
		$satoshi = 3000 * $totusr;
		$satoshimyr = 0.00001 * $price_myr;
		$totsatoshimyr = round(($satoshimyr * $totusr), 2);

		$getbtc = round(($totsatoshimyr / $price_myr), 8);
		// INSERT INTO REDEEM TABLE

		if ($totsatoshimyr >= 10) {
			//INSERT REDEEM
			$insert_redeem = Redeem::create([
				'uid' => $redeem_uid,
				'username' => $usrnm,
				'myr_amount' => $totsatoshimyr,
				'crypto_amount' => $getbtc,
				'rate' => $price_myr,
				'redeem_urlcode' => $totusr,
				'total_urlcode' => $totusr_all
			]);

			$msg = array("text"=>"Successfully submitted your request.");
			$datamsg = response()->json(['sucess' => $msg]);
		} 
		else {
			$msg = array("text"=>"Sorry, the minimum redeem is 10 MYR.");
			$datamsg = response()->json(['error' => $msg]);
		}
	}
	else{
		$msg = array("text"=>"No access.");
		$datamsg = response()->json(['error' => $msg]);
	}
	return $datamsg->content();
}

public function submit_penerima(Request $request)
{
	$id = $request->id;
	$token = $request->token;
	$username = trim($request->username);
	$fullname = $request->fullname;
	$noic = $request->noic;
	$dob = $request->dob;
	$contact_number = $request->contact_number;
	$email = $request->email;
	$relationship = $request->relationship;
	$gender = $request->gender;
	$address1 = $request->address_1;
	$address2 = $request->address_2;
	$postcode = $request->postcode;
	$state = $request->state;
	$city = $request->city;

	$checkuser = User::where('id_colony',$username)->count();

	if($checkuser != 0)
	{
		$datauser = User::where('id_colony',$username)->first();
			//$checksameuser = User::where('id',$id)->first();

		if($datauser->id != $id)
		{
			//$datamsg = response()->json(['status' => 'Exist']);
			$checkhibah = HibahDetail::where('uid',$id)->count();

			if($checkhibah != 0)
			{
				$datahibah = HibahDetail::where('uid',$id)->first();

				$penerimahibah = PenerimaHibah::where('hibah_id',$datahibah->id)->where('id_colony',$datauser->id)->count();

				if($penerimahibah != 0)
				{
					$datamsg = response()->json(['error' => 'Username already tagged.']);
				}
				else
				{

					$counthibah = PenerimaHibah::where('hibah_id',$datahibah->id)->count();
					if($counthibah <= 5)
					{
						$insertdetail = PenerimaHibah::create([
							'hibah_id' => $datahibah->id,
							'id_colony' => $datauser->id,
							'full_name' => $fullname,
							'noic' => $noic,
							'dob' => date("Y-m-d", strtotime($dob)),
							'relationship' => $relationship,
							'gender' => $gender,
							'address1' => $address1,
							'address2' => $address2,
							'postcode' => $postcode,
							'state' => $state,
							'contact_number' => $contact_number,
							'email' => $email,
							'city' => $city
						]);

						$datamsg = response()->json(['success' => 'Nominee has been save successfully.']);
					}
					else
					{
						$datamsg = response()->json(['error' => '5 Nominee Hibah already exceed.']);
					}

				}

			}
			else
			{

				all_getbalance($id, 'BTC');
			    $crypto_balance = WalletAddress::where('uid', $id)->where('crypto', 'BTC')->first()->available_balance;
			    $current_price = PriceApi::where('crypto', 'BTC')->first()->price;
			    $total_balance_myr = number_format(($crypto_balance * $current_price),2,'.','');

			    if($total_balance_myr <= 10)
			    {
			    	$datamsg = response()->json(['error' => 'Insufficient Balance.']);
			    }
			    else
			    {
			    	$inserthibah = HibahDetail::create([
					'uid' => $id,
					'status' => 'pending'
				]);

				$insertdetail = PenerimaHibah::create([
					'hibah_id' => $inserthibah->id,
					'id_colony' => $datauser->id,
					'full_name' => $fullname,
					'noic' => $noic,
					'dob' => date("Y-m-d", strtotime($dob)),
					'relationship' => $relationship,
					'gender' => $gender,
					'address1' => $address1,
					'address2' => $address2,
					'postcode' => $postcode,
					'state' => $state,
					'contact_number' => $contact_number,
					'email' => $email,
					'city' => $city
				]);

				$datamsg = response()->json(['success' => 'Nominee has been save successfully.']);
			    }

				
			}

		}
		else
		{
			$datamsg = response()->json(['error' => 'You cannot use your username as nominee.']);
		}
	}
	else
	{
		$datamsg = response()->json(['error' => 'ID Colony does not exist']);
	}

	return $datamsg->content();

}

public function count_penerima(Request $request)
{
	$id = $request->id;
	$token = $request->token;

	$checkhibah = HibahDetail::where('uid',$id)->count();

	if($checkhibah != 0)
	{

		$datahibah = HibahDetail::where('uid',$id)->first();

		$penerimahibah = PenerimaHibah::where('hibah_id',$datahibah->id)->count();

		$penerimahibah += 1;

		if($penerimahibah == 1)
		{
			$penerimacount = 'First';
		}
		else if($penerimahibah == 2)
		{
			$penerimacount = 'Second';
		}
		else if($penerimahibah == 3)
		{
			$penerimacount = 'Third';
		}
		else if($penerimahibah == 4)
		{
			$penerimacount = 'Fourth';
		}
		else
		{
			$penerimacount = 'Fifth';
		}


		$datamsg = response()->json(['total' => $penerimacount]);


	}
	else
	{
		$datamsg = response()->json(['total' => 'First']);

	}

	return $datamsg->content();


}

public function submit_hibah(Request $request)
{
	
	$id = $request->id;
	$token = $request->token;
	$hibah_id = $request->hibah_id;
	$id_user_1 = $request->id_user_1;
	$id_user_2 = $request->id_user_2;
	$id_user_3 = $request->id_user_3;
	$id_user_4 = $request->id_user_4;
	$id_user_5 = $request->id_user_5;
	$percentage_1 = $request->percentage_1;
	$percentage_2 = $request->percentage_2;
	$percentage_3 = $request->percentage_3;
	$percentage_4 = $request->percentage_4;
	$percentage_5 = $request->percentage_5;

	if($id_user_1 != "")
	{
		PenerimaHibah::where('hibah_id',$hibah_id)->where('id_colony', $id_user_1)->update(["percentage"=>$percentage_1]);
	}

	if($id_user_2 != "")
	{
		PenerimaHibah::where('hibah_id',$hibah_id)->where('id_colony', $id_user_2)->update(["percentage"=>$percentage_2]);
	}


	if($id_user_3 != "")
	{
		PenerimaHibah::where('hibah_id',$hibah_id)->where('id_colony', $id_user_3)->update(["percentage"=>$percentage_3]);
	}


	if($id_user_4 != "")
	{
		PenerimaHibah::where('hibah_id',$hibah_id)->where('id_colony', $id_user_4)->update(["percentage"=>$percentage_4]);
	}


	if($id_user_5 != "")
	{
		PenerimaHibah::where('hibah_id',$hibah_id)->where('id_colony', $id_user_5)->update(["percentage"=>$percentage_5]);
	}


	$hibahstatus = HibahDetail::where('id',$hibah_id)->update(["status"=>'process']);


	$datamsg = response()->json(['success' => 'Detail Hibah have been successfully submitted.']);

	return $datamsg->content();

	
}

	/*public function listhibah(Request $request)
	{
		$id = $request->id;
		$token = $request->token;

		$datahibah = HibahDetail::where('uid',$id)->first();

		$penerimahibah = PenerimaHibah::where('hibah_id',$datahibah->id)->get();
		$no=1;
		foreach($penerimahibah as $penerimahibahs)
		{
		
			$userhibah = User::where('id',$penerimahibahs->id_colony)->first();

			if($no == 1)
			{
				$penerimacount = 'First';
			}
			else if($no == 2)
			{
				$penerimacount = 'Second';
			}
			else if($no == 3)
			{
				$penerimacount = 'Third';
			}
			else if($no  == 4)
			{
				$penerimacount = 'Fourth';
			}
			else
			{
				$penerimacount = 'Fifth';
			}
			

			
			$datahibahs[] = array("id_user"=>$userhibah->id,"id_colony"=>$userhibah->username,"count_receiver"=>$penerimacount,"percentage"=>$penerimahibahs->percentage);
		$no++;

		}

		$datamsg = response()->json(['hibahlist' => $datahibahs,"hibah_id"=> $datahibah->id]);

		return $datamsg->content();



		
	}*/

	public function listhibah(Request $request)
	{
		$id = $request->id;
		$token = $request->token;

		$datahibah = HibahDetail::where('uid',$id)->first();

		$penerimahibah = PenerimaHibah::where('hibah_id',$datahibah->id)->get();
		$no=1;
		foreach($penerimahibah as $penerimahibahs)
		{

			$userhibah = User::where('id',$penerimahibahs->id_colony)->first();

			if($no == 1)
			{
				$penerimacount = 'First';
			}
			else if($no == 2)
			{
				$penerimacount = 'Second';
			}
			else if($no == 3)
			{
				$penerimacount = 'Third';
			}
			else if($no  == 4)
			{
				$penerimacount = 'Fourth';
			}
			else
			{
				$penerimacount = 'Fifth';
			}
			

			
			$datahibahs[] = array("id_user_".$no=>$userhibah->id,"id_colony_".$no=>$userhibah->username,"count_receiver_".$no=>$penerimacount,"percentage_".$no=>$penerimahibahs->percentage);
			$no++;

		}
		
		$flatten = meletop($datahibahs);
		$datamsg = response()->json(['hibahlist' => $flatten ,"hibah_id"=> $datahibah->id]);

		return $datamsg->content();



		
	}


	public function dumpprivatekey(Request $request)
	{
		$id = $request->id;
		$token = $request->token;
		$crypto = $request->crypto;
		$systemToken = apiToken($id);

		if($token == $systemToken)
		{
			$datauser = User::where('id',$id)->first();

			$username = 'usr_'.$datauser->username;

			$data = dumpkey($username,$crypto);

			$dataarr = json_decode($data);

			$datamsg = response()->json(['datakey' => $dataarr]);

			return $datamsg->content();
		}
		else{
			$msg = array("text"=>"No access.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();
		}

	}

	public function balance_coinvata(Request $request)
	{
		$id = $request->id;
		$token = $request->token;
		$crypto = $request->crypto;

		$current_price = PriceAPI::where('crypto', $crypto)->first()->price;
		$walladdress = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->address;
		$crypto_balance = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->available_balance;
		$crypto_balance_myr = round(($current_price * $crypto_balance), 2);

		if ($crypto == 'XLM') {
			$walladdress = xlm_idinfo('2', "account_id");
			$crypto_balance = xlm_getbalance_pod($id);
			$crypto_balance_myr = round(($current_price * $crypto_balance), 2);
		}

                //START CALCULATION            
		$price_myr = round($current_price, 2);

                //NETWORK FEE RM0.5
		$network_fee_myr = Setting::where('id', '1')->first()->network_fee;
		$network_fee_crypto = round(($network_fee_myr / $price_myr), 5);

                //END CALCULATION
		$total_fee_crypto = $network_fee_crypto ;
		$total_fee_myr = round(($current_price * $total_fee_crypto), 2);
		if($crypto == 'XRP')
		{
			if($crypto_balance > 20)
			{
				$max_send_crypto = round((($crypto_balance - 20) - $total_fee_crypto), 5);
				$max_send_myr = $crypto_balance_myr - $total_fee_myr;
			}
			else
			{
				$max_send_crypto = 0;
				$max_send_myr = $crypto_balance_myr - $total_fee_myr;
			}
		}
		else
		{
			$max_send_crypto = round(($crypto_balance - $total_fee_crypto), 5);
			$max_send_myr = $crypto_balance_myr - $total_fee_myr;

		}

		if($crypto == 'ETH'){
			$gas = gaspriceData();
			$decode = json_decode($gas);
			$converter = new \Bezhanov\Ethereum\Converter();
			$normal = $converter->toWei($decode->normal, 'gwei');
			$fast = $converter->toWei($decode->fast, 'gwei');

			        //Get Data ETH
			$current_price = PriceAPI::where('name', 'Ethereum')->first()->price;
			$withdraw_commision = Setting::first()->withdrawal_commission;
			$fee = $withdraw_commision / $current_price;      
			$gasL = '100000';
			$gasP = $fast;
			$fee = $withdraw_commision / $current_price;
			$address_fee = WalletAddress::where('uid', 888)->where('crypto','ETH')->first()->address;
			$check_users = User::where('id', $id)->first();

		            //Check Address ETH (From)
		            //Address User
			$address_from = WalletAddress::where('uid',$id)->where('crypto', 'ETH')->first()->address;
			$converter = new \Bezhanov\Ethereum\Converter();
		            //$balance_from = Ethereum::eth_getBalance($address_from, 'latest', TRUE);
			$balance_from =0;
			$balance_fromWei = $converter->fromWei($balance_from, 'ether');

			if ($balance_fromWei != 0) {
				$estFee = $converter->fromWei($gasP * $gasL, 'ether');
				$bal = $balance_fromWei - (($estFee * 2) + $fee);

				if (strpos($bal, '-') !== false) {
					$bal = 0;
				}
			} 
			else {
				$estFee = $converter->fromWei($gasP * $gasL, 'ether');
				$bal = 0;
			}

			$total_fee_crypto = ($estFee * 2) + $fee;
			$total_fee_myr = round(($current_price * $total_fee_crypto), 2);
			$crypto_balance_myr = round(($current_price * $crypto_balance), 2);
			$max_send_crypto = round(($crypto_balance - $total_fee_crypto),5);
			$max_send_myr = round(($crypto_balance_myr - $total_fee_myr),2);
		}

		if ($crypto_balance <= $total_fee_crypto) {
			$max_send_crypto = 0;
			$max_send_myr = 0;
		} 
		else {
			$max_send_crypto = $max_send_crypto;
			$max_send_myr = $max_send_myr;
		}

		if($crypto == "DOGE")
		{
			$msg = array("crypto_amount"=>number_format($max_send_crypto,2));
		}
		else
		{
			$msg = array("crypto_amount"=>number_format($max_send_crypto,5));
		}

		$datamsg = response()->json([
			'cryptoBalance' => $msg
		]);

		return $datamsg->content();
	}


	public function checkhibah(Request $request)
	{
		$id = $request->id;
		$token = $request->token;

		$datahibah = HibahDetail::where('uid',$id)->where('status','process')->orWhere('status','completed')->count();

		if($datahibah != 0)
		{	
			$infohibah = HibahDetail::where('uid',$id)->where('status','process')->orWhere('status','completed')->first();

			$penerimahibah = PenerimaHibah::where('hibah_id',$infohibah->id)->get();
			$no=1;
			foreach($penerimahibah as $penerimahibahs)
			{

				$userhibah = User::where('id',$penerimahibahs->id_colony)->first();

				if($no == 1)
				{
					$penerimacount = 'First';
				}
				else if($no == 2)
				{
					$penerimacount = 'Second';
				}
				else if($no == 3)
				{
					$penerimacount = 'Third';
				}
				else if($no  == 4)
				{
					$penerimacount = 'Fourth';
				}
				else
				{
					$penerimacount = 'Fifth';
				}



				$datahibahs[] = array("id_user_".$no=>$userhibah->id,"id_colony_".$no=>$userhibah->username,"count_receiver_".$no=>$penerimacount,"percentage_".$no=>$penerimahibahs->percentage);
				$no++;

			}

			$flatten = meletop($datahibahs);
			$datamsg = response()->json(['status' => 'true','status_hibah' => $infohibah->status,'hibahlist' => $flatten]);


		}
		else
		{
			$datamsg = response()->json(['status' => 'false']);
		}		
		

		return $datamsg->content();



		
	}

	public function uploadpersonal(Request $request)
	{

		$id = $request->id;
		$touseremail = $request->email;
		$fullname = $request->fullname;
		$target = $request->target;

		$gg1= explode('_',$target);
		$gg = end($gg1);
		$id = current(explode('-',$gg)); 
		$ez1=explode('-',$target);
		$ez=end($ez1);
		$check = current(explode('.',$ez));
		$target_path = "user_".$id."/".$target; 


		$updt1 = User::where('id', $id)
		->update([
			'picture' => $target_path
		]);

		$userData = Kyc::where('id',$id)->first();

		/*send email notification to user */

		
		echo '{"success":{"text":"Your profile picture has been updated."}}';
	}
	public function enterredeem(Request $request) {
		$id = $request->id;
		$token = $request->token;
		$entercode = $request->entercode;
		$systemToken = apiToken($id);

		if($token == $systemToken)
		{
			$referral = str_rot13($entercode);
			$check = User::where('username',$referral)->count();

			if($check == 1){
				User::where('id',$id)
				->update([
					"urlcode"=>$referral,
					"urlcode_status"=>1
				]);
				$msg = array("text"=>"Successfully referral.");
				$datamsg = response()->json([
					'success' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$msg = array("text"=>"This Referral Code is not an user referral code.");
				$datamsg = response()->json([
					'error' => $msg
				]);
				return $datamsg->content();
			}

		}
		else{
			$msg = array("text"=>"No access.");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}
	}
	public function checknotification(Request $request) {
		$id = $request->id;
		$token = $request->token;
		$systemToken = apiToken($id);
		if($token == $systemToken)
		{
			$count = Notification::where('uid',$id)->where('read',"0")->orderBy('id','desc')->count();
			if($count=='' || $count==0){
				$msg = array("text"=>"No notification found.");
				$datamsg = response()->json([
					'error' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$msg = array("text"=>$count);
				$datamsg = response()->json([
					'success' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("text"=>"No access.");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}
	}
	public function updatenotification(Request $request) {
		$id = $request->id;
		$token = $request->token;
		$notiid = $request->notiid;
		$systemToken = apiToken($id);
		if($token == $systemToken)
		{   
			$notification = Notification::where('id',$notiid)->first();
			if($notification->read == '1'){
				$msg = array("text"=>"No access.");
				$datamsg = response()->json([
					'error' => $msg
				]);
				return $datamsg->content();
			}
			else{
				Notification::where('id',$notiid)
				->update([
					'read' => '1'
				]);

				$msg = array("text"=>"Success.");
				$datamsg = response()->json([
					'success' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("text"=>"No access.");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}
	}

	public function updateallnotify(Request $request)
	{
		$id = $request->id;
		$token = $request->token;
		$systemToken = apiToken($id);
		if($token == $systemToken)
		{  
			$updatenotify = Notification::where('uid',$id)->where('read','0')
			->update([
				'read' => '1'
			]);

			$msg = array("text"=>"");
			$datamsg = response()->json([
				'success' => $msg
			]);
			return $datamsg->content();
		}
		else{
			$msg = array("text"=>"No access.");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}

	}
public function jompay(Request $request){
	$msg = array("text"=>"This service is currently under maintenance.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

}


	public function jompay1(Request $request) {

		$id = $request->id;
		$token = $request->token;
		$biller = $request->biller;
		$ref1 = $request->ref1;
		$ref2 = $request->ref2;
		$myr = $request->myr;
		$crypto = $request->crypto;

		$systemToken = apiToken($id);

		if($token == $systemToken)
		{

		
			$totalFee = 0;
			$priceapi = PriceAPI::where('crypto', $crypto)->first();
			$current_price = $priceapi->price_pinkexcjompay;
			$amount = round($myr/$current_price,8);
			$totalMyr = $amount * $current_price;
			$myr_amount = $myr;

			$check_verification = Kyc::where('uid', $id)->first();
			$check_user_level2 = User::where('id', $id)->first();
			if($check_verification->level == "Level 2")
			{
				$level1 = '1';
				$level2 = '1';
			}
			else
			{
				$level1 = '1';
				$level2 = '0';
			}

			if($check_verification == null || $check_verification->status == "pending for review" || $check_verification->status == "pending for reupload" || $check_verification->status == "uncompleted" || $check_verification->status == "pending review for reupload" || $check_verification->status == "rejected" || $check_verification->status == "reupdate")
			{
				$msg = array("text"=>"Sorry, Please upgrade to level 2");
				$datamsg = response()->json([
					'error' => $msg
				]);

				return $datamsg->content();

			}elseif($check_verification->status == 'completed' && $level2 == "0")
			{

				$msg = array("text"=>"Sorry, Please upgrade to level 2");
				$datamsg = response()->json([
					'error' => $msg
				]);

				return $datamsg->content();
			}
			elseif($crypto=="LIFE"||$crypto=="XLM"){
				$msg = array("text"=>$crypto." is unavailable for jompay until further notice.");
				$datamsg = response()->json([
					'error' => $msg
				]);

				return $datamsg->content();
			}
			else
			{

				$get_address = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first();
				$percentage_sell = Setting::where('id', '1')->first()->sell_comission;

              //if null
				if ($get_address == null) {

					$msg = array("text"=>"Sorry, Please add wallet ".$crypto);
					$datamsg = response()->json([
						'error' => $msg
					]);

					return $datamsg->content();

				} 
				else
				{

            //data from database
					$current_price = $priceapi->price;
					$change = $priceapi->percentage;
					$name = $priceapi->name;
					$walladdress = $get_address->address;

					$current_price = $priceapi->price;

					$price_myr = round($current_price, 2);
					$crypto_balance = round(($get_address->available_balance), 5);
					$crypto_balance_myr = round(($current_price * $crypto_balance), 2);

	        			//NETWORK FEE RM0.5
					$network_fee_myr = Setting::where('id', '1')->first()->network_fee;
					$network_fee_crypto = round(($network_fee_myr / $price_myr), 5);
	        			//WITHDRAWAL COMMISSION RM0.5
					$sell_comm_myr = Setting::where('id', '1')->first()->jompay_comission;
					$comm = number_format(($price_myr * $sell_comm_myr),2,'.','');
                    				$total_comm = number_format(($price_myr - $comm),2,'.',''); // Currrent Rate
                    				$amount_comm = number_format(($myr / $total_comm),8,'.','');
					$amount_comm = number_format(($amount - $amount_comm),8,'.','');

					$sell_comm_crypto = number_format(($sell_comm_myr / $price_myr), 8);

	        			//re-calculate crypto amount
					$total_fee_crypto = $network_fee_crypto + $sell_comm_crypto;
					$crypto_amount = round(($myr_amount / $current_price), 8);

					$totalAll = $crypto_amount + $total_fee_crypto;
					$total_amount = $crypto_balance -  $totalAll ;

					if($crypto != 'ETH')
					{
						all_getbalance($id, $crypto);
						$crypto_balance = $get_address->available_balance;
					}
					else if($crypto == "XRP")
					{
						$current_price = $priceapi->price;

						$price_myr = round($current_price, 2);
						$crypto_balance = round(($get_address->available_balance), 5);
						$crypto_balance_myr = round(($current_price * $crypto_balance), 2);

	        			//NETWORK FEE RM0.5
						$network_fee_myr = Setting::where('id', '1')->first()->network_fee;
						$network_fee_crypto = round(($network_fee_myr / $price_myr), 5);
	        			//WITHDRAWAL COMMISSION RM0.5
						$sell_comm_myr = Setting::where('id', '1')->first()->jompay_commission;
						$comm = number_format(($price_myr * $sell_comm_myr),2,'.','');
                    				$total_comm = number_format(($price_myr - $comm),2,'.',''); // Currrent Rate
                    				$amount_comm = number_format(($myr / $total_comm),8,'.','');
						$amount_comm = number_format(($amount - $amount_comm),8,'.','');
						$sell_comm_crypto = round(($sell_comm_myr / $price_myr), 5);


	        			//re-calculate crypto amount
						$total_fee_crypto = $network_fee_crypto + $sell_comm_crypto;
						$crypto_amount = round(($myr_amount / $current_price), 8);

						$totalAll = $crypto_amount + $total_fee_crypto;
						$total_amount = $crypto_balance -  $totalAll ;

					}

					else
					{
						$converter = new \Bezhanov\Ethereum\Converter();
						$price = Gasprice::where('id',1)->first()->rapid;
						$wallet_balance = Ethereum::eth_getBalance($get_address->address,'latest',TRUE);
						$wallet_balance = number_format($wallet_balance, 0, '', '');
						$crypto_balance = $converter->fromWei($wallet_balance, 'ether');
						$gasL = '100000';
						if($price == 0 || $price ==''){$price=50;}
						$gasP = $converter->toWei($price, 'gwei');

						if($crypto_balance != 0)
						{
							$estFee = $converter->fromWei($gasP*$gasL, 'ether');

							$bal = $crypto_balance - $estFee;

							if (strpos($bal, '-') !== false) {
								$bal = 0;
							}
						}
						else
						{
							$estFee = $converter->fromWei($gasP*$gasL, 'ether');
							$bal = 0;
						}

						$totalFee = $estFee;
						$totalBal = $totalFee + $amount;
						$max = $crypto_balance - $totalFee;
						if (strpos($max, '-') !== false) {$max = 0;}

						if($totalBal > $crypto_balance)
						{
							$msg = array("text"=>"Insufficient balance. You can only withdraw $max");
							$datamsg = response()->json([
								'error' => $msg
							]);

							return $datamsg->content();
						}
					}

					$crypto_balance_myr = round(($current_price * $crypto_balance), 2);
					$label = $get_address->label;

            //CALCULATION
					if($crypto=='DOGE'){
						$sell_price = round( ($current_price - ($current_price * $percentage_sell) ), 6);
					}
					else{
						$sell_price = round( ($current_price - ($current_price * $percentage_sell) ), 2);
					}
					$new_crypto_amount = round($myr_amount/$sell_price,5);
					$afterbal = round(($crypto_balance - $new_crypto_amount),8);

					$trans_no = 'MPODS'.$check_user_level2->username;


            //CHECK TABLE LIMITATION
					$sqljplimit = Jompay_limit::where('uid', $id)->orderBy('id', 'desc')->first();
					if($sqljplimit == '' || $sqljplimit == null){
						$fullname = $check_verification->name;
						$limit_amount = '1000';
						$limit_usage = '0';
						$limit_balance = '1000';
						$limit_datenow = date("Y-m-d H:i:s");
						$datenow = date("Y-m-d H:i:s");
						$lbalance2 = $limit_balance - $myr_amount;
					}
					else{
						$fullname = $sqljplimit->fullname;
						$limit_amount = $sqljplimit->limit_amount;
						$limit_usage = $sqljplimit->limit_usage;
						$limit_balance = $sqljplimit->limit_balance;
						$limit_datenow = $sqljplimit->daterecord;
						$datenow = date("Y-m-d H:i:s");
						$lbalance2 = $limit_balance - $myr_amount;
					}

					$count = Jompay::where('uid', $id)->where('crypto',$crypto)->count();

					$lmyr = 10;
					if($id==26441){$lmyr = 2;}
					if($id==680){$lmyr = 2;}

					if($count != 0 && $count != ''){

						$getdate =Jompay::where('uid', $id)->where('crypto',$crypto)->orderBy('id', 'desc')->first()->created_at;
						$newtimestamp = strtotime($getdate.'+10 minutes');

						$currentdate = date('Y-m-d H:i:s');
						$tmp = strtotime($currentdate);

						$test = ($newtimestamp - $tmp)/60;
						$newdate = date('Y-m-d H:i:s', $newtimestamp);

						$new = number_format($test,0);

						if($currentdate <= $newdate ){
							$msg = array("text"=>"Please wait for $new minutes to make a new jompay order for $crypto. Thank you.");
							$datamsg = response()->json([
								'error' => $msg
							]);

							return $datamsg->content();

						}
					}

					if($crypto == 'XRP' && ($total_amount <= 20) ){
						$msg = array("text"=>'You must leave 20 XRP reserve in the account you are sending from.');
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();

					}
					elseif( (round($crypto_balance,5) == 0) || ($amount > round($crypto_balance,5))){

						$msg = array("text"=>"Insufficient Balance");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();

					}elseif ($myr_amount < $lmyr) {

						$msg = array("text"=>"Must be more than 10 MYR");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();

					} elseif ($myr_amount > 1000) {

						$msg = array("text"=>"Sorry, The maximum limit for jompay is 1000 MYR per day.  ");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();

					} elseif ($limit_balance == 0) {

						$msg = array("text"=>"You reach the limitation for today");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();

					} elseif ($myr_amount > $limit_balance) {

						$msg = array("text"=>"Sorry, your limit balance is " . $limit_balance . " MYR");
						$datamsg = response()->json([
							'error' => $msg
						]);

						return $datamsg->content();

					} else {}
        //MOVE TO WALLET ADMIN
				//$move = move_crypto($crypto,$label,'usr_admin',$amount);
					$move = move_crypto_comment($crypto, $label,'usr_jompay', $amount,'sell');

					if ($move == null) {
					//INSERT JOMPAY (to record the failed)
						$insert_verify = Jompay::create([
							'uid'=>$id,
							'fullname'=>$fullname,
							'biller_code'=>$biller,
							'ref1'=>$ref1,
							'ref2'=>$ref2,
							'currentbal'=>$crypto_balance,
							'crypto_amount'=>$amount,
							'afterbal'=>$afterbal,
							'myr_amount'=>$myr_amount,
							'txid' => 'fail',
							'status'=> 'failed',
							'trans_no'=>$trans_no,
							'rate'=>$sell_price,
							'current_rate'=>$current_price,
							'crypto'=>$crypto,
							'fee'=>$amount_comm,
							'gas_fee'=>$totalFee,
							'crypto_release'=> '0'
						]);
						$msg = array("text"=>"#3 Request failed. Please try again");
						$datamsg = response()->json([
							'error' => $msg
						]);
						return $datamsg->content();

					}else{
				//UPDATE AVAILABLE BALANCE
						$available_bal = $crypto_balance - $amount;
						$update_balance = WalletAddress::where('uid', $id)->where('crypto', $crypto)
						->update([
							'available_balance' => $available_bal
						]);
				//INSERT JOMPAY
						$insert_verify = Jompay::create([
							'uid'=>$id,
							'fullname'=>$fullname,
							'biller_code'=>$biller,
							'ref1'=>$ref1,
							'ref2'=>$ref2,
							'currentbal'=>$crypto_balance,
							'crypto_amount'=>$amount,
							'afterbal'=>$afterbal,
							'myr_amount'=>$myr_amount,
							'txid' => $move,
							'status'=> 'process',
							'trans_no'=>$trans_no,
							'rate'=>$sell_price,
							'current_rate'=>$current_price,
							'crypto'=>$crypto,
							'fee'=>$amount_comm,
							'gas_fee'=>$totalFee,
							'crypto_release'=> '1'
						]);
                        //insert into limitation table
						if (date("Y-m-d", strtotime($limit_datenow)) == date("Y-m-d", strtotime($datenow))) {
						//INSERT LIMITATION
							$insert_limit = Jompay_limit::create([
								'uid' => $id,
								'fullname' => $fullname,
								'limit_level' => 'Level 2',
								'limit_amount' => '1000',
								'daterecord' => $datenow,
								'limit_usage' => $myr_amount,
								'limit_balance' => $lbalance2
							]);
						} else {
							$newbalance = 1000 - $myr_amount;
						//INSERT LIMITATION
							$insert_limit = Jompay_limit::create([
								'uid' => $id,
								'fullname' => $fullname,
								'limit_level' => 'Level 2',
								'limit_amount' => '1000',
								'daterecord' => $datenow,
								'limit_usage' => $myr_amount,
								'limit_balance' => $newbalance
							]);
						}

						if($insert_limit){
							$username = User::where('id', $id)->first()->username;
	          //notification
							$content = "Dear " . $username . ", Kindly note that you have made a JomPAY request for the $biller bill with the amount of RM $myr equivalent to $amount $crypto on the time of the request. Do allow up to 48 hours for settlement and any notifications will be sent to your registered email. Thank you for your cooperation and have a great day ahead.
";
							$sendnotify = Notification::create([
								'uid' => $id,
								'title' => 'Instant Jompay Request',
								'content' => $content,
								'read' => '0'
							]);
						}
                        ////
						$msg = array("text"=>"Your request successfully submitted. Your request is in queue and will be process on 48 hours in working days.");
						$datamsg = response()->json([
							'success' => $msg
						]);

						return $datamsg->content();

					}
				}
			}
		}
		else
		{
			$msg = array("text"=>"No access");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();

		}

	}

	public function currentjompay(Request $request)
	{
		$id = $request->id;
		$token = $request->token;
		$crypto = $request->crypto;
		$max = 0;
		$systemToken = apiToken($id);

		if($token == $systemToken)
		{
			//Bank Name

			$price2 = PriceAPI::where('crypto',$crypto)->first()->price;

			$rate = Setting::where('id',1)->first()->jompay_comission;

			$price_instantsell = ($price2-($price2 * $rate));

			if($crypto == 'DOGE'){
				$price_instantsell = round($price_instantsell,5);
			}
			else{
				$price_instantsell = round($price_instantsell,2);
			}

			if($crypto == 'ETH'){
				$get_address = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->address;
				$converter = new \Bezhanov\Ethereum\Converter();
				$price = Gasprice::where('id',1)->first()->rapid;
				$wallet_balance = Ethereum::eth_getBalance($get_address,'latest',TRUE);
				$value = $converter->fromWei($wallet_balance, 'ether');
				$gasL = '100000';
				if($price == 0 || $price ==''){$price=50;}
				$gasP = $converter->toWei($price, 'gwei');

				$estFee = $converter->fromWei($gasP*$gasL, 'ether');
				if($value == ''){$value=0;}
				$totalFee = $estFee;
				$totalBal = $totalFee;

				$max = number_format(($value - $totalFee),8);
				if (strpos($max, '-') !== false) {
					$max = 0;
				}
			}
			else{

				if($crypto == 'BTC'){btc_getbalance($id);}
				else if($crypto == 'LTC'){ltc_getbalance($id);}
				else if($crypto == 'DOGE'){doge_getbalance($id);}
				else if($crypto == 'DASH'){dash_getbalance($id);}
				else if($crypto == 'BCH'){bch_getbalance($id);}
				$wallet = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first();
				$btc_balance = $wallet->available_balance;
				$currentjompay = $btc_balance - ($btc_balance * $rate);
				$max = $currentjompay;}
				$msg = array('price_jompay' => $price_instantsell,
					'max' => number_format($max,5),
					'price_curr' => $price2);

				$datamsg = response()->json([
					'success' => $msg
				]);

				return $datamsg->content();
			}
			else
			{
				$msg = array("text"=>"No access");
				$datamsg = response()->json([
					'error' => $msg
				]);

				return $datamsg->content();
			}
		}
public function listjompay(Request $request)
{
	$id = $request->id;
	$token = $request->token;
	$crypto = $request->crypto;

	$systemToken = apiToken($id);
	
	if($token == $systemToken)
	{
if($crypto=='XLM'){
			$pinkexcbuyc = StellarPinkexcsell::where('uid', $id)->count();
			if($pinkexcbuyc)
			{
				$pinkexcbuy = StellarPinkexcsell::where('uid', $id)->orderBy('id', 'desc')->get();
				$datamsg = response()->json([
				'sellData' => $pinkexcbuy
				]);
				return $datamsg->content();
			}
			else{
				$msg = array("text"=>"You do not have transaction yet.");
				$datamsg = response()->json([
				'error' => $msg
				]);
				return $datamsg->content();
			}
		}
else{
	$pinkexcsell = Jompay::where('uid', $id)->where('crypto',$crypto)->count();
		if($pinkexcsell != 0)
		{
			$pinkexcbuy = Jompay::where('uid', $id)->where('crypto',$crypto)->orderBy('id', 'desc')->get();
			$datamsg = response()->json([
				'jompayData' => $pinkexcbuy
			]);
			return $datamsg->content();
		}
		else
		{
			$msg = array("text"=>"You do not have transaction yet.");
			$datamsg = response()->json([
				'error' => $msg
			]);
			return $datamsg->content();
		}
}
	}
	else
	{
		$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);
		return $datamsg->content();
	}
}

public function status(Request $request){
	$id = $request->id;
	$token = $request->token;
	$crypto = $request->crypto;

	$systemToken = apiToken($id);
	
	if($token == $systemToken)
	{
		$status_btc = '0';
		$status_ltc= '0';
		$status_bch= '0';
		$status_doge= '0';
		$status_dash= '0';
		$status_xlm= '0';
		$status_eth= '0';
		$status_xrp= '0';
		$status_life= '0';
		$btc = WalletAddress::where('uid', $id)->where('crypto', 'BTC')->first();

		if($btc){
			$status_btc = '1';
		}

		$bch = WalletAddress::where('uid', $id)->where('crypto', 'BCH')->first();
		if($bch){
			$status_bch = '1';
		}

		$ltc = WalletAddress::where('uid', $id)->where('crypto', 'LTC')->first();
		if($ltc){
			$status_ltc = '1';
		}

		$dash = WalletAddress::where('uid', $id)->where('crypto', 'DASH')->first();
		if($dash){   
			$status_dash = '1';
		}

		$doge = WalletAddress::where('uid', $id)->where('crypto', 'DOGE')->first();
		if($doge){
			$status_doge = '1';
		}


		$xlm = WalletAddress::where('uid', $id)->where('crypto', 'XLM')->first();
		if($xlm){
			$status_xlm = '1';
		}

		$xrp = WalletAddress::where('uid', $id)->where('crypto', 'XRP')->first();
		if($xrp){
			$status_xrp = '1';
		}

		$eth = WalletAddress::where('uid', $id)->where('crypto', 'ETH')->first();
		if($eth){
			$status_eth = '1';
		}

		$life = WalletAddress::where('uid', $id)->where('crypto', 'LIFE')->first();
		if($life){
			$status_life = '1';
		}

		$msg = array(
			"status_btc"=>$status_btc,
			"status_ltc"=>$status_ltc,
			"status_bch"=>$status_bch,
			"status_doge"=>$status_doge,
			"status_dash"=>$status_dash,
			"status_xlm"=>$status_xlm,
			"status_eth"=>$status_eth,
			"status_xrp"=>$status_xrp,
			"status_life"=>$status_life);
		$datamsg = response()->json([
			'balance' => $msg
		]);

		return $datamsg->content();

	}
	else
	{
		$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);
		return $datamsg->content();
	}

}
public function atmcheck(Request $request){
		$phone = $request->phone;

		$user = Kyc::where('phone',$phone)->count();
		//dd($user);
		if($user!=0){$msg = array("text"=>"Access");
		$datamsg = response()->json([
			'success' => $msg
		]);
		return $datamsg->content();
}
		else{$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);
		return $datamsg->content();
}

	}
public function get_label_test(Request $request)
	{
		$label = $request->address;
		$crypto = $request->crypto;
		$convertFrom = $request->from;
		$convertTo = $request->to;
		$rate = $request-rate;

		//$coinvata_price = coinvata_price($convertFrom,$convertTo,$rate);
		//$data_coinvata = json_decode($coinvata_price->content());
		//$displayprice = round($data_coinvata->displayprice,8);
		//$displayprice = round($displayprice,5);
		//$check_label_in_node = get_label_crypto2($crypto, $label);
		$check_label_in_node = getbalance($crypto, $label);
		//$check_label_in_node = btc_getbalance('26441');
		//$btc = WalletAddress::where('uid', '26441')->where('crypto', 'LTC')->first();

		//$check_label_in_node = $btc->available_balance;
		//$network_fee_myr = getestimatefee($crypto);
		//$network_fee_myr = str_replace("\n", '', $network_fee_myr);
	
		//dd($network_fee_myr);
		//$check_label_in_node = str_replace("\n", '', $check_label_in_node);
		//$var = bcdiv($check_label_in_node, 1, 5);
	
		$msg = array("node"=>$check_label_in_node);
     		$datamsg = response()->json([
     			'error' => $msg
     		]);

		/*
		if($check_label_in_node == ''){
			$msg = array("text"=>$check_label_in_node);
     			$datamsg = response()->json([
     			'error' => $msg
     			]);
		}
		else{
			$msg = array("text"=>$check_label_in_node);
     			$datamsg = response()->json([
     			'success' => $msg
     			]);

		}*/

     		return $datamsg->content();
	}

public function datelevel2 (Request $request){
	$reload_number = $request->reload_number;
	$getdate =Anypaytrans::where('reload_number', $reload_number)->orderBy('id', 'desc')->first()->created_at;
	//dd($getdate);
        					if($getdate != null && $getdate != ''){

        						//$getdate =Pinkexcsell::where('uid', $id)->where('crypto',$crypto)->orderBy('id', 'desc')->first()->created_at;
        						$newtimestamp = strtotime($getdate.'+2 minutes');

        						$currentdate = date('Y-m-d H:i:s');
        						$tmp = strtotime($currentdate);

        						$test = ($newtimestamp - $tmp)/60;
        						$newdate = date('Y-m-d H:i:s', $newtimestamp);

        						$new = number_format($test,0);

        						if($currentdate <= $newdate ){
        							$msg = array("text"=>"Please wait for $new minutes to make a new sell order for $crypto. Thank you.");
        							$datamsg = response()->json([
        								'error' => $msg
        							]);

        							return $datamsg->content();

        						}
						dd('test');
        					}

}

##################Stellar####################

public function check_balanceXLMuser2(Request $request){
	
	$id_user = $request->id;
	$num = $request->amount;
	$accname = $request->account_name;
	$bankname = $request->bankname;
	$memo = '2;'.microtime();
	$token = $request->token;		

	$systemToken = apiToken($id_user);

	if($token == $systemToken)
	{ 
		$rows_price = PriceApi::where('crypto','XLM')->first(); 
		$rate_pricemyr = $rows_price->price;

		$price2 = round($rate_pricemyr,2);
		$price_instantbuy = ($price2 * 0.05) + $price2;
		$price_instantsell =  ( $price2-($price2 * 0.05));

		$rowsx = Limitation::where('uid',$id_user)->orderBY('id','DESC')->first();

		$rows = StellarInfo::where('id',2)->first(); 

    //horizon
		$horizon = $rows->str_horizon.'accounts/';

		$setting = Setting::where('id',1)->first(); 

		$total_fee =  settings('network_fee_xlm');	 	

		$amount = number_format($num, 7, '.', '');

		$fix_limit = $rows->fix_limit;
		$balance_amt = $amount + $total_fee;  
		$stellar_address = $rows->seed_id;
		$acc_id = $rows->account_id; 


		$rate = $price_instantsell;
		$rate1 = round($rate,2);
		$myrAmount = round(($rate1 * $amount),0); 

		$lbalance2 = $rowsx->limit_balance - $myrAmount;

		$getbal = xlm_getbalance_pod($id_user);

		$wait_str = 0;  

		$rowsy = StellarPod::where('str_status','pending')->where('source_id',$id_user)->first();
		if(isset($rowsy)){$wait_str = 1;}  

		$rowsw = StellarPod::where('str_status','pending')->where('destination_id',$id_user)->first();
		if(isset($rowsw)){$wait_str = 1;} 

		$rowss = User::where('xlm_block','1')->where('id',$id_user)->first();
		if(isset($rowss)){$wait_str = 1;} 

		if($wait_str == 1){$check_err = "Sorry, wait for the second. Please try again later";}
		elseif($myrAmount > $rowsx->limit_amount){$check_err = "Sorry, your cash out amount is exceed the limitation per month. Limit cash out per month is ".$rowsx->limit_amount." MYR";}
		elseif($myrAmount > $rowsx->limit_balance){$check_err = "Sorry, limit balance is now is RM ".$rowsx->limit_balance;}
		elseif($myrAmount < 100 && $id_user!='26441'){$check_err = "Sorry, The minimum amount for Instant Sell is 100 MYR";}
		elseif($lbalance2 < 0){$check_err = "Sorry, your balance cash out amount is exceed the limitation per month. Limit cash out per month is 100 000 MYR.";}
		elseif($getbal < $balance_amt){$check_err = "Sorry, your stellar is not enough to process the Instant Sell. You must left at least '$total_fee' stellar for transaction fee.";}
		elseif($rowsx->limit_balance == 0){$check_err = "You are reach the limitation in this month.";}
		else{$check_err = '';}

		/* check lumen sender */ 

		$ids = $id_user; 
		$rows = StellarPod::where('str_status','!=','cancel')->where(function ($query) use ($ids) {
			$query->where('source_id', $ids)
			->orWhere('destination_id', $ids);
		})->orderBY('id','desc')->first();


		if($check_err!='')
		{
			$arr = array( 
				"stellar_address"=> $stellar_address,
				"account_id"=> $acc_id,
				"msj"=> $check_err); 
		}
		elseif(!isset($rows)){
			$arr = array( 
				"stellar_address"=> $stellar_address,
				"account_id"=> $acc_id,
				"msj"=> "Sorry, your stellar is not enough to process the Instant Sell.");

		}
		elseif($rows->destination_id==$ids)
		{
			$balance =  $amount + $total_fee;
        if($rows->balance_destination <= $balance) // lumen not enough for user in database
        {
        	$arr = array( 
        		"stellar_address"=> $stellar_address,
        		"account_id"=> $acc_id,
        		"msj"=> "Sorry, your stellar is not enough to process the Instant Sell.");

        }else{  // send lumen success
            $e = $this->save_instantsellXLM2($amount,$memo,$id_user,$bankname,$accname,$token);

        	$arr = array( 
        		"stellar_address"=> $stellar_address,
        		"account_id"=> $acc_id,
        		"msj"=> "None error stellar POD.");
        }
    }
    elseif($rows->source_id==$ids)
    {
    	$balance =  $amount + $total_fee;
        if($rows->balance_source <= $balance) // lumen not enough for user in database
        {
        	$arr = array( 
        		"stellar_address"=> $stellar_address,
        		"account_id"=> $acc_id,
        		"msj"=> "Sorry, your stellar is not enough to process the Instant Sell.");

        }else{	 // send lumen success
          $e = $this->save_instantsellXLM2($amount,$memo,$id_user,$bankname,$accname,$token);

        	$arr = array( 
        		"stellar_address"=> $stellar_address,
        		"account_id"=> $acc_id,
        		"msj"=> "None error stellar POD.");
        }
    }else{	  
    	if($rows->source_id!=$ids && $rows->destination_id!=$ids) 
    	{
    		$arr = array( 
    			"stellar_address"=> $stellar_address,
    			"account_id"=> $acc_id,
    			"msj"=> "Sorry, your stellar amount not available.");
    	}
    	else{
                $e = $this->save_instantsellXLM2($amount,$memo,$id_user,$bankname,$accname,$token);

    		$arr = array( 
    			"stellar_address"=> $stellar_address,
    			"account_id"=> $acc_id,
    			"msj"=> "None error stellar POD.");
    	}
    } 
    

    $datamsg = response()->json([
    	'success' => $arr
    ]);

    return $datamsg->content();        

}
else
{
	$msg = array("text"=>"No access");
	$datamsg = response()->json([
		'error' => $msg
	]);

	return $datamsg->content();
}

}

public function save_instantsellXLM2($amount,$memo,$id_user,$bankname,$accname,$token)
{  
	$id_user = $id_user;
	$amount = $amount;
	$accname = $accname;
	$bankname = $bankname;
	$memo = $memo;
	$token = $token;

	$systemToken = apiToken($id_user);

	if($token == $systemToken)
	{ 
		$rows_price = PriceApi::where('crypto','XLM')->first(); 

		$limit_xlm = Limitation::where('uid', $id_user)->where('category','sell')->orderby('id','desc')->first();
		$user_xlm = User::where('id', $id_user)->first();

		$rate_pricemyr = $rows_price->price;

		$price2 = round($rate_pricemyr,2);

		$price_instantbuy = ($price2 * 0.05) + $price2;
		$price_instantsell =  ( $price2-($price2 * 0.05));

		$amount = $amount;

		$memo = $memo;  
		$source_id = $id_user;
		$datenow = date("Y-m-d H:i:s");

		$limit_datenow = $limit_xlm->daterecord;

		if($limit_xlm->resident=='yes'){$limit_amount = '100000';}else{$limit_amount = '50000';}
		$limit_balance = $limit_xlm->limit_balance;
		$fullname = $user_xlm->username;    

		$rate = $price_instantsell;
		$rate1 = round($rate,2);
		$myrAmount = round(($rate1 * $amount),0);
		$limit_usage = $myrAmount;		   
		$lbalance2 = $limit_balance - $myrAmount;

		$rows = StellarInfo::where('id',1)->first(); 
		$admin_accountID = $rows->account_id;

		$rows2 = StellarInfo::where('id',2)->first();

		$user_accountID = $rows2->account_id;
		$total_fee =  settings('network_fee_xlm');

		$rowsx = User::where('id',$source_id)->first();

		$username_sender = $rowsx->username;

		$rowsy = Kyc::where('uid',$source_id)->first();
		$bankname = $bankname;
		$accname =  $accname;

		if(isset($rowsy)){

			if($rowsy->bankname1==$bankname){$banknumber = $rowsy->banknumber1;}
			if($rowsy->bankname2==$bankname){$banknumber = $rowsy->banknumber2;}

		}else{$banknumber = '';}

		$refnum = 'MPODS'.$username_sender;

		$bal_sourC = xlm_getbalance_pod($source_id);
		$bal_sour = $bal_sourC - $amount - $total_fee;
		$bal_sourA = round($bal_sour,7);

    // $sql1 = new StellarPinkexcsell;
    // $sql1->uid = $source_id;
    // $sql1->username = $username_sender;
    // $sql1->currentbal = $bal_sourC;
    // $sql1->crypto_amount = $amount;
    // $sql1->afterbal = $bal_sourA;
    // $sql1->myr_amount = $myrAmount;
    // $sql1->paymethod = 'ATM';
    // $sql1->bankname = $bankname;
    // $sql1->accnum = $banknumber;
    // $sql1->accname = $accname;
    // $sql1->trans_no = $refnum;
       // $sql1->status = 'unpaid'; 
    // $sql1->recipient = '';
    // $sql1->rate = $rate1;
    // $sql1->current_rate = $rate_pricemyr;
    // $sql1->memo = $memo;
    // $sql1->save();

		$sql1 = StellarPinkexcsell::create([
			'uid' => $source_id,
			'username' => $username_sender,
			'currentbal' => $bal_sourC,
			'crypto_amount' => $amount,
			'afterbal' => $bal_sourA,
			'myr_amount' => $myrAmount,
			'paymethod' => 'Online Banking',
			'bankname'=> $bankname,
			'accnum' => $banknumber,
			'accname'=>$accname,
			'trans_no'=>$refnum,
			'status' => 'process',
			'recipient' => '',
			'rate' => $rate1,
			'current_rate' => $rate_pricemyr,
			'memo' => $memo
		]);

        $id_new = $sql1->id;
        
        $trans_no = refforbuy('mobile', 'XLM', $id_new);

        $updt = StellarPinkexcsell::where('id',$id_new)->update([
                    'trans_no'=>$trans_no
                ]);


    // $ins_pod = new StellarPod;
    // $ins_pod->type = 'instant sell';
    // $ins_pod->pod_id = $id_new;
    // $ins_pod->source_id = $source_id;
    // $ins_pod->balance_source = $bal_sourA;
    // $ins_pod->destination_id = 'admin';
    // $ins_pod->balance_destination = '0';
    // $ins_pod->send_token = $amount;
    // $ins_pod->memo = $memo;
    // $ins_pod->txtmemo = '';
    // $ins_pod->status = 'send';
    // $ins_pod->str_status = 'pending';
    // $ins_pod->str_transaction_id = ''; 
    // $ins_pod->save();

		$ins_pod = StellarPod::create([
			"type" => 'instant sell',
			"pod_id" => $id_new,
			"source_id" => $source_id,
			"balance_source" => $bal_sourA,
			"destination_id" => 'admin',
			"balance_destination" => '0',
			"send_token" => $amount,
			"myr_amount" => $myrAmount,
			"rate" => $rate1,
			"current_price" => $price2,
			"memo" => $memo,
			"txtmemo" => '',
			"status" => 'send',
			"str_status" => 'pending',
			"str_transaction_id" => '',
			"probBy" => 'mobile' 
		]);

		$sets = Limitation::where('uid',$source_id)->orderBy('id','desc')->first();
		$limit_level = $sets->limit_level;
		$resident = $sets->resident;

		if( date("Y-m",strtotime($limit_datenow)) == date("Y-m",strtotime($datenow))){$limit_use = $limit_usage;}
		else{$limit_use='0.0000';}

    // $sql2 = new Limitation;
    // $sql2->uid = $source_id;
    // $sql2->fullname = $fullname;
    // $sql2->limit_level = $limit_level;
    // $sql2->limit_amount = $limit_amount;
    // $sql2->daterecord = $datenow;
    // $sql2->limit_usage = $limit_use;
    // $sql2->resident = $resident;
    // $sql2->limit_balance = $lbalance2;
    // $sql2->category = 'sell';
    // $sql2->save();

		$sql2 = Limitation::create([
			"uid" => $source_id,
			"fullname" => $fullname,
			"limit_level" => $limit_level,
			"limit_amount" => $limit_amount,
			"daterecord" => $datenow,
			"limit_usage" => $limit_use,
			"resident" => $resident,
			"limit_balance" => $lbalance2,
			"category" => 'sell'
		]);

        $send = xlmtoadmin($amount,$memo);
	//dd($send);
    //notification
		$price = PriceApi::where('crypto','XLM')->first()->price;
		$username = User::where('id',$source_id)->first()->username;
		$myrAmount = round(($price * $amount),2);
		$xlm_amnt = round($amount,5);
		$content = "Dear ".$username.", you have requested for instant sell XLM with amount ".$xlm_amnt." XLM ( ".$myrAmount." MYR ). The process will take within 24 until 48 hours on working days. We will notify and send an email to you after the process is complete. Thank you for your business. ";
		$sql3 = new Notification;
		$sql3->uid = $source_id;
		$sql3->title = 'Instant Sell Request';
		$sql3->content = $content;
		$sql3->read = '0';
		$sql3->save();

		$msg = array("text"=>"Process completed");
		$datamsg = response()->json([
			'success' => $msg
		]);
		return $datamsg->content();
	}
	else
	{
		$msg = array("text"=>"No access");
		$datamsg = response()->json([
			'error' => $msg
		]);

		return $datamsg->content();
	}

}
public function any_operator()
{
	$state = Anypayop::all();

	$datamsg = response()->json([
		'anypay' => $state
	]);

	return $datamsg->content();
}
public function test_balance(){
	//$try = anypaybalance();
	//dd($try <= 300);
	//$move = move_crypto_comment('BTC', 'usr_nurhafiz','usr_admin', '0.00005','sell');
	$move = anypaytopupstatus("MCOLD27");
	dd($move);
}
public function anypay(Request $request){
	$msg = array("text"=>"This service is currently under maintenance.");
			$datamsg = response()->json([
				'error' => $msg
			]);

			return $datamsg->content();

}


public function anypay1(Request $request)
{
    $id = $request->id;
    $token = $request->token;
    $operator_name = $request->operator_name;
    $crypto = $request->crypto;
    $reload_number = $request->reload_number;
    $myr_amount = $request->myr_amount;
    $systemToken = apiToken($id);

    $reload_number = correct_number($reload_number);

    if (substr($reload_number, 0, 1) != '0') { 
      $msg = array("text" => "Invalid number. The entered number must start with 0, no country code needed.");
      $datamsg = response()->json([
        'error' => $msg
      ]);
      return $datamsg->content();
    }

    if($operator_name == 'Steam Wallet PIN'||$operator_name == 'Razer Gold Pin'){
        $msg = array("text" => "Not available for this operators yet.");
        $datamsg = response()->json([
            'error' => $msg
        ]);

        return $datamsg->content();
    }

    $getdate = Anypaytrans::where('reload_number', $reload_number)->orderBy('id', 'desc')->first()->created_at;
    if ($getdate != null && $getdate != '') {
    $newtimestamp = strtotime($getdate . '+2 minutes');
    $currentdate = date('Y-m-d H:i:s');
    $tmp = strtotime($currentdate);
    $test = ($newtimestamp - $tmp) / 60;
    $newdate = date('Y-m-d H:i:s', $newtimestamp);
    $new = number_format($test, 0);

    if ($currentdate <= $newdate) {
      $msg = array("text" => "Please wait for $new minutes to make a new reload with $reload_number,phone number. Thank you.");
      $datamsg = response()->json([
        'error' => $msg
      ]);
      return $datamsg->content();
     }
    }

    if ($token == $systemToken) {

        if ($crypto == 'ETH') {
            eth_getbalance($id);
		$eth_list = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first();
            $get_address = $eth_list->address;
            $converter = new \Bezhanov\Ethereum\Converter();
            $price = Gasprice::where('id', 1)->first()->rapid;
            $wallet_balance = Ethereum::eth_getBalance($get_address, 'latest', TRUE);
            $value = $converter->fromWei($wallet_balance, 'ether');
            $gasL = '100000';
            if ($price == 0 || $price == '') {
                $price = 50;
            }
            $gasP = $converter->toWei($price, 'gwei');

            $estFee = $converter->fromWei($gasP * $gasL, 'ether');
            if ($value == '') {
                $value = 0;
            }
            $totalFee = $estFee;
            $totalBal = $totalFee;

            $max = number_format(($value - $totalFee), 8);
            // if (strpos($max, '-') !== false) {
            //     $max = 0;
            // }
            $curr_price = PriceApi::where('crypto',$crypto)->first()->price;
            $crypto_amount = round($myr_amount/$curr_price,5);
		
            if ($max < $crypto_amount) { 
                $msg = array("text" => "You do not have enough ETH to complete this transaction.");
                $datamsg = response()->json([
                                'error' => $msg
                            ]);
                return $datamsg->content();
            }
		$crypto_balance = $value;
        }
        elseif($crypto == 'LIFE' || $crypto == 'XLM'){
            $msg = array("text" => "$crypto is not open for this service.");
                $datamsg = response()->json([
                                'error' => $msg
                            ]);
                return $datamsg->content();
        }
        else{
            $balance = all_getbalance($id, $crypto);
            $curr_price = PriceApi::where('crypto',$crypto)->first()->price;
            $crypto_amount = round($myr_amount/$curr_price,5);

            if($balance < $crypto_amount) { 
                $msg = array("text" => "You do not have enough $crypto to complete this transaction.");
                $datamsg = response()->json([
                                'error' => $msg
                            ]);
                return $datamsg->content();
            }
		$crypto_balance = $balance;
        }

            $operator_code = anypay_code($operator_name);
            $label = WalletAddress::where('uid', $id)->where('crypto', $crypto)->first()->label;

            $response = anypay_transaction($id, $label, $crypto,$crypto_balance, $myr_amount, $reload_number, $operator_code, 'mobile');

            if($response == 'error'){
                $msg = array("text" => "Sorry, the transaction failed. Please contact our support.");
                $datamsg = response()->json([
                                'error' => $msg
                            ]);

                return $datamsg->content();
            }
            else{
                $msg = array("text" => "The top-up was a success. Please wait about 60 seconds before checking your balance.");
                $datamsg = response()->json([
                                'success' => $msg
                            ]);

                return $datamsg->content();
            }
    } else {
        $msg = array("text" => "No access");
        $datamsg = response()->json([
            'error' => $msg
        ]);

        return $datamsg->content();
    }
}

public function listanypay(Request $request)
{
    $id = $request->id;
    $token = $request->token;
    $systemToken = apiToken($id);

    if ($token == $systemToken) {
        $anypaylist = Anypaytrans::where('uid', $id)->orderBy('id', 'desc')->get();
        if (isset($anypaylist)) {

            $datamsg = response()->json([
                'anypayData' => $anypaylist
            ]);

            return $datamsg->content();
        } else {

            $msg = array("text" => "You do not have transaction yet.");
            $datamsg = response()->json([
                'error' => $msg
            ]);

            return $datamsg->content();
        }
    } else {
        $msg = array("text" => "No access");
        $datamsg = response()->json([
            'error' => $msg
        ]);

        return $datamsg->content();
    }
}



	}