<?php

use App\General;
use App\User;
use App\WalletAddress;
use App\Verification;
use App\PriceApi;
use App\StellarInfo;
use App\StellarPod;
use App\DuplicateXLM;
use Carbon\Carbon;
use App\Setting;
use App\Gasprice;
use Jcsofts\LaravelEthereum\Facade\Ethereum as Ethereum;
use Jcsofts\LaravelEthereum\Lib\EthereumTransaction;
use CashaddrConverter;
use App\Gasprice;


function urlss()
{
    
        return realpath(base_path().'/../assets');
   
}

///////////////////////////////////////////////////////////////
/// IMAGECHECK/////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

function ImageCheck($ext){
    if($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'bnp'){
        $ext = "";
    }
    return $ext;
}




///////////////////////////////////////////////////////////////
/// SETTINGS /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

function send_email_verify($to, $subject, $name, $message,$hash){
  $setting = Setting::first();

  $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
  $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $template = $setting->template_email_verify;
  $mm = str_replace("{{name}}",$name,$template);
  $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
  $url = str_replace("{{url}}",$setting->url.'verify/email/'.$hash , $supportemail);
  $message = str_replace("{{message}}",$message,$url);
  mail($to, $subject, $message, $headers);

}

function send_reset_password($to, $subject, $name, $message,$hash){
  $setting = Setting::first();

  $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
  $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $template = $setting->template_reset_password;
  $mm = str_replace("{{name}}",$name,$template);
  $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
  $url = str_replace("{{url}}",$setting->url.'password/reset/'.$hash , $supportemail);
  $message = str_replace("{{message}}",$message,$url);
  mail($to, $subject, $message, $headers);

}

///////////////////////////////////////////////////////////////
/// INFO DASHBOARD ADMIN /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////


function dashboardInfo($crypto,$id,$label_admin,$label_fees,$label_coinvata)
{  
	 $settings = Setting::first(); 
     $price_api = PriceApi::where('crypto',$crypto)->first(); 
	 $rate_pricemyr = $price_api->price; 

	 $price2 = round($rate_pricemyr,2);
	 if($crypto=='xlm'){ $network_fee = $settings->network_fee_xlm; }
	 else{ $network_fee = round($settings->network_fee/$price2,8); }
	 
    $btc_balance = getbalance('BTC',$label_admin);
    $btc_myrbalance = $btc_balance * PriceApi::where('crypto','BTC')->first()->price;
    $btc_feebalance = getbalance('BTC',$label_fees);
	$btc_feebalance_myr = $btc_feebalance * PriceApi::where('crypto','BTC')->first()->price;
	$btcC_balance = getbalance('BTC',$label_coinvata);
    $btcC_myrbalance = $btcC_balance * PriceApi::where('crypto','BTC')->first()->price;
    
    $bch_balance = getbalance('BCH',$label_admin);
    $bch_myrbalance = $bch_balance * PriceApi::where('crypto','BCH')->first()->price;
    $bch_feebalance = getbalance('BCH',$label_fees);
	$bch_feebalance_myr = $bch_feebalance * PriceApi::where('crypto','BCH')->first()->price;
	$bchC_balance = getbalance('BCH',$label_coinvata);
    $bchC_myrbalance = $bchC_balance * PriceApi::where('crypto','BCH')->first()->price;
    
    $ltc_balance = getbalance('LTC',$label_admin);
    $ltc_myrbalance = $ltc_balance * PriceApi::where('crypto','LTC')->first()->price;
    $ltc_feebalance = getbalance('LTC',$label_fees);
	$ltc_feebalance_myr = $ltc_feebalance * PriceApi::where('crypto','LTC')->first()->price;
	$ltcC_balance = getbalance('LTC',$label_coinvata);
    $ltcC_myrbalance = $ltcC_balance * PriceApi::where('crypto','LTC')->first()->price;
    
    $doge_balance = getbalance('DOGE',$label_admin);
    $doge_myrbalance = $doge_balance * PriceApi::where('crypto','DOGE')->first()->price;
    $doge_feebalance = getbalance('DOGE',$label_admin);
	$doge_feebalance_myr = $doge_feebalance * PriceApi::where('crypto','DOGE')->first()->price;
	$dogeC_balance = getbalance('DOGE',$label_coinvata);
    $dogeC_myrbalance = $dogeC_balance * PriceApi::where('crypto','DOGE')->first()->price;
    
    $dash_balance = getbalance('DASH',$label_admin);
    $dash_myrbalance = $dash_balance * PriceApi::where('crypto','DASH')->first()->price;
    $dash_feebalance = getbalance('DASH',$label_fees);
	$dash_feebalance_myr = $dash_feebalance * PriceApi::where('crypto','DASH')->first()->price;
	$dashC_balance = getbalance('DASH',$label_coinvata);
    $dashC_myrbalance = $dashC_balance * PriceApi::where('crypto','DASH')->first()->price;
	
    $xlm_balance = xlm_getbalance(1);
    $xlm_myrbalance = xlm_getbalance_myr(1);
    $xlm_feebalance = xlm_getbalance(2);
	$xlm_feebalance_myr = xlm_getbalance_myr(2);
	$xlmC_balance = 0;
    $xlmC_myrbalance = 0;
	
    $eth_balance = getbalance('ETH',$label_admin);
    $eth_myrbalance = $eth_balance * PriceApi::where('crypto','ETH')->first()->price;
    $eth_feebalance = getbalance('ETH',$label_fees);
	$eth_feebalance_myr = $eth_feebalance * PriceApi::where('crypto','ETH')->first()->price;
	$ethC_balance = getbalance('ETH',$label_coinvata);
    $ethC_myrbalance = $ethC_balance * PriceApi::where('crypto','ETH')->first()->price;
	
    $life_balance = getbalance('LIFE',$label_admin);
    $life_myrbalance = $life_balance * PriceApi::where('crypto','LIFE')->first()->price;
    $life_feebalance = getbalance('ETH',$label_fees);
	$life_feebalance_myr = $life_feebalance * PriceApi::where('crypto','LIFE')->first()->price;
	$lifeC_balance = 0;
    $lifeC_myrbalance = $ethC_balance * PriceApi::where('crypto','LIFE')->first()->price;
	
     //$xrp_getbalance = xrp_getbalance($id); 
	
    $totalAll = $btc_myrbalance + $bch_myrbalance + $ltc_myrbalance + $doge_myrbalance + $dash_myrbalance + $xlm_myrbalance + $eth_myrbalance + $life_myrbalance;
     	
	$total_users = User::all()->count();  
	 $dup_hash_xlm = DuplicateXLM::count();  
	 $totalXLM = StellarInfo::where('id',2)->first()->balance;
	 $admin_address = WalletAddress::where('label',$label_admin)->where('crypto',$crypto)->first();
	 
	if($crypto=='LIFE'){
	 $adminC_address = '';
	 }else{
	 $adminC_address = WalletAddress::where('label',$label_coinvata)->where('crypto',$crypto)->first();	 
	 }
	 
	if($crypto == 'bch'||$crypto == 'BCH'){ 
	$user_balance = $bch_balance;
	$userC_balance = $bchC_balance;
	$url = 'admin.home';
	}	
	elseif($crypto == 'ltc'||$crypto == 'LTC'){
	$user_balance = $ltc_balance;
	$userC_balance = $ltcC_balance;
	$url = 'admin.home';
	}	
	elseif($crypto == 'dash'||$crypto == 'DASH'){
	$user_balance = $dash_balance;
	$userC_balance = $dashC_balance;
	$url = 'admin.home';
	}	
	elseif($crypto == 'doge'||$crypto == 'DOGE'){
	$user_balance = $doge_balance;
	$userC_balance = $dogeC_balance;
	$url = 'admin.home';
	}
	elseif($crypto == 'life'||$crypto == 'LIFE'){
	$user_balance = $life_balance;
	$userC_balance = $lifeC_balance;
	$url = 'admin.home';
	}
	elseif($crypto == 'eth'||$crypto == 'ETH'){
	$user_balance = $eth_balance;
	$userC_balance = $ethC_balance;
	$url = 'admin.home';
	}	
	elseif($crypto == 'doge'||$crypto == 'DOGE'){
	$user_balance = $doge_balance;
	$userC_balance = $dogeC_balance;
	$url = 'admin.home';
	}	
	elseif($crypto == 'xlm'||$crypto == 'XLM'){
	$user_balance = xlm_getbalance(1);
	$userC_balance = 0;
	$url = 'admin.home_xlm';
	}	
	elseif($crypto == 'btc'||$crypto == 'BTC'){
	$user_balance = $btc_balance;
	$userC_balance = $btcC_balance;
	$url = 'admin.home';
	}	
	else{
	$user_balance = 0;
	$userC_balance = 0;
	$url = 'admin.home';
	}	 
		
	 
	$data = response()->json([
	'user_balance' => $user_balance,
	'userC_balance' => $userC_balance,
	'totalAll' => $totalAll,
	   'btc_balance' => $btc_balance,
	   'btcC_balance' => $btcC_balance,
	   'bch_balance' => $bch_balance,
	   'bchC_balance' => $bchC_balance,
	   'ltc_balance' => $ltc_balance,
	   'ltcC_balance' => $ltcC_balance,
	   'doge_balance' => $doge_balance,
	   'dogeC_balance' => $dogeC_balance,
	   'dash_balance' => $dash_balance,
	   'dashC_balance' => $dashC_balance,
	   'xlm_balance' => $xlm_balance,
	   'xlmC_balance' => $xlmC_balance,
	   'eth_balance' => $eth_balance,
	   'ethC_balance' => $ethC_balance,
	   'life_balance' => $life_balance,
	   'lifeC_balance' => $lifeC_balance,
	   'btc_myrbalance' => $btc_myrbalance,
	   'btcC_myrbalance' => $btcC_myrbalance,
	   'bch_myrbalance' => $bch_myrbalance,
	   'bchC_myrbalance' => $bchC_myrbalance,
	   'ltc_myrbalance' => $ltc_myrbalance,
	   'ltcC_myrbalance' => $ltcC_myrbalance,
	   'doge_myrbalance' => $doge_myrbalance,
	   'dogeC_myrbalance' => $dogeC_myrbalance,
	   'dash_myrbalance' => $dash_myrbalance,
	   'dashC_myrbalance' => $dashC_myrbalance,
	   'xlm_myrbalance' => $xlm_myrbalance,
	   'xlmC_myrbalance' => $xlmC_myrbalance,
	   'eth_myrbalance' => $eth_myrbalance,
	   'ethC_myrbalance' => $ethC_myrbalance,
	   'life_myrbalance' => $life_myrbalance,
	   'lifeC_myrbalance' => $lifeC_myrbalance,
	   'btc_feebalance' => $btc_feebalance,
	   'bch_feebalance' => $bch_feebalance,
	   'ltc_feebalance' => $ltc_feebalance,
	   'doge_feebalance' => $doge_feebalance,
	   'dash_feebalance' => $dash_feebalance,
	   'xlm_feebalance' => $xlm_feebalance,
	   'eth_feebalance' => $eth_feebalance,
	   'life_feebalance' => $life_feebalance,
	   'btc_feebalance_myr' => $btc_feebalance_myr,
	   'bch_feebalance_myr' => $bch_feebalance_myr,
	   'ltc_feebalance_myr' => $ltc_feebalance_myr,
	   'doge_feebalance_myr' => $doge_feebalance_myr,
	   'dash_feebalance_myr' => $dash_feebalance_myr,
	   'xlm_feebalance_myr' => $xlm_feebalance_myr,
	   'eth_feebalance_myr' => $eth_feebalance_myr,
	   'life_feebalance_myr' => $life_feebalance_myr,
	   'total_users' => $total_users,
	   'network_fee' => $network_fee,
	   'totalXLM' => $totalXLM,
	   'dup_hash_xlm' => $dup_hash_xlm,
		'url' => $url,
		'adminC_address' => $adminC_address,
		'admin_address' => $admin_address
	]);
	
	return $data->content();
}

/////////////////////////////////////////////////////////////////////
///  SET URL CRYPTO                     ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getinfoBTC() {

    $URL_INFO = PriceApi::where('crypto', 'BTC')->first();
    $URL_IP = $URL_INFO->ip_getinfo;
    return $URL_IP;
}

function getinfoBCH() {
    $URL_INFO = PriceApi::where('crypto', 'BCH')->first();
    $URL_IP = $URL_INFO->ip_getinfo;
    return $URL_IP;
}

function getinfoLTC() {
    $URL_INFO = PriceApi::where('crypto', 'LTC')->first();
    $URL_IP = $URL_INFO->ip_getinfo;
    return $URL_IP;
}

function getinfoDASH() {
    $URL_INFO = PriceApi::where('crypto', 'DASH')->first();
    $URL_IP = $URL_INFO->ip_getinfo;
    return $URL_IP;
}

function getinfoDOGE() {
    $URL_INFO = PriceApi::where('crypto', 'DOGE')->first();
    $URL_IP = $URL_INFO->ip_getinfo;
    return $URL_IP;
}

function getinfoXLM() {
    $URL_INFO = "";
    return $URL_IP;
}

function getinfoETH() {
    $URL_INFO = "";
    return $URL_IP;
}

function getinfoLIFE() {
    $URL_INFO = "";
    return $URL_IP;
}

/////////////////////////////////////////////////////////////////////
///  1.BTC                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////



function btc_getbalance($id) {

      $uid = WalletAddress::where('uid', $id)->where('crypto','BTC')->first();


    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    //UPDATE BALANCE INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->available_balance = $wallet_balance;
    //$user->save();

   $updt = WalletAddress::where('uid', $id)->where('crypto', 'BTC')
            ->update([
                 'available_balance' => $wallet_balance
            ]);
      




    return $wallet_balance;
}

function btc_getbalance_myr($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','BTC')->first();
    $current_price = PriceAPI::where('name', 'Bitcoin')->first();

    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    $myr_balance = $current_price->price * $wallet_balance;

    return $myr_balance;
}

function btc_address($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','BTC')->first();
    //GET ADDRESS 
    $post = [
        'id' => 3,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $wallet_address = curl_exec($ch);

    curl_close($ch);


    //UPDATE ADDRESS INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->address = $wallet_address;
    //$user->save();

    return $wallet_address;
}

function btc_generate_address($username)
{
  $post = [
    'id' => 2,
    'label' => 'usr_'.$username
];

$ch = curl_init(getinfoBTC());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

// execute!
$addr = curl_exec($ch);

// close the connection, release resources used
curl_close($ch);

$user = User::where('username',$username)->first();

$address = WalletAddress::create([
    'uid' => $user->id,
    'label' => 'usr_'.$username,
    'address' => $addr,
    'available_balance' => '0.00000000',
    'crypto' => 'BTC',
]);
}

function btc_admin_get_fees(){ 
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  
    return $balance;
}

function btc_admin_get_fees_myr(){ 
  
  $current_price = PriceAPI::where('name','Bitcoin')->first(); 
  
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  $myr_balance = $current_price->price * $balance;
  
    return $myr_balance;
}

function btc_getransactionall() {
     
    
    //GET all transaction
    $post = [
        'id' => 8
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $transaction;

}
function btc_getransaction($label) {
     
    
    //GET user transaction
    $post = [
        'id' => 11,
        'label' => $label
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $info_transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $info_transaction;

}

function btc_moveuser($userlabel,$getuserlabel,$cryptoseller){
  
   $post = [
   'id' => 15,
   'label' => $userlabel,
   'label2' => $getuserlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoBTC());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   return $result;
}
 

function btc_sellrelease($userlabel,$cryptoseller){
  
   $post = [
   'id' => 10,
   'label' => $userlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoBTC());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   return $result;
}
 
function btc_admin_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_admin'
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $balance = curl_exec($ch);

    curl_close($ch); 
    return $balance;
  
}

function btc_transfer_admintouser($useraddress,$cryptoamount) {
     
    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_admin',
    'address' => $useraddress,
    'amount' => $cryptoamount
  ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);
  
  return $wallet_balance;

}

function btc_check_address($address) {
     
   //GET ADDRESS 
    $post = [
        'id' => 14,
        'address' => $address
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       $wallet_address= curl_exec($ch);
       
    curl_close($ch);
     
    return $wallet_address;
}


/////////////////////////////////////////////////////////////////////
/// 2. BCH                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function bch_getbalance($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','BCH')->first();

    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    //UPDATE BALANCE INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->available_balance = $wallet_balance;
    //$user->save();
   $updt = WalletAddress::where('uid', $id)->where('crypto', 'BCH')
            ->update([
                 'available_balance' => $wallet_balance
            ]);


    return $wallet_balance;
}

function bch_getbalance_myr($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','BCH')->first();
    $current_price = PriceAPI::where('name', 'Bitcoin Cash')->first();

    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    $myr_balance = $current_price->price * $wallet_balance;

    return $myr_balance;
}

function bch_address($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','BCH')->first();
    //GET ADDRESS 
    $post = [
        'id' => 3,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $wallet_address = curl_exec($ch);

    curl_close($ch);


    //UPDATE ADDRESS INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->address = $wallet_address;
    //$user->save();

    return $wallet_address;
}


//new
function bch_check_address($address) {
     
   //GET ADDRESS 
    $post = [
        'id' => 14,
        'address' => $address
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       $wallet_address= curl_exec($ch);
       
    curl_close($ch);
     
    return $wallet_address;
}

function bch_admin_get_fees(){ 
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  
    return $balance;
}

function bch_admin_get_fees_myr(){ 
  
  $current_price = PriceAPI::where('name','Bitcoin Cash')->first(); 
  
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  $myr_balance = $current_price->price * $balance;
  
    return $myr_balance;
}

function bch_getransactionall() {
     
    
    //GET all transaction
    $post = [
        'id' => 8
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $transaction;

}

function bch_getransaction($label) {
     
    //GET user transaction
    $post = [
        'id' => 11,
        'label' => $label
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $info_transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $info_transaction;

}

function bch_sellrelease($userlabel,$cryptoseller){
  
   $post = [
   'id' => 10,
   'label' => $userlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoBCH());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   
   return $result;
}
 
function bch_admin_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_admin'
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $balance = curl_exec($ch);

    curl_close($ch);
    
        
       return $balance;
  
}

function bch_transfer_admintouser($useraddress,$cryptoamount) { 

    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_admin',
    'address' => $useraddress,
    'amount' => $cryptoamount
  ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);
  
  return $wallet_balance;

}

//end new














/////////////////////////////////////////////////////////////////////
/// 3. LTC                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function ltc_getbalance($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','LTC')->first();

    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    //UPDATE BALANCE INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->available_balance = $wallet_balance;
    //$user->save();

	   $updt = WalletAddress::where('uid', $id)->where('crypto', 'LTC')
            ->update([
                 'available_balance' => $wallet_balance
            ]);
	

    return $wallet_balance;
}

function ltc_getbalance_myr($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','LTC')->first();
    $current_price = PriceAPI::where('name', 'Litecoin')->first();

    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    $myr_balance = $current_price->price * $wallet_balance;

    return $myr_balance;
}

function ltc_address($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','LTC')->first();
    //GET ADDRESS 
    $post = [
        'id' => 3,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $wallet_address = curl_exec($ch);

    curl_close($ch);


    //UPDATE ADDRESS INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->address = $wallet_address;
    //$user->save();

    return $wallet_address;
}


//new
function ltc_check_address($address) {
     
   //GET ADDRESS 
    $post = [
        'id' => 14,
        'address' => $address
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       $wallet_address= curl_exec($ch);
       
    curl_close($ch);
     
    return $wallet_address;
}

function ltc_admin_get_fees(){ 
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  
    return $balance;
}

function ltc_admin_get_fees_myr(){ 
  
  $current_price = PriceAPI::where('name','Litecoin')->first(); 
  
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  $myr_balance = $current_price->price * $balance;
  
    return $myr_balance;
}

function ltc_getransactionall() {
     
    
    //GET all transaction
    $post = [
        'id' => 8
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $transaction;

}

function ltc_getransaction($label) {
     
    
    //GET user transaction
    $post = [
        'id' => 11,
        'label' => $label
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $info_transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $info_transaction;

}

function ltc_sellrelease($userlabel,$cryptoseller){
  
   $post = [
   'id' => 10,
   'label' => $userlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoLTC());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   return $result;
}
 
function ltc_admin_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_admin'
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $balance = curl_exec($ch);

    curl_close($ch);
    
        
       return $balance;
  
}

function ltc_transfer_admintouser($useraddress,$cryptoamount) { 

    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_admin',
    'address' => $useraddress,
    'amount' => $cryptoamount
  ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);
  
  return $wallet_balance;

}


//end new











/////////////////////////////////////////////////////////////////////
/// 4. DASH                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function dash_getbalance($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','DASH')->first();

    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    //UPDATE BALANCE INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->available_balance = $wallet_balance;
    //$user->save();
   $updt = WalletAddress::where('uid', $id)->where('crypto', 'DASH')
            ->update([
                 'available_balance' => $wallet_balance
            ]);


    return $wallet_balance;
}

function dash_getbalance_myr($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','DASH')->first();
    $current_price = PriceAPI::where('name', 'Dash')->first();

    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    $myr_balance = $current_price->price * $wallet_balance;

    return $myr_balance;
}

function dash_address($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','DASH')->first();
    //GET ADDRESS 
    $post = [
        'id' => 3,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $wallet_address = curl_exec($ch);

    curl_close($ch);


    //UPDATE ADDRESS INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->address = $wallet_address;
    //$user->save();

    return $wallet_address;
}

//new
 
function dash_check_address($address) {
     
   //GET ADDRESS 
    $post = [
        'id' => 14,
        'address' => $address
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       $wallet_address= curl_exec($ch);
       
    curl_close($ch);
     
    return $wallet_address;
}

function dash_admin_get_fees(){ 
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  
    return $balance;
}

function dash_admin_get_fees_myr(){ 
  
  $current_price = PriceAPI::where('name','Dash')->first(); 
  
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  $myr_balance = $current_price->price * $balance;
  
    return $myr_balance;
}

function dash_getransactionall() {
     
    
    //GET all transaction
    $post = [
        'id' => 8
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $transaction;

}

function dash_getransaction($label) {
     
    //GET user transaction
    $post = [
        'id' => 11,
        'label' => $label
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $info_transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $info_transaction;

}

function dash_sellrelease($userlabel,$cryptoseller){
  
   $post = [
   'id' => 10,
   'label' => $userlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoDASH());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   return $result;
}
 
function dash_admin_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_admin'
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $balance = curl_exec($ch);

    curl_close($ch);
    
        
       return $balance;
  
}

function dash_transfer_admintouser($useraddress,$cryptoamount) { 

    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_admin',
    'address' => $useraddress,
    'amount' => $cryptoamount
  ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);
  
  return $wallet_balance;

}




//end new
/////////////////////////////////////////////////////////////////////
/// 5. DOGE                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function doge_getbalance($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','DOGE')->first();

    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    //UPDATE BALANCE INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->available_balance = $wallet_balance;
    //$user->save();
	
	   $updt = WalletAddress::where('uid', $id)->where('crypto', 'DOGE')
            ->update([
                 'available_balance' => $wallet_balance
            ]);


    return $wallet_balance;
}

function doge_getbalance_myr($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','DOGE')->first();
    $current_price = PriceAPI::where('name', 'Dogecoin')->first();

    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);


    $myr_balance = $current_price->price * $wallet_balance;

    return $myr_balance;
}

function doge_address($id) {

    $uid = WalletAddress::where('uid', $id)->where('crypto','DOGE')->first();
    //GET ADDRESS 
    $post = [
        'id' => 3,
        'label' => $uid->label
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $wallet_address = curl_exec($ch);

    curl_close($ch);


    //UPDATE ADDRESS INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->address = $wallet_address;
    //$user->save();

    return $wallet_address;
}
//new 

function doge_check_address($address) {
     
   //GET ADDRESS 
    $post = [
        'id' => 14,
        'address' => $address
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       $wallet_address= curl_exec($ch);
       
    curl_close($ch);
     
    return $wallet_address;
}

function doge_admin_get_fees(){ 
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  
    return $balance;
}

function doge_admin_get_fees_myr(){ 
  
  $current_price = PriceAPI::where('name','Dogecoin')->first(); 
  
    $post = [
    'id' => 4,
    'label' => 'usr_pinkexc_fees'
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  $myr_balance = $current_price->price * $balance;
  
    return $myr_balance;
}

function doge_getransactionall() {
     
    
    //GET all transaction
    $post = [
        'id' => 8
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $transaction;

}
function doge_getransaction($label) {
     
    
    //GET user transaction
    $post = [
        'id' => 11,
        'label' => $label
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $info_transaction = json_decode($bit_trans);  
  //dd($info_transaction); 
       return $info_transaction;

}

function doge_sellrelease($userlabel,$cryptoseller){
  
   $post = [
   'id' => 10,
   'label' => $userlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoDOGE());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   return $result;
}
 
function doge_admin_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_admin'
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $balance = curl_exec($ch);

    curl_close($ch);
    
        
       return $balance;
  
}

function doge_transfer_admintouser($useraddress,$cryptoamount) { 

    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_admin',
    'address' => $useraddress,
    'amount' => $cryptoamount
  ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $wallet_balance = curl_exec($ch);

    curl_close($ch);
  
  return $wallet_balance;

}
//end new
/////////////////////////////////////////////////////////////////////
///  6. XLM                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
  function xlm_getbalance($id){
  $xlm_info = StellarInfo::where('id',$id)->first();
   
  $url = $xlm_info->str_horizon.'accounts/'.$xlm_info->account_id;
   
   $lumen = 0 ; $pink = 0; 
   $ch = curl_init();
   $m = 'x';
   $i = 0;
   $l = 0;
  
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
    if($arr_datas->asset_type != 'native'){ 
     $m = $i;
     $pink = $arr_data->balances[$m]->balance;
    }
   if($arr_datas->asset_type == 'native'){
    $l = $i;
    $bal = $xlm_info->fix_limit;
    if($arr_data->balances[$l]->balance <= $bal){
    $lumen = 0;
    }else{
    $lumen = $arr_data->balances[$l]->balance - $bal;  
    }
   } 
   $i++;
  } 
  // return number_format($lumen,7);
  return $lumen;
  } 

function xlm_getbalance_myr($id) {
     
     $current_price = PriceAPI::where('name','Stellar')->first(); 
 
    $myr_balance = $current_price->price * xlm_getbalance($id);
    
       return $myr_balance;

}

  function xlm_getbalance_pod($id){
  $pod_des = StellarPod::where('destination_id',$id)->where('str_status','!=','cancel')->orderBy('id','desc')->first();    
  $pod_sour = StellarPod::where('source_id',$id)->where('str_status','!=','cancel')->orderBy('id','desc')->first(); 
        
        if(isset($pod_des) && !(isset($pod_sour))){
    return $pod_des->balance_destination;
  
        }
        
         elseif(!(isset($pod_des)) && isset($pod_sour)){
  
    return $pod_sour->balance_source;
  }
        
        
         elseif(isset($pod_des) && isset($pod_sour)){
  if($pod_des->id >= $pod_sour->id){
    return $pod_des->balance_destination;
  }elseif($pod_des->id < $pod_sour->id){
    return $pod_sour->balance_source;
  }
        
        }else{
            return 0;
        }
        
        
        
  }

  function xlm_idinfo($id,$value) {
    $xlm_info = StellarInfo::where('id',$id)->first();
   
  return $xlm_info->$value;
  }
/////////////////////////////////////////////////////////////////////
///  7. ETH                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function eth_getbalance($id){

      
   $converter = new \Bezhanov\Ethereum\Converter(); 
       $user = WalletAddress::where('uid',$id)->where('crypto','ETH')->first();
      $wallet_balance = Ethereum::eth_getBalance($user->address,'latest',TRUE);
      
      $float = number_format($wallet_balance, 0, '','');
      $value = round($converter->fromWei($float, 'ether'),5);
      
      
      $updt = WalletAddress::where('uid', $id)->where('crypto', 'ETH')
            ->update([
                 'available_balance' => $value
            ]);
      
      return $value;
}
function eth_getbalance_myr($id){
$converter = new \Bezhanov\Ethereum\Converter();  
  $uid = WalletAddress::where('uid',$id)->where('crypto','ETH')->first();
  $current_price = PriceAPI::where('name','Ethereum')->first(); 

  $wallet_balance = WalletAddress::where('uid',$id)->where('crypto','ETH')->first();

  $myr_balance = $current_price->price * $wallet_balance->available_balance;

  return $myr_balance;

}

function eth_moveuser($userlabel,$getuserlabel,$cryptoseller){
   $converter = new \Bezhanov\Ethereum\Converter();
        $user = WalletAddress::where('label',$getuserlabel)->where('crypto','ETH')->first();
        $admin = WalletAddress::where('label',$userlabel)->where('crypto','ETH')->first();
        $to = $user->address;
        $from = $admin->address;
        $gas = '0x'.dec2hex('100000');
        $gasPrice = '0x'.dec2hex('5000000000');

        $value = '0x'.dec2hex($converter->toWei($cryptoseller, 'ether'));
     //$value = '0x9184e72a';

        $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);

        $txid =  Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');
}
/////////////////////////////////////////////////////////////////////
///  8. XRP                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function xrp_getbalance($id) {

}

function xrp_getbalance_myr($id) {

}

/////////////////////////////////////////////////////////////////////
///  9. LIFE                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
///  TRANSACTIONS                 ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function transactions_btc($label) {


    //btc
    $post_ad = [
        'id' => 6,
        'label' => $label
    ];

    $ch_ad = curl_init(getinfoBTC());
    curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
    $btc_trans = curl_exec($ch_ad);

    $arr_data = json_decode($btc_trans);

    return $arr_data;
}

function transactions_bch($label) {
    //btc
    $post_ad = [
        'id' => 6,
        'label' => $label
    ];

    $ch_ad = curl_init(getinfoBCH());
    curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
    $btc_trans = curl_exec($ch_ad);

    $arr_data = json_decode($btc_trans);

    return  $arr_data;
}

function transactions_ltc($label) {


    //btc
    $post_ad = [
        'id' => 6,
        'label' => $label
    ];

    $ch_ad = curl_init(getinfoLTC());
    curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
    $btc_trans = curl_exec($ch_ad);

    $arr_data = json_decode($btc_trans);

    return $arr_data;
}

function transactions_dash($label) {


    //btc
    $post_ad = [
        'id' => 6,
        'label' => $label
    ];

    $ch_ad = curl_init(getinfoDASH());
    curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
    $btc_trans = curl_exec($ch_ad);

    $arr_data = json_decode($btc_trans);

    return $arr_data;
}

function transactions_doge($label) {


    //btc
    $post_ad = [
        'id' => 6,
        'label' => $label
    ];

    $ch_ad = curl_init(getinfoDOGE());
    curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
    $btc_trans = curl_exec($ch_ad);

    $arr_data = json_decode($btc_trans);

    return $arr_data;
}

// function balance_notify_eth($address){
//   $balance_url = "https://api.etherscan.io/api?module=account&action=balance&address=$address&tag=latest&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB";
//   $json = file_get_contents($json_url);
//   $data = json_decode($json);
//   return $data->result;
// }

// function wallet_notify_eth($address) {
//   //$block_url = "https://api.etherscan.io/api?module=proxy&action=eth_blockNumber&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB";
//   $json_url = "https://api.etherscan.io/api?module=account&action=txlist&address=$address&startblock=0&endblock=99999999&sort=desc&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB";
//         //get JSON data
//   $json = file_get_contents($json_url);
//   $data = json_decode($json);
//   return $data->result;
// }

function transactions_eth($address) {


  $json_url = "https://api.etherscan.io/api?module=account&action=txlist&address=$address&startblock=0&endblock=99999999&sort=asc&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB";
        //get JSON data
  $json = file_get_contents($json_url);
  $data = json_decode($json);

  return $data->result;
}

function transactions_life($address) {


  $json_url = "https://api.etherscan.io/api?module=account&action=txlist&address=$address&startblock=0&endblock=99999999&sort=asc&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB";
        //get JSON data
  $json = file_get_contents($json_url);
  $data = json_decode($json);

  return $data->result;
}


/////////////////////////////////////////////////////////////////////
///  ADMIN GET TRANSACTION             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function gettransaction_crypto($crypto, $txid) { 

 $getip = PriceApi::where('crypto', $crypto)->first()->ip_getinfo;

        $post = [
            'id' => 19,
            'txid' => $txid
        ];

        $ch = curl_init($getip);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

         $data = curl_exec($ch);
		$transaction = json_decode($data);
        curl_close($ch);
        return $transaction;

}

/////////////////////////////////////////////////////////////////////
///  ADD CRYPTO                  ///////////////////////////////////////
////////////////////////////////////////////////////////////////////


function addCrypto($crypto, $label) {

    //bch

    if ($crypto == 'BCH') {
        $post_ad = [
            'id' => 2,
            'label' => $label
        ];

        $ch_ad = curl_init(getinfoBCH());
        curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
        $add_crypto = curl_exec($ch_ad);
    } elseif ($crypto == 'LTC') {
        //ltc
        $post_ad = [
            'id' => 2,
            'label' => $label
        ];

        $ch_ad = curl_init(getinfoLTC());
        curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
        $add_crypto = curl_exec($ch_ad);
    } elseif ($crypto == 'DASH') {
        //dash
        $post_ad = [
            'id' => 2,
            'label' => $label
        ];

        $ch_ad = curl_init(getinfoDASH());
        curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
        $add_crypto = curl_exec($ch_ad);
    } elseif ($crypto == 'DOGE') {
        //doge
        $post_ad = [
            'id' => 2,
            'label' => $label
        ];

        $ch_ad = curl_init(getinfoDOGE());
        curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
        $add_crypto = curl_exec($ch_ad);
    } elseif ($crypto == 'XLM') {
        $add_crypto = '';
    } elseif ($crypto == 'XRP') {
        $add_crypto = '';
    } elseif ($crypto == 'ETH') {
        $add_crypto =  Ethereum::personal_newAccount('Pinkexc@22');
    } elseif ($crypto == 'LIFE') {
        $add_crypto =  Ethereum::personal_newAccount('Pinkexc@22');
    } else {
        $add_crypto = null;
    }
    return $add_crypto;
}

/////////////////////////////////////////////////////////////////////
///  WITHDRAW / SEND                  ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function send_crypto($crypto, $label, $address, $amount) {


    if ($crypto == 'BTC') {
        //btc

        $post = [
            'id' => 9,
            'label' => $label,
            'address' => $address,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoBTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

        curl_close($ch);

        return $txid;
    } elseif ($crypto == 'BCH') {

        //bch

        $post = [
            'id' => 9,
            'label' => $label,
            'address' => $address,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoBCH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

        curl_close($ch);

        return $txid;
    } elseif ($crypto == 'LTC') {
        //LTC

        $post = [
            'id' => 9,
            'label' => $label,
            'address' => $address,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoLTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

        curl_close($ch);

        return $txid;
    } elseif ($crypto == 'DASH') {

        //dash

        $post = [
            'id' => 9,
            'label' => $label,
            'address' => $address,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoDASH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

        curl_close($ch);

        return $txid;
    } elseif ($crypto == 'DOGE') {

        //doge

        $post = [
            'id' => 9,
            'label' => $label,
            'address' => $address,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoDOGE());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

        curl_close($ch);

        return $txid;
    } elseif ($crypto == 'ETH') {
$converter = new \Bezhanov\Ethereum\Converter();
       $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
       $from = $user->address;
       $to = $address;
       $gas = '0x'.dec2hex('100000');
       $gasPrice = '0x'.dec2hex('5000000000');

       $value = '0x'.dec2hex($converter->toWei($amount, 'ether'));
     //$value = '0x9184e72a';

       $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);

       return Ethereum::personal_sendTransaction($transaction,'P-HY,mUr)PfGQ9NW/BNs:+q3>)YLb+Q8uz"gq;(!*Avd*EQd');

   } elseif ($crypto == 'XRP') {
    $txid = '';
    return $txid;
} elseif ($crypto == 'XLM') {
    $txid = '';
    return $txid;
} elseif ($crypto == 'LIFE') {
    $txid = '';
    return $txid;
} else {
    $txid = null;
    return $txid;
}
}

/////////////////////////////////////////////////////////////////////
///  MOVE TO FEES WALLET                 ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function withdrawal_fees_crypto($crypto, $label, $amount) {

    if ($crypto == 'BTC') {
        //btc
        $post = [
            'id' => 17,
            'label' => $label,
            'amount' => $amount,
        ];


        $ch = curl_init(getinfoBTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'BCH') {
        $post = [
            'id' => 17,
            'label' => $label,
            'amount' => $amount,
        ];


        $ch = curl_init(getinfoBCH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'LTC') {
        $post = [
            'id' => 17,
            'label' => $label,
            'amount' => $amount,
        ];


        $ch = curl_init(getinfoLTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'DASH') {
        //dash
        $post = [
            'id' => 17,
            'label' => $label,
            'amount' => $amount,
        ];


        $ch = curl_init(getinfoDASH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'DOGE') {
        //doge
        $post = [
            'id' => 17,
            'label' => $label,
            'amount' => $amount,
        ];


        $ch = curl_init(getinfoDOGE());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'XLM') {
        $add_crypto = '';
    } elseif ($crypto == 'XRP') {
        $add_crypto = '';
    } elseif ($crypto == 'ETH') {
$converter = new \Bezhanov\Ethereum\Converter();
        $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
        $admin = WalletAddress::where('label',$label2)->where('crypto',$crypto)->first();
        $from = $user->address;
        $to = $admin->address;
        $gas = '0x'.dec2hex('100000');
        $gasPrice = '0x'.dec2hex('5000000000');

        $value = '0x'.dec2hex($converter->toWei($amount, 'ether'));
     //$value = '0x9184e72a';

        $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);

        $txid =  Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');

        if($txid != '')
        {
            return true;
        }
        else
        {
            return null;
        }
    } elseif ($crypto == 'LIFE') {
        $add_crypto = '';
    } else {

        $add_crypto = null;
        return $add_crypto;
    }
}

function dec2hex($number)
{
  $hexvalues = array('0','1','2','3','4','5','6','7',
   '8','9','a','b','c','d','e','f');
  $hexval = '';
  while($number != '0')
  {
    $hexval = $hexvalues[bcmod($number,'16')].$hexval;
    $number = bcdiv($number,'16',0);
}
return $hexval;
}


/////////////////////////////////////////////////////////////////////
///  GET LABEL BY ADDRESS                  ///////////////////////////////////////
////////////////////////////////////////////////////////////////////



function get_label_crypto($crypto, $address) {
    if ($crypto == 'BTC') {
        //bch

        $post = [
            'id' => 14,
            'address' => $address
        ];


        $ch = curl_init(getinfoBTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $label = curl_exec($ch);

        curl_close($ch);

        return $label;
    } elseif ($crypto == 'BCH') {
        //bch

        $post = [
            'id' => 14,
            'address' => $address
        ];


        $ch = curl_init(getinfoBCH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $label = curl_exec($ch);

        curl_close($ch);

        return $label;
    } elseif ($crypto == 'LTC') {

        $post = [
            'id' => 14,
            'address' => $address
        ];


        $ch = curl_init(getinfoLTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $label = curl_exec($ch);

        curl_close($ch);

        return $label;
    } elseif ($crypto == 'DASH') {
        $post = [
            'id' => 14,
            'address' => $address
        ];


        $ch = curl_init(getinfoDASH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $label = curl_exec($ch);

        curl_close($ch);

        return $label;
    } elseif ($crypto == 'DOGE') {
        $post = [
            'id' => 14,
            'address' => $address
        ];


        $ch = curl_init(getinfoDOGE());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $label = curl_exec($ch);

        curl_close($ch);

        return $label;
    } elseif ($crypto == 'XLM') {
        $label = '';
        return $label;
    } elseif ($crypto == 'XRP') {
        $label = '';
        return $label;
    } elseif ($crypto == 'ETH') {
        $label = '';
        return $label;
    } elseif ($crypto == 'LIFE') {
        $label = '';
        return $label;
    } else {
        $label = null;
        return $label;
    }
}

/////////////////////////////////////////////////////////////////////
///  MOVE                ///////////////////////////////////////
////////////////////////////////////////////////////////////////////


function move_crypto($crypto, $label, $label2, $amount) {
    if ($crypto == 'BTC') {
        $post = [
            'id' => 15,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoBTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'BCH') {
        $post = [
            'id' => 15,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoBCH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'LTC') {
        $post = [
            'id' => 15,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoLTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'DASH') {

        $post = [
            'id' => 15,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoDASH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'DOGE') {
        $post = [
            'id' => 15,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount
        ];


        $ch = curl_init(getinfoDOGE());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'ETH') {


	$converter = new \Bezhanov\Ethereum\Converter();
        $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
        $admin = WalletAddress::where('label',$label2)->where('crypto',$crypto)->first();
        $from = $user->address;
        $to = $admin->address;
        $gas = '0x'.dec2hex('100000');
	//$gasPriceData = $
        $gasPrice = '0x'.dec2hex('5000000000');

        $value = '0x'.dec2hex($converter->toWei($amount, 'ether'));
     	//$value = '0x9184e72a';

        $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);

        $txid =  Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');

        if($txid != '')
        {
            return true;
        }
        else
        {
            return null;
        }


    } elseif ($crypto == 'XLM') {
        $result = '';
        return $result;
    } elseif ($crypto == 'XRP') {
        $result = '';
        return $result;
    } elseif ($crypto == 'LIFE') {
        $result = '';
        return $result;
    } else {
        $result = null;
        return $result;
    }
}


/////////////////////////////////////////////////////////////////////
///  ADMIN GET BALANCE             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function getbalance($crypto, $label) {
    //UPDATE BALANCE INTO TABLE WALLETADDRESS
    // $user = WalletAddress::findOrFail($uid->id);
    // $user->available_balance = $wallet_balance;
    //$user->save();

    if ($crypto == 'BTC') {
        //GET BALANCE
        $post = [
            'id' => 4,
            'label' => $label
        ];

        $ch = curl_init(getinfoBTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    } elseif ($crypto == 'BCH') {
        $post = [
            'id' => 4,
            'label' => $label
        ];

        $ch = curl_init(getinfoBCH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    } elseif ($crypto == 'LTC') {
        $post = [
            'id' => 4,
            'label' => $label
        ];

        $ch = curl_init(getinfoLTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    } elseif ($crypto == 'DASH') {
        $post = [
            'id' => 4,
            'label' => $label
        ];

        $ch = curl_init(getinfoDASH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    } elseif ($crypto == 'DOGE') {
        $post = [
            'id' => 4,
            'label' => $label
        ];

        $ch = curl_init(getinfoDOGE());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    } elseif ($crypto == 'ETH') {
$converter = new \Bezhanov\Ethereum\Converter();      
$user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
      $wallet_balance = Ethereum::eth_getBalance($user->address,'latest',TRUE);     
   $float = number_format($wallet_balance, 0, '','');
      $value = round($converter->fromWei($float, 'ether'),5);

      return $value;

  } elseif ($crypto == 'XLM') {
    $wallet_balance = '';
    return $wallet_balance;
} elseif ($crypto == 'XRP') {
    $wallet_balance = '';
    return $wallet_balance;
} elseif ($crypto == 'LIFE') {
    $converter = new \Bezhanov\Ethereum\Converter();      
$user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
      $wallet_balance = Ethereum::eth_getBalance($user->address,'latest',TRUE);     
   $float = number_format($wallet_balance, 0, '','');
      $value = round($converter->fromWei($float, 'ether'),5);

      return $value;
} else {
    $wallet_balance = null;
    return $wallet_balance;
}
}

/////////////////////////////////////////////////////////////////////
///  MYR BALANCE             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function getbalance_myr_balance($id) {



  $labelBTC = WalletAddress::where('uid', $id)->where('crypto', 'BTC')->first();

  if ($labelBTC != null) {
    $available_balBTC = WalletAddress::where('uid', $id)->where('crypto', 'BTC')->first()->available_balance;
    $current_priceBTC = PriceAPI::where('crypto', 'BTC')->first()->price;
	if($available_balBTC == ''){
	$available_balBTC = 0;
	}

    $myr_balanceBTC = round(($current_priceBTC * $available_balBTC), 2);
} else {
    $myr_balanceBTC = 0;
}


$labelBCH = WalletAddress::where('uid', $id)->where('crypto', 'BCH')->first();

if ($labelBCH != null) {
  $available_balBCH = WalletAddress::where('uid', $id)->where('crypto', 'BCH')->first()->available_balance;
  $current_priceBCH = PriceAPI::where('crypto', 'BCH')->first()->price;
if($available_balBCH == ''){
	$available_balBCH = 0;
	}

  $myr_balanceBCH = round(($current_priceBCH * $available_balBCH), 2);
} else {
    $myr_balanceBCH = 0;
}


$labelLTC = WalletAddress::where('uid', $id)->where('crypto', 'LTC')->first();

if ($labelLTC != null) {
    $available_balLTC = WalletAddress::where('uid', $id)->where('crypto', 'LTC')->first()->available_balance;
    $current_priceLTC = PriceAPI::where('crypto', 'LTC')->first()->price;
if($available_balLTC == ''){
	$available_balLTC = 0;
	}

    $myr_balanceLTC = round(($current_priceLTC * $available_balLTC), 2);
} else {
    $myr_balanceLTC = 0;
}

$labelDASH = WalletAddress::where('uid', $id)->where('crypto', 'DASH')->first();

if ($labelDASH != null) {
   $available_balDASH = WalletAddress::where('uid', $id)->where('crypto', 'DASH')->first()->available_balance;
   $current_priceDASH = PriceAPI::where('crypto', 'DASH')->first()->price;
if($available_balDASH == ''){
	$available_balDASH = 0;
	}

   $myr_balanceDASH = round(($current_priceDASH * $available_balDASH), 2);
} else {
    $myr_balanceDASH = 0;
}

$labelDOGE = WalletAddress::where('uid', $id)->where('crypto', 'DOGE')->first();

if ($labelDOGE != null) {
    $available_balDOGE = WalletAddress::where('uid', $id)->where('crypto', 'DOGE')->first()->available_balance;
    $current_priceDOGE = PriceAPI::where('crypto', 'DOGE')->first()->price;
if($available_balDOGE == ''){
	$available_balDOGE = 0;
	}
    $myr_balanceDOGE = round(($current_priceDOGE * $available_balDOGE), 2);
} else {
    $myr_balanceDOGE = 0;
}

$labelXLM = WalletAddress::where('uid', $id)->where('crypto', 'XLM')->first();

if ($labelXLM != null) {
    $available_balXLM = xlm_getbalance_pod($id);
    $current_priceXLM = PriceAPI::where('crypto', 'XLM')->first()->price;
if($available_balXLM == ''){
	$available_balXLM = 0;
	}

    $myr_balanceXLM = round(($current_priceXLM * $available_balXLM), 2);
} else {
    $myr_balanceXLM = 0;
}

$labelETH = WalletAddress::where('uid', $id)->where('crypto', 'ETH')->first();

if ($labelETH != null) {
    $available_balETH = eth_getbalance($id);
    $current_priceETH = PriceAPI::where('crypto', 'ETH')->first()->price;
if( $available_balETH == ''){
	 $available_balETH = 0;
	}

    $myr_balanceETH = round(($current_priceETH * $available_balETH), 2);
} else {
    $myr_balanceETH = 0;
}

$labelXRP = WalletAddress::where('uid', $id)->where('crypto', 'XRP')->first();

if ($labelXRP != null) {
    $available_balXRP = WalletAddress::where('uid', $id)->where('crypto', 'XRP')->first()->available_balance;
    $current_priceXRP = PriceAPI::where('crypto', 'XRP')->first()->price;
if( $available_balXRP == ''){
	 $available_balXRP = 0;
	}

    $myr_balanceXRP = round(($current_priceXRP * $available_balXRP), 2);
} else {
    $myr_balanceXRP = 0;
}


$totalAsset = $myr_balanceBTC + $myr_balanceBCH + $myr_balanceLTC + $myr_balanceDASH + $myr_balanceDOGE + $myr_balanceXLM + $myr_balanceXRP + $myr_balanceETH;

return $totalAsset;

}


function callAPI($method, $url, $data){
 $curl = curl_init();

 switch ($method){
  case "POST":
  curl_setopt($curl, CURLOPT_POST, 1);
  if ($data)
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
break;
case "PUT":
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
if ($data)
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                              
break;
default:
if ($data)
    $url = sprintf("%s?%s", $url, http_build_query($data));
}

   // OPTIONS:
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
  'Content-Type: application/json',
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

   // EXECUTE:
$result = curl_exec($curl);
if(!$result){die("Connection Failure");}
curl_close($curl);
return $result;
}


function coinvata_price($convertFrom,$convertTo,$rate)
{

  if ($convertFrom == "BTC") 
  {

    $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin?localization=false', false);
    $response_price = json_decode($get_data_price, true);

    $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
    $maximum_price = round((10000 / $response_price['market_data']['current_price']['myr']),8);

    if ($convertTo == "LTC") {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin?localization=false', false);
        $response = json_decode($get_data, true);

        $currentprice = $response['market_data']['current_price']['ltc'] - ($response['market_data']['current_price']['ltc'] * $rate);
        $displayprice = $response['market_data']['current_price']['ltc'];


    }
    else if ($convertTo == "DOGE") {

       $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
       $response = json_decode($get_data, true);

       $currentdata = $response['market_data']['current_price']['btc'];

       $currentprice = (1 / $currentdata) - ((1 / $currentdata) * $rate);
       $displayprice = (1 / $currentdata);

   }
   else if ($convertTo == "DASH") {

       $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
       $response = json_decode($get_data, true);

       $currentdata = $response['market_data']['current_price']['btc'];

       $currentprice = (1 / $currentdata) - ((1 / $currentdata) * $rate);
       $displayprice = (1 / $currentdata);

   }
   else if ($convertTo == "ETH") {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin?localization=false', false);
        $response = json_decode($get_data, true);

        $currentprice = $response['market_data']['current_price']['eth'] - ($response['market_data']['current_price']['eth'] * $rate);
        $displayprice = $response['market_data']['current_price']['eth'];

}
else if ($convertTo == "BCH") {

   $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin?localization=false', false);
   $response = json_decode($get_data, true);

   $currentprice = $response['market_data']['current_price']['bch'] - ($response['market_data']['current_price']['bch'] * $rate);
   $displayprice = $response['market_data']['current_price']['bch'];

}

}
else if($convertFrom == "LTC")
{

  $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/litecoin?localization=false', false);
  $response_price = json_decode($get_data_price, true);

  $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
  $maximum_price = round((10000 / $response_price['market_data']['current_price']['myr']),8);


  if ($convertTo == "BTC") {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/litecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentprice = $response['market_data']['current_price']['btc'] - ($response['market_data']['current_price']['btc'] * $rate);
    $displayprice = $response['market_data']['current_price']['btc'];

}
else if ($convertTo == "DOGE") {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['ltc'];

    $currentprice = (1 / $currentdata) - ((1 / $currentdata) * $rate);
    $displayprice = (1 / $currentdata);

}
else if ($convertTo == "DASH") 
{
    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['ltc'];

    $currentprice = (1 / $currentdata) - ((1 / $currentdata) * $rate);
    $displayprice = (1 / $currentdata);

}
else if ($convertTo == "ETH") 
{

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/litecoin?localization=false', false);
        $response = json_decode($get_data, true);

        $currentprice = $response['market_data']['current_price']['eth'] - ($response['market_data']['current_price']['eth'] * $rate);
        $displayprice = $response['market_data']['current_price']['eth'];

}
else if ($convertTo == "BCH") 
{

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/litecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['bch'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

}
}
else if($convertFrom == "DOGE")
{

  $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
  $response_price = json_decode($get_data_price, true);

  $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
  $maximum_price = round((10000 / $response_price['market_data']['current_price']['myr']),8);

  if ($convertTo == "BTC") {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['btc'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

}
else if ($convertTo == "LTC") 
{

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['ltc'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

}
else if ($convertTo == "DASH") 
{

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['btc'];

    $get_data1 = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
    $response1 = json_decode($get_data1, true);

    $currentdata1 = $response1['market_data']['current_price']['btc'];

    $currentprice = ($currentdata / $currentdata1) - (($currentdata / $currentdata1) * $rate);
    $displayprice = ($currentdata / $currentdata1);

}
else if ($convertTo == "ETH") {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['eth'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

}
else if ($convertTo == "BCH") 
{

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['bch'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

}
}
else if($convertFrom == "DASH")
{

    $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
    $response_price = json_decode($get_data_price, true);

    $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
    $maximum_price = round((10000 / $response_price['market_data']['current_price']['myr']),8);


    if ($convertTo == "BTC") 
    {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['btc'];

        $currentprice = $currentdata - ($currentdata * $rate);
        $displayprice = $currentdata;

    }
    else if ($convertTo == "LTC") 
    {
        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['ltc'];

        $currentprice = $currentdata - ($currentdata * $rate);
        $displayprice = $currentdata;


    }
    else if ($convertTo == "DOGE") 
    {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['btc'];

        $get_data1 = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
        $response1 = json_decode($get_data1, true);

        $currentdata1 = $response1['market_data']['current_price']['btc'];

        $currentprice = ($currentdata / $currentdata1) - (($currentdata / $currentdata1) * $rate);
        $displayprice = ($currentdata / $currentdata1);

    }
    else if ($convertTo == "ETH") 
    {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['eth'];

        $currentprice = $currentdata - ($currentdata * $rate);
        $displayprice = $currentdata;

 }
 else if ($convertTo == "BCH") 
 {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['bch'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;


}
}
else if($convertFrom == "BCH")
{

    $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin-cash?localization=false', false);
    $response_price = json_decode($get_data_price, true);

    $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
    $maximum_price = round((10000 / $response_price['market_data']['current_price']['myr']),8);


    if ($convertTo == "BTC") 
    {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin-cash?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['btc'];

        $currentprice = $currentdata - ($currentdata * $rate);
        $displayprice = $currentdata;

    }
    else if ($convertTo == "LTC") 
    {
        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin-cash?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['ltc'];

        $currentprice = $currentdata - ($currentdata * $rate);
        $displayprice = $currentdata;

    }
    else if ($convertTo == "DOGE") 
    {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['bch'];

        $currentprice = (1 / $currentdata) - ((1 /$currentdata) * $rate);
        $displayprice = (1 / $currentdata);

    }
    else if ($convertTo == "DASH") 
    {
        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['bch'];

        $currentprice = (1 / $currentdata) - ((1 /$currentdata) * $rate);
        $displayprice = (1 / $currentdata);

    }
    else if ($convertTo == "ETH") 
    {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin-cash?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['eth'];

        $currentprice = $currentdata - ($currentdata * $rate);
        $displayprice = $currentdata;

    }
}
else if($convertFrom == "ETH")
{

    $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ethereum?localization=false', false);
    $response_price = json_decode($get_data_price, true);

    $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
    $maximum_price = round((10000 / $response_price['market_data']['current_price']['myr']),8);


    if ($convertTo == "BTC") 
    {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ethereum?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['btc'];

        $currentprice = $currentdata - ($currentdata * $rate);
        $displayprice = $currentdata;

    }
    else if ($convertTo == "LTC") 
    {
        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ethereum?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['ltc'];

        $currentprice = $currentdata - ($currentdata * $rate);
        $displayprice = $currentdata;

    }
    else if ($convertTo == "DOGE") 
    {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['eth'];

        $currentprice = (1 / $currentdata) - ((1 /$currentdata) * $rate);
        $displayprice = (1 / $currentdata);

    }
    else if ($convertTo == "DASH") 
    {
        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['eth'];

        $currentprice = (1 / $currentdata) - ((1 /$currentdata) * $rate);
        $displayprice = (1 / $currentdata);

    }
    else if ($convertTo == "BCH") 
    {

        $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ethereum?localization=false', false);
        $response = json_decode($get_data, true);

        $currentdata = $response['market_data']['current_price']['bch'];

        $currentprice = $currentdata - ($currentdata * $rate);
        $displayprice = $currentdata;

    }
}

return response()->json([
    'current_price' => $currentprice,
    'displayprice' => $displayprice,
    'minimum_price' => $minimum_price,
    'maximum_price' => $maximum_price

]);

}
//////////////////////////////////////////
/////SEND EMAIL COINVATA//////////////////
//////////////////////////////////////////

function send_email_coinvata($id,$from,$to,$displayprice,$amountFrom,$amountTo,$fee,$user_id)
{

    $invoice_no = $id;
    $txt_no = sprintf('%07d',$invoice_no); 
    $t=time();
	$Userdata = User::where('id',$user_id)->first();
    $to=$Userdata->email; //change to ur mail address
    $subject="Colony | Coinvata";
   // $message =  file_get_contents('https://www.pinkexc.online/sources/template.php/?&b=edit&id='.$id); /* Your Template*/


    $htmlContent = '
    <html><body>


    <center>
    <div style="max-width:800px;padding: 20px;border: 1px solid #eee;box-shadow: 0 0 10px rgba(0, 0, 0, .15);font-size: 14px;font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;color: #555;">
    <table style="width:100%;">



    <tr>
    <td colspan="3">
    <img src="https://www.pinkexc.online/assets/images/logoreceipt.png" style="width:250px;">
    </td> 
    <td colspan="2">
    P'.$invoice_no.''.$t.' <br>
    '.Carbon::today()->format('d-m-Y').' <br>

    </td> 
    </tr>




    <tr>
    <td colspan="3">
    Pinkexc (M). Sdn. Bhd.<br>
    1, Jln Meru Bestari A14,<br> 
    Medan Meru Bestari, <br>
    31200 Ipoh, Perak Darul Ridzuan.

    </td>   
    <td colspan="2">

    admin@pinkexc.com
    </td>
    </tr>

    <tr><td colspan="5"><br><br></td>
    </tr>

    <tr>
    <td colspan="2">
    <b>Username : </b>'.$Userdata->username.'<br> 
    <b>Email : </b>'.$Userdata->email.'<br>

    </td> 
    <td colspan="3"></td>
    </tr>

    <tr>
    <td colspan="5"><hr></td>
    </tr>


    <tr><td colspan="5"><br></td></tr>



    <tr>
    <td colspan="2"><strong>Item</strong></td> 
    <td width="20%;"><strong>&nbsp;Current Rate&nbsp;&nbsp;</strong></td>
    <td width="20%;"><strong>&nbsp;Amount '.$from.'&nbsp;&nbsp;</strong></td>
    <td width="20%;"><strong>&nbsp;Amount '.$to.'&nbsp;&nbsp;</strong></td>

    </tr>
    <tr>
    <td colspan="5"><hr></td>
    </tr>


    

    <tr>
    <td colspan="2">'.$from.' - '.$to.'</td> 
    <td width="20%;"> '.round($displayprice,8).' '.$to.' </td>
    <td width="20%;">'.$amountFrom.' '.$from.'</td>
    <td width="20%;">'.$amountTo.' '.$to.'</td>
    </tr>   


    </table>
    <br>
    <h5> Note : The displayed price does not include '.$fee.'% service charge. </h5>

    </div>
    </center>

    </body></html>

    ';
    $headers = 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
    $headers .= "From: noreply@pinkexc.com "; 

           mail($to, $subject, $htmlContent, $headers);  // Mail Function
       }




//////////////////////////////////////////
/////NEW//////////////////
//////////////////////////////////////////


function walletinfo($uid,$value) 
{
  $verify = WalletAddress::where('uid',$uid)->first();
  return $verify->$value;
}

function verificationinfo($uid,$value)
{
  $verify = Verification::where('uid',$uid)->first();
  return $verify->$value;
}

function formatBytes($bytes, $precision = 2) { 
    if ($bytes > pow(1024,3)) return round($bytes / pow(1024,3), $precision)."GB";
    else if ($bytes > pow(1024,2)) return round($bytes / pow(1024,2), $precision)."MB";
    else if ($bytes > 1024) return round($bytes / 1024, $precision)."KB";
    else return ($bytes)."B";
} 

function idinfo($uid,$value){
  $verify = User::where('id',$uid)->first();
  return $verify->$value;
}

function settings($value){
  $setting = Setting::first();
  return $setting->$value;
}

////////////////API FUNCTION//////////////////

       function apiToken($session_uid)
       {
        $key=md5('Colony'.$session_uid);
        return hash('sha256', $key);
    }

    function isValidUsername($str) {
        return preg_match('/^[a-zA-Z0-9-_]+$/',$str);
    }

    function isValidEmail($str) {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }


####################gasprice#########################
function gaspriceData(){
    $gasprice = Gasprice::where('id',1)->first();
    
    $normal1 = $gasprice->average;
    $normal = $normal1+5;
    $fast1 =$gasprice->rapid;
    $fast = $fast1+5;
    
        $datamsg = response()->json([
            'normal' => $normal,
        'fast' => $fast
        ]);

        return $datamsg->content();

}


function send_email_toadmin($to, $subject,$name,$message,$orderid){
  $setting = Setting::first();

  $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
  $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $template = $setting->template_sendtoadmin;
  $mm = str_replace("{{name}}",$name,$template);
  $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
  $url = str_replace("{{url}}",$setting->url.'admin/instant_buy/edit/'.$orderid, $supportemail);
  $message = str_replace("{{message}}",$message,$url);


  $result = mail($to, $subject,$message, $headers);
return $result;
}




