<?php

use App\General;
use App\User;
use App\WalletAddress;
use App\Verification;
use App\PriceApi;
use App\StellarInfo;
use App\StellarPod;
use App\StellarWithdrawal;
use App\DuplicateXLM;
use App\Pinkexcsell;
use App\Pinkexcbuy;
use App\StellarPinkexcsell;
use App\StellarPinkexcbuy;
use App\Anypayop;
use App\Anypaytrans;
use Carbon\Carbon;
use App\Setting;
use App\ETHDeplete;
use App\EthAllTransaction;
use App\Gasprice;
////for maintenace
use Jcsofts\LaravelEthereum\Facade\Ethereum as Ethereum;    //eth down
use Jcsofts\LaravelEthereum\Lib\EthereumTransaction;    //eth down
use EthereumRPC\EthereumRPC;    //eth down
use ERC20\ERC20;    //eth down
////////////
//use CashaddrConverter;
use FOXRP\Rippled\Client;
//use Mail;
use App\ShopPayment;
use App\ShopOrder;
use App\ShopProduct;
use App\ShopRelease;
//use DB;
use App\StellarCoinvata;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PaymentOp;
use ZuluCrypto\StellarSdk\XdrModel\Asset;



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

function send_email_verify_shop($to, $subject, $name, $message,$hash){
  $setting = Setting::first();

  $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
  $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $template = $setting->template_email_verify;
  $mm = str_replace("{{name}}",$name,$template);
  $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
  $url = str_replace("{{url}}",$setting->url.'shop/verify/email/'.$hash , $supportemail);
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


function dashboardInfo($crypto,$id,$label_admin,$label_fees,$label_coinvata,$label_jompay)
{  

	 $settings = Setting::first(); 
     $price_api = PriceApi::where('crypto',$crypto)->first(); 
	 $rate_pricemyr = $price_api->price; 

	 $price2 = round($rate_pricemyr,2);
	 if($crypto=='xlm'){ $network_fee = $settings->network_fee_xlm; }
	 else{ $network_fee = round($settings->network_fee/$price2,8); }
	
    $btc_balance = WalletAddress::where('label',$label_admin)->where('crypto','BTC')->first()->available_balance;
    $btc_myrbalance = $btc_balance * PriceApi::where('crypto','BTC')->first()->price;
    $btc_feebalance = WalletAddress::where('label',$label_fees)->where('crypto','BTC')->first()->available_balance;
	$btc_feebalance_myr = $btc_feebalance * PriceApi::where('crypto','BTC')->first()->price;
	$btcC_balance = WalletAddress::where('label',$label_coinvata)->where('crypto','BTC')->first()->available_balance;
    $btcC_myrbalance = $btcC_balance * PriceApi::where('crypto','BTC')->first()->price;
	$btcJ_balance = WalletAddress::where('label',$label_jompay)->where('crypto','BTC')->first()->available_balance;
    $btcJ_myrbalance = $btcJ_balance * PriceApi::where('crypto','BTC')->first()->price;
    
    $bch_balance = WalletAddress::where('label',$label_admin)->where('crypto','BCH')->first()->available_balance;
    $bch_myrbalance = $bch_balance * PriceApi::where('crypto','BCH')->first()->price;
    $bch_feebalance = WalletAddress::where('label',$label_fees)->where('crypto','BCH')->first()->available_balance;
	$bch_feebalance_myr = $bch_feebalance * PriceApi::where('crypto','BCH')->first()->price;
	$bchC_balance = WalletAddress::where('label',$label_coinvata)->where('crypto','BCH')->first()->available_balance;
    $bchC_myrbalance = $bchC_balance * PriceApi::where('crypto','BCH')->first()->price;
	$bchJ_balance = WalletAddress::where('label',$label_jompay)->where('crypto','BCH')->first()->available_balance;
    $bchJ_myrbalance = $bchJ_balance * PriceApi::where('crypto','BCH')->first()->price;
    
    $ltc_balance = WalletAddress::where('label',$label_admin)->where('crypto','LTC')->first()->available_balance;
    $ltc_myrbalance = $ltc_balance * PriceApi::where('crypto','LTC')->first()->price;
    $ltc_feebalance = WalletAddress::where('label',$label_fees)->where('crypto','LTC')->first()->available_balance;
	$ltc_feebalance_myr = $ltc_feebalance * PriceApi::where('crypto','LTC')->first()->price;
	$ltcC_balance = WalletAddress::where('label',$label_coinvata)->where('crypto','LTC')->first()->available_balance;
    $ltcC_myrbalance = $ltcC_balance * PriceApi::where('crypto','LTC')->first()->price;
	$ltcJ_balance = WalletAddress::where('label',$label_jompay)->where('crypto','LTC')->first()->available_balance;
    $ltcJ_myrbalance = $ltcJ_balance * PriceApi::where('crypto','LTC')->first()->price;
    
    $doge_balance = WalletAddress::where('label',$label_admin)->where('crypto','DOGE')->first()->available_balance;
    $doge_myrbalance = $doge_balance * PriceApi::where('crypto','DOGE')->first()->price;
    $doge_feebalance = WalletAddress::where('label',$label_fees)->where('crypto','DOGE')->first()->available_balance;
	$doge_feebalance_myr = $doge_feebalance * PriceApi::where('crypto','DOGE')->first()->price;
	$dogeC_balance = WalletAddress::where('label',$label_coinvata)->where('crypto','DOGE')->first()->available_balance;
    $dogeC_myrbalance = $dogeC_balance * PriceApi::where('crypto','DOGE')->first()->price;
	$dogeJ_balance = WalletAddress::where('label',$label_jompay)->where('crypto','DOGE')->first()->available_balance;
    $dogeJ_myrbalance = $dogeJ_balance * PriceApi::where('crypto','DOGE')->first()->price;
 
    $dash_balance = WalletAddress::where('label',$label_admin)->where('crypto','DASH')->first()->available_balance; 
 //$dash_balance = 0; 
    $dash_myrbalance = $dash_balance * PriceApi::where('crypto','DASH')->first()->price;
    $dash_feebalance = WalletAddress::where('label',$label_fees)->where('crypto','DASH')->first()->available_balance;
//$dash_feebalance = 0;
	$dash_feebalance_myr = $dash_feebalance * PriceApi::where('crypto','DASH')->first()->price;
	$dashC_balance = WalletAddress::where('label',$label_coinvata)->where('crypto','DASH')->first()->available_balance;
	//$dashC_balance = 0;
    $dashC_myrbalance = $dashC_balance * PriceApi::where('crypto','DASH')->first()->price;
	$dashJ_balance = WalletAddress::where('label',$label_jompay)->where('crypto','DASH')->first()->available_balance;
	//$dashJ_balance = 0;
    $dashJ_myrbalance = $dashJ_balance * PriceApi::where('crypto','DASH')->first()->price;
	
    $xlm_balance = xlm_getbalance(1);
    $xlm_myrbalance = xlm_getbalance_myr(1); 
    $xlm_feebalance = round(StellarWithdrawal::where('transfer','0')->sum('com_withdrawal'),7);
    $xlm_feebalance_myr = $xlm_feebalance * PriceApi::where('crypto','XLM')->first()->price;
    $xlmC_balance = xlm_getbalance(3);
    $xlmC_myrbalance = xlm_getbalance_myr(3);
    $xlmJ_balance = 0;
    $xlmJ_myrbalance = 0;
	   
    $eth_balance = WalletAddress::where('label',$label_admin)->where('crypto','ETH')->first()->available_balance;
    $eth_myrbalance = $eth_balance * PriceApi::where('crypto','ETH')->first()->price;
    $eth_feebalance = WalletAddress::where('label',$label_fees)->where('crypto','ETH')->first()->available_balance;
	$eth_feebalance_myr = $eth_feebalance * PriceApi::where('crypto','ETH')->first()->price;
	$ethC_balance = WalletAddress::where('label',$label_coinvata)->where('crypto','ETH')->first()->available_balance;
    $ethC_myrbalance = $ethC_balance * PriceApi::where('crypto','ETH')->first()->price;
	$ethJ_balance = WalletAddress::where('label',$label_jompay)->where('crypto','ETH')->first()->available_balance;
    $ethJ_myrbalance = $ethJ_balance * PriceApi::where('crypto','ETH')->first()->price;

/*
$eth_balance = 0;
    $eth_myrbalance = 0;
    $eth_feebalance = 0;
	$eth_feebalance_myr = 0;
	$ethC_balance = 0;
    $ethC_myrbalance = 0;
	$ethJ_balance = 0;
    $ethJ_myrbalance = 0;
*/
	
    $life_balance = WalletAddress::where('label',$label_admin)->where('crypto','LIFE')->first()->available_balance;
    $life_myrbalance = $life_balance * PriceApi::where('crypto','LIFE')->first()->price;
    $life_feebalance = 0;
	$life_feebalance_myr = 0;
	$lifeC_balance = 0;
    $lifeC_myrbalance = 0;
	$lifeJ_balance = 0;
    $lifeJ_myrbalance = 0;
/*
$life_balance = 0;
    $life_myrbalance = 0;
    $life_feebalance = 0;
	$life_feebalance_myr = 0;
	$lifeC_balance = 0;
    $lifeC_myrbalance = 0;
	$lifeJ_balance = 0;
    $lifeJ_myrbalance = 0;
*/
	
     $xrp_balance = WalletAddress::where('label',$label_admin)->where('crypto','XRP')->first()->available_balance;
    $xrp_myrbalance = $xrp_balance * PriceApi::where('crypto','XRP')->first()->price;
    $xrp_feebalance = WalletAddress::where('label',$label_fees)->where('crypto','XRP')->first()->available_balance;
	$xrp_feebalance_myr = $xrp_feebalance * PriceApi::where('crypto','XRP')->first()->price;
	$xrpC_balance = WalletAddress::where('label',$label_coinvata)->where('crypto','XRP')->first()->available_balance;
   $xrpC_myrbalance = $xrpC_balance * PriceApi::where('crypto','XRP')->first()->price;
	$xrpJ_balance = 0;
   $xrpJ_myrbalance = 0;

	
    $totalAll = $btc_myrbalance + $bch_myrbalance + $ltc_myrbalance + $doge_myrbalance + $dash_myrbalance + $xlm_myrbalance + $eth_myrbalance + $life_myrbalance + $xrp_myrbalance;
     	
	$total_users = User::count();  
	 $dup_hash_xlm = DuplicateXLM::count();   
	 $totalXLM = StellarInfo::where('id',2)->first()->balance;
	 $admin_address = WalletAddress::where('label',$label_admin)->where('crypto',$crypto)->first();
	
	if($crypto=='LIFE'){
	 $adminC_address = '';
	 }else{
	 $adminC_address = WalletAddress::where('label',$label_coinvata)->where('crypto',$crypto)->first();	 
	 }
	 
	if($crypto=='LIFE' || $crypto=='XRP' || $crypto=='XLM'){ 
	 $adminJ_address = '';
	 }else{
	 $adminJ_address = WalletAddress::where('label',$label_jompay)->where('crypto',$crypto)->first();	 
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
	elseif($crypto == 'xrp'||$crypto == 'XRP'){
	$user_balance = $xrp_balance;
	$userC_balance = $xrpC_balance;
	$url = 'admin.home';
	}	
	elseif($crypto == 'xlm'||$crypto == 'XLM'){
	$user_balance = xlm_getbalance(1);
	$userC_balance = xlm_getbalance(3);
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
	   'btc_myrbalance' => $btc_myrbalance,
	   'btcC_myrbalance' => $btcC_myrbalance,
	   'btc_feebalance' => $btc_feebalance,
	   'btc_feebalance_myr' => $btc_feebalance_myr,
	   'btcJ_balance' => $btcJ_balance,
	   'btcJ_myrbalance' => $btcJ_myrbalance,
	   'bch_balance' => $bch_balance,
	   'bchC_balance' => $bchC_balance,
	   'bch_myrbalance' => $bch_myrbalance,
	   'bchC_myrbalance' => $bchC_myrbalance,
	   'bch_feebalance' => $bch_feebalance,
	   'bch_feebalance_myr' => $bch_feebalance_myr,
	   'bchJ_balance' => $bchJ_balance,
	   'bchJ_myrbalance' => $bchJ_myrbalance,
	   'ltc_balance' => $ltc_balance,
	   'ltcC_balance' => $ltcC_balance,
	   'ltc_myrbalance' => $ltc_myrbalance,
	   'ltcC_myrbalance' => $ltcC_myrbalance,
	   'ltc_feebalance' => $ltc_feebalance,
	   'ltc_feebalance_myr' => $ltc_feebalance_myr,
	   'ltcJ_balance' => $ltcJ_balance,
	   'ltcJ_myrbalance' => $ltcJ_myrbalance,
	   'doge_balance' => $doge_balance,
	   'dogeC_balance' => $dogeC_balance,
	   'doge_myrbalance' => $doge_myrbalance,
	   'dogeC_myrbalance' => $dogeC_myrbalance,
	   'doge_feebalance' => $doge_feebalance,
	   'doge_feebalance_myr' => $doge_feebalance_myr,
	   'dogeJ_balance' => $dogeJ_balance,
	   'dogeJ_myrbalance' => $dogeJ_myrbalance,
	   'dash_balance' => $dash_balance,
	   'dashC_balance' => $dashC_balance,
	   'dash_myrbalance' => $dash_myrbalance,
	   'dashC_myrbalance' => $dashC_myrbalance,
	   'dash_feebalance' => $dash_feebalance,
	   'dash_feebalance_myr' => $dash_feebalance_myr,
	   'dashJ_balance' => $dashJ_balance,
	   'dashJ_myrbalance' => $dashJ_myrbalance,
	   'xlm_balance' => $xlm_balance,
	   'xlmC_balance' => $xlmC_balance,
	   'xlm_myrbalance' => $xlm_myrbalance,
	   'xlmC_myrbalance' => $xlmC_myrbalance,
	   'xlm_feebalance' => $xlm_feebalance,
	   'xlm_feebalance_myr' => $xlm_feebalance_myr,
	   'xlmJ_balance' => $xlmJ_balance,
	   'xlmJ_myrbalance' => $xlmJ_myrbalance,
	   'eth_balance' => $eth_balance,
	   'ethC_balance' => $ethC_balance,
	   'eth_myrbalance' => $eth_myrbalance,
	   'ethC_myrbalance' => $ethC_myrbalance,
	   'eth_feebalance' => $eth_feebalance,
	   'eth_feebalance_myr' => $eth_feebalance_myr,
	   'ethJ_balance' => $ethJ_balance,
	   'ethJ_myrbalance' => $ethJ_myrbalance,
	   'life_balance' => $life_balance,
	   'lifeC_balance' => $lifeC_balance,
	   'life_myrbalance' => $life_myrbalance,
	   'lifeC_myrbalance' => $lifeC_myrbalance,
	   'life_feebalance' => $life_feebalance,
	   'life_feebalance_myr' => $life_feebalance_myr,
	   'lifeJ_balance' => $lifeJ_balance,
	   'lifeJ_myrbalance' => $lifeJ_myrbalance,
	   'xrp_balance' => $xrp_balance,
	   'xrpC_balance' => $xrpC_balance,
	   'xrp_myrbalance' => $xrp_myrbalance,
	   'xrpC_myrbalance' => $xrpC_myrbalance,
	   'xrp_feebalance' => $xrp_feebalance,
	   'xrp_feebalance_myr' => $xrp_feebalance_myr,
	   'xrpJ_balance' => $xrpJ_balance,
	   'xrpJ_myrbalance' => $xrpJ_myrbalance,
	   'total_users' => $total_users,
	   'network_fee' => $network_fee,
	   'totalXLM' => $totalXLM,
	   'dup_hash_xlm' => $dup_hash_xlm,
		'url' => $url,
		'adminJ_address' => $adminJ_address,
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

function getinfoXRP()
{
  $URL_INFO = PriceApi::where('crypto','XRP')->first();
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
    //dd($transaction); 
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

 
function btc_coinvata_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_coinvata'
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


function btc_transfer_coinvatatouser($useraddress,$cryptoamount) {
     
    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_coinvata',
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

function bch_moveuser($userlabel,$getuserlabel,$cryptoseller){
  
   $post = [
   'id' => 15,
   'label' => $userlabel,
   'label2' => $getuserlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoBCH());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   return $result;
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

function bch_coinvata_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_coinvata'
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


function bch_transfer_coinvatatouser($useraddress,$cryptoamount) { 

    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_coinvata',
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

function ltc_moveuser($userlabel,$getuserlabel,$cryptoseller){
  
   $post = [
   'id' => 15,
   'label' => $userlabel,
   'label2' => $getuserlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoLTC());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   return $result;
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

 
function ltc_coinvata_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_coinvata'
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


function ltc_transfer_coinvatatouser($useraddress,$cryptoamount) { 

    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_coinvata',
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
  //dd($transaction); 
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

function dash_moveuser($userlabel,$getuserlabel,$cryptoseller){
  
   $post = [
   'id' => 15,
   'label' => $userlabel,
   'label2' => $getuserlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoDASH());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   return $result;
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

 
function dash_coinvata_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_coinvata'
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


function dash_transfer_coinvatatouser($useraddress,$cryptoamount) { 

    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_coinvata',
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

function doge_moveuser($userlabel,$getuserlabel,$cryptoseller){
  
   $post = [
   'id' => 15,
   'label' => $userlabel,
   'label2' => $getuserlabel,
   'amount' => $cryptoseller
   ];

   $ch = curl_init(getinfoDOGE());
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   $result = curl_exec($ch);

   curl_close($ch);
   return $result;
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

 
function doge_coinvata_get_profit(){
  
    //GET BALANCE
    $post = [
        'id' => 4,
        'label' => 'usr_coinvata'
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

function doge_transfer_coinvatatouser($useraddress,$cryptoamount) { 

    //GET BALANCE
    $post = [
    'id' => 9,
    'label' => 'usr_coinvata',
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
      $wallet_balance = Ethereum::eth_getBalance($user->address,'latest',TRUE);    //eth down
   //$wallet_balance = 0;
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

  $uid = WalletAddress::where('uid', $id)->where('crypto','XRP')->first();

  //$client = new Client('https://s.altnet.rippletest.net:51234');
   $client = new Client('http://178.128.105.75:5005');

  $wallet_balance = 0;

 $response = $client->send('account_info', [
    'account' => $uid->address
  ]);

// Set balance if successful.
  if ($response->isSuccess()) {
    $data = $response->getResult();
    $wallet_balance = $data['account_data']['Balance'] / 1000000;

  }

  
  $updt = WalletAddress::where('uid', $id)->where('crypto', 'XRP')
  ->update([
   'available_balance' => $wallet_balance
 ]);

  return $wallet_balance;
}

function xrp_getbalance_myr($id) {

  $uid = WalletAddress::where('uid', $id)->where('crypto','XRP')->first();
  $current_price = PriceAPI::where('name', 'XRP')->first();

  $client = new Client('http://178.128.105.75:5005');

  $wallet_balance = null;

  $response = $client->send('account_info', [
    'account' => $uid->address
  ]);

// Set balance if successful.
  if ($response->isSuccess()) {
    $data = $response->getResult();
    $wallet_balance = $data['account_data']['Balance'];
  }


  $myr_balance = $current_price->price * ($wallet_balance/1000000);

  return $myr_balance;
}
/////////////////////////////////////////////////////////////////////
///  9. LIFE                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function life_getbalance($id){

$converter = new \Bezhanov\Ethereum\Converter();      
$user = WalletAddress::where('uid',$id)->where('crypto','LIFE')->first();
$wallet_balance = Ethereum::eth_getBalance($user->address,'latest',TRUE); 
//$wallet_balance = '0';    
////$float = number_format($wallet_balance, 0, '','');
$pari = new EthereumRPC('blappONE:bR4k82xIvhU7uI13E123n4ng2xIvTepiY417@bapp1.pinkexc.com', 443);
$erc20 = new ERC20($pari);
$token = $erc20->token("0xce61f5e6d1fe5a86e246f68aff956f7757282ef0");
/////dd($token);
$tokbal = $token->balanceOf($user->address);//"0x12E8962188B533E8FE53509B381dBfB31cc3fAA3");
$value = round($tokbal,8);  



      //$value = 0;


      $updt = WalletAddress::where('uid', $id)->where('crypto', 'LIFE')
            ->update([
                 'available_balance' => $value
            ]);
      
      return $value;

}
function life_getbalance_myr($id){
$converter = new \Bezhanov\Ethereum\Converter();  
  $uid = WalletAddress::where('uid',$id)->where('crypto','LIFE')->first();
  $current_price = PriceAPI::where('name','LIFE')->first(); 

  $wallet_balance = WalletAddress::where('uid',$id)->where('crypto','LIFE')->first();

  $myr_balance = $current_price->price * $wallet_balance->available_balance;

  return $myr_balance;

}




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

function transactions_xrp($label) {

      $specificAddress = WalletAddress::where('label',$label)->where('crypto','XRP')->first()->address; 
      $currencySelected = "XRP";
      $maxlimit = '1000000';
      $json_url = "https://data.ripple.com/v2/accounts/$specificAddress/payments?currency=$currencySelected&limit=$maxlimit";
        //get JSON data
      $json = file_get_contents($json_url);
      $data = json_decode($json);

      return $data->payments;
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
        $client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');
      $base58 = new \StephenHill\Base58();
      $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+-=[]\{}|:<>?,./";
      $charactersLength = strlen($characters);
      $data = '';
      for ($i = 0; $i < $charactersLength; $i++) {$data .= $characters[rand(0, $charactersLength - 1)];}
        $hashed = hash('sha512', $data);
      $convhashed = substr($hashed, 0, 32);
      $pass = $base58->encode($convhashed);

      $res = $client->send('wallet_propose', [
        'key_type' => 'secp256k1'
      ]);

    // $account = $request->address;
    // $response = $client->send('account_info', [
    //     'account' => $account
    // ]);
      $data = $res->isSuccess();

    // $passarr = array(['passphrase'=>$pass]);
    // $responseGen = $client->send('wallet_propose',$passarr);
    // if ($responseGen->isSuccess()) {
    //     $dataGen = $responseGen->getResult();
    //     $add_crypto = $dataGen['account_id'];
    //   //   $arrgen = array ([
    //   //     $genaddr = $dataGen['account_id'],
    //   //     $genseed = $dataGen['master_seed'],
    //   //     $genseedhex = $dataGen['master_seed_hex']
    //   // ]);
    //   //   dd($arrgen);
    //   //   return 'Generated';
    // }

    } elseif ($crypto == 'ETH') {
	$checkaddress = WalletAddress::where('label', $label)->where('crypto', 'LIFE')->count();
if($checkaddress != 0)
{
$add_crypto =  WalletAddress::where('label', $label)->where('crypto', 'LIFE')->first()->address;
}
else
{
 $add_crypto =  Ethereum::personal_newAccount('Pinkexc@22');
}
	
       
    } elseif ($crypto == 'LIFE') {
        	$checkaddress = WalletAddress::where('label', $label)->where('crypto', 'ETH')->count();
if($checkaddress != 0)
{
$add_crypto =  WalletAddress::where('label', $label)->where('crypto', 'ETH')->first()->address;
}
else
{
 $add_crypto =  Ethereum::personal_newAccount('Pinkexc@22');
}

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
    //$client = new Client('https://s.altnet.rippletest.net:51234');
     $client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');

        $amount_conv = strval(floatval($amount)*1000000); //1000000 equivalent to 1XRP
        $tx_type = "Payment";
        $account = WalletAddress::where('label','usr_admin')->where('crypto',$crypto)->first()->address; //rippleuser1 
        $acc_secret = WalletAddress::where('label','usr_admin')->where('crypto',$crypto)->first()->secret;
        $destination = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->address; //ripplebase address
        
        $currency = "XRP";

        //-------------------Payment Submission-----------------------------------
        $txParams = [
          'TransactionType' => $tx_type,
          'Account' => $account,
          'Destination' => $destination,
          'Amount' => $amount_conv,
          'Fee' => '10'
        ];
        $transaction = new \FOXRP\Rippled\Api\Transaction($txParams, $client);
        $responsePay = $transaction->submit(base64_decode($acc_secret));
        if ($responsePay->isSuccess()) {
          $dataSubmit = $responsePay->getResult();

          $txid = $dataSubmit['tx_json']['hash'];
          return $txid;
        }} elseif ($crypto == 'XLM') {
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
///////////////////////////////////////////////////////////
///////////sendfrom with command//////////////////////////
/////////////////////////////////////////////////////


function send_crypto_comment($crypto, $label, $address, $amount,$comment) {


    if ($crypto == 'BTC') {
        //btc

        $post = [
            'id' => 21,
            'label' => $label,
            'address' => $address,
            'amount' => $amount,
 	    'comment' => $comment
        ];


        $ch = curl_init(getinfoBTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

	$bal1 = getbalance('BTC', $label);

        curl_close($ch);
 
        return $txid;
    } elseif ($crypto == 'BCH') {

        //bch

        $post = [
            'id' => 21,
            'label' => $label,
            'address' => $address,
            'amount' => $amount,
'comment' => $comment
        ];


        $ch = curl_init(getinfoBCH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

        curl_close($ch);
	$bal1 = getbalance('BCH', $label);

        return $txid;
    } elseif ($crypto == 'LTC') {
        //LTC

        $post = [
            'id' => 21,
            'label' => $label,
            'address' => $address,
            'amount' => $amount,
'comment' => $comment
        ];


        $ch = curl_init(getinfoLTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

        curl_close($ch);

$bal1 = getbalance('LTC', $label);

        return $txid;
    } elseif ($crypto == 'DASH') {

        //dash

        $post = [
            'id' => 21,
            'label' => $label,
            'address' => $address,
            'amount' => $amount,
'comment' => $comment
        ];


        $ch = curl_init(getinfoDASH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

        curl_close($ch);

$bal1 = getbalance('DASH', $label);

        return $txid;
    } elseif ($crypto == 'DOGE') {

        //doge

        $post = [
            'id' => 21,
            'label' => $label,
            'address' => $address,
            'amount' => $amount,
'comment' => $comment
        ];


        $ch = curl_init(getinfoDOGE());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);

        curl_close($ch);

$bal1 = getbalance('DOGE', $label);

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

if(Auth::id()=="29285"){
$bal1 = getbalance('ETH', $label);
dd($bal1);
dd($transaction);
}

$bal1 = getbalance('ETH', $label);

       //return Ethereum::personal_sendTransaction($transaction,'P-HY,mUr)PfGQ9NW/BNs:+q3>)YLb+Q8uz"gq;(!*Avd*EQd');
	return Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');


   } elseif ($crypto == 'XRP') {
    //$client = new Client('https://s.altnet.rippletest.net:51234');
     $client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');

        $amount_conv = strval(floatval($amount)*1000000); //1000000 equivalent to 1XRP
        $tx_type = "Payment";
        $account = WalletAddress::where('label','usr_admin')->where('crypto',$crypto)->first()->address; //rippleuser1 
        $acc_secret = WalletAddress::where('label','usr_admin')->where('crypto',$crypto)->first()->secret;
        $destination = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->address; //ripplebase address
        
        $currency = "XRP";

        //-------------------Payment Submission-----------------------------------
        $txParams = [
          'TransactionType' => $tx_type,
          'Account' => $account,
          'Destination' => $destination,
          'Amount' => $amount_conv,
          'Fee' => '10'
        ];
        $transaction = new \FOXRP\Rippled\Api\Transaction($txParams, $client);
        $responsePay = $transaction->submit(base64_decode($acc_secret));
        if ($responsePay->isSuccess()) {
          $dataSubmit = $responsePay->getResult();

          $txid = $dataSubmit['tx_json']['hash'];

$bal1 = getbalance('XRP', $label);
          return $txid;
        }} elseif ($crypto == 'XLM') {
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
        $admin = WalletAddress::where('label','usr_pinkexc_fees')->where('crypto',$crypto)->first();
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

	$label = str_replace("\n", '', $label);

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

function get_label_crypto2($crypto, $address) {
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

	$label = str_replace("\n", '', $label);

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

getbalance($crypto, $label);
getbalance($crypto, $label2);

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

getbalance($crypto, $label);
getbalance($crypto, $label2);

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

getbalance($crypto, $label);
getbalance($crypto, $label2);

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

getbalance($crypto, $label);
getbalance($crypto, $label2);

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

getbalance($crypto, $label);
getbalance($crypto, $label2);

        return $result;
    } 
	elseif ($crypto == 'ETH') {
	$data = Ethereum::eth_getTransactionReceipt('0x6e15a39c9975d59b21732122fc8bc8a3d5a16705edd1a6de927d92798b23afed');
	//dd($data);
        $converter = new \Bezhanov\Ethereum\Converter();
        $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
        $admin = WalletAddress::where('label',$label2)->where('crypto',$crypto)->first();
        $from = $user->address;
        $to = $admin->address;
        $gas = '0x'.dec2hex('100000');
        $gasprice = Gasprice::where('id',1)->first();
        $normal = $gasprice->rapid;
	 if($normal == '0' || $normal == ''){$normal = 50;}

        $gasPriceData = $converter->toWei($normal, 'gwei');
        $gasPrice = '0x'.dec2hex($gasPriceData);
        $value = '0x'.dec2hex($converter->toWei($amount, 'ether'));
        //$value = '0x9184e72a';
        $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);
        $txid =  Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');

getbalance($crypto, $label);
getbalance($crypto, $label2);

        if($txid != '')
        {
            return $txid;
        }
        else
        {
            return null;
        }


    } elseif ($crypto == 'XLM') {
        $result = '';
        return $result;
    } elseif ($crypto == 'XRP') {
    //    $client = new Client('https://s.altnet.rippletest.net:51234');
     $client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');

        $amount_conv = strval(floatval($amount)*1000000); //1000000 equivalent to 1XRP
        $tx_type = "Payment";
        $account = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->address; //rippleuser1 
        $acc_secret = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->secret;
        $destination = WalletAddress::where('label',$label2)->where('crypto',$crypto)->first()->address; //ripple admin address
        
        $currency = "XRP";

        //-------------------Payment Submission-----------------------------------
        $txParams = [
          'TransactionType' => $tx_type,
          'Account' => $account,
          'Destination' => $destination,
          'Amount' => $amount_conv,
          'Fee' => '10'
        ];
        $transaction = new \FOXRP\Rippled\Api\Transaction($txParams, $client);
        $responsePay = $transaction->submit(base64_decode($acc_secret));
        if ($responsePay->isSuccess()) {
          $dataSubmit = $responsePay->getResult();

          $txid = $dataSubmit['tx_json']['hash'];

getbalance($crypto, $label);
getbalance($crypto, $label2);

          if($txid != '')
          {
            return $txid;
          }
          else
          {
            return null;
          }
          
        }    } 
      elseif ($crypto == 'LIFE') {
      //$pari = new EthereumRPC('blappONE:bR4k82xIvhU7uI13E123n4ng2xIvTepiY417@bapp1.pinkexc.com', 443);
      //$erc20 = new ERC20($pari);
      $contract = "0xce61f5e6D1fE5a86E246F68AFF956f7757282eF0"; // ERC20 contract address
      $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->address; // Sender's Ethereum account
      $admin =  WalletAddress::where('label',$label2)->where('crypto',$crypto)->first()->address; // Recipient's Ethereum account
      //$amountLIFE = strval(floatval($amount)+0.00001);
      $amountLIFE = $amount;

      // Grab instance of ERC20_Token class
      $token = $erc20->token($contract);
      // First argument is admin/recipient of this transfer
      // Second argument is the amount of tokens that will be sent
      $data = $token->encodedTransferData($admin, $amountLIFE);
      $transaction = $pari->personal()->transaction($user, $contract) // from $payer to $contract address
        ->amount("0") // Amount should be ZERO
        ->data($data); // Our encoded ERC20 token transfer data from previous step

      // Send transaction with ETH account passphrase
      $txId = $transaction->send("Pinkexc@22"); // Replace "secret" with actual passphrase of SENDER's ethereum account

getbalance($crypto, $label);
getbalance($crypto, $label2);

      if($txId != ''){
      $id = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->uid;
      $update_bal1 = life_getbalance($id);
      return $txId;}
      else{return null;}
    }  
	else {
        $result = null;
        return $result;
    }
}





////////////////////////////////////////////////////////////////////
////////////////////////////move with comment////////////////////////
//////////////////////////////////////////////////////////////////////
function move_crypto_comment($crypto, $label, $label2, $amount, $comment) {
    if ($crypto == 'BTC') {
        $post = [
            'id' => 20,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount,
	    'comment' => $comment
        ];


        $ch = curl_init(getinfoBTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

	 getbalance($crypto, $label);
	 getbalance($crypto, $label2);

        curl_close($ch);

        return $result;
    } elseif ($crypto == 'BCH') {
        $post = [
            'id' => 20,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount,
	    'comment' => $comment
        ];


        $ch = curl_init(getinfoBCH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

	 getbalance($crypto, $label);
	 getbalance($crypto, $label2);


        curl_close($ch);

        return $result;
    } elseif ($crypto == 'LTC') {
        $post = [
            'id' => 20,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount,
		'comment' => $comment
        ];


        $ch = curl_init(getinfoLTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

 getbalance($crypto, $label);
	 getbalance($crypto, $label2);


        return $result;
    } elseif ($crypto == 'DASH') {

        $post = [
            'id' => 20,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount,
	'comment' => $comment
        ];


        $ch = curl_init(getinfoDASH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);

 getbalance($crypto, $label);
	 getbalance($crypto, $label2);


        return $result;
    } elseif ($crypto == 'DOGE') {
        $post = [
            'id' => 20,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount,
	'comment' => $comment
        ];


        $ch = curl_init(getinfoDOGE());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);

        curl_close($ch);
 getbalance($crypto, $label);
	 getbalance($crypto, $label2);


       // return $result;
//$return = $crypto.'-'.$label.'-'.$label2.'-'.$amount.'-'.$comment;
return $result;
    } 
	elseif ($crypto == 'ETH') {
	//$data = Ethereum::eth_getTransactionReceipt('0x6e15a39c9975d59b21732122fc8bc8a3d5a16705edd1a6de927d92798b23afed');
	//dd($data);
        $converter = new \Bezhanov\Ethereum\Converter();
        $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
        $admin = WalletAddress::where('label',$label2)->where('crypto',$crypto)->first();
        $from = $user->address;
        $to = $admin->address;
        $gas = '0x'.dec2hex('100000');
        $gasprice = Gasprice::where('id',1)->first();
        $fast = $gasprice->rapid;

	if($fast == '0' || $fast == ''){$fast = 50;}
        $amount = getbalance('ETH', $label);
        $gasPriceData = $converter->toWei($fast, 'gwei');
        $gasPrice = '0x'.dec2hex($gasPriceData);
        $value = '0x'.dec2hex($converter->toWei($amount, 'ether'));
        //$value = '0x9184e72a';
	$transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);
        $txid =  Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');
        
 	 getbalance($crypto, $label);
	 getbalance($crypto, $label2);


        if($txid != '')
        {
            return $txid;
        }
        else
        {
            return null;
        }


    } elseif ($crypto == 'XLM') {
        $result = '';
        return $result;
    } elseif ($crypto == 'XRP') {
    //    $client = new Client('https://s.altnet.rippletest.net:51234');
     $client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');

        $amount_conv = strval(floatval($amount)*1000000); //1000000 equivalent to 1XRP
        $tx_type = "Payment";
        $account = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->address; //rippleuser1 
        $acc_secret = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->secret;
        $destination = WalletAddress::where('label',$label2)->where('crypto',$crypto)->first()->address; //ripple admin address
        
        $currency = "XRP";

        //-------------------Payment Submission-----------------------------------
        $txParams = [
          'TransactionType' => $tx_type,
          'Account' => $account,
          'Destination' => $destination,
          'Amount' => $amount_conv,
          'Fee' => '10'
        ];
        $transaction = new \FOXRP\Rippled\Api\Transaction($txParams, $client);
        $responsePay = $transaction->submit(base64_decode($acc_secret));
        if ($responsePay->isSuccess()) {
          $dataSubmit = $responsePay->getResult();

          $txid = $dataSubmit['tx_json']['hash'];

 getbalance($crypto, $label);
	 getbalance($crypto, $label2);


          if($txid != '')
          {
            return $txid;
          }
          else
          {
            return null;
          }
          
        }    } 
      elseif ($crypto == 'LIFE') {

 return null;

    ///  $pari = new EthereumRPC('blappONE:bR4k82xIvhU7uI13E123n4ng2xIvTepiY417@bapp1.pinkexc.com', 443);
    ///  $erc20 = new ERC20($pari);
    ///  $contract = "0xce61f5e6D1fE5a86E246F68AFF956f7757282eF0"; // ERC20 contract address
    ///  $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->address; // Sender's Ethereum account
    ///  $admin =  WalletAddress::where('label',$label2)->where('crypto',$crypto)->first()->address; // Recipient's Ethereum account
        ///   $amountLIFE = $amount;


      // Grab instance of ERC20_Token class
     /// $token = $erc20->token($contract);
      // First argument is admin/recipient of this transfer
      // Second argument is the amount of tokens that will be sent


     /// $data = $token->encodedTransferData($admin, $amountLIFE);

     /// $transaction = $pari->personal()->transaction($user, $contract) // from $payer to $contract address

     ///   ->amount("0") // Amount should be ZERO
      ///  ->data($data); // Our encoded ERC20 token transfer data from previous step


      // Send transaction with ETH account passphrase
    ///  $txId = $transaction->send("Pinkexc@22"); // Replace "secret" with actual passphrase of SENDER's ethereum account

 		///getbalance($crypto, $label);
	 	///getbalance($crypto, $label2);


     /// if($txId != ''){
     /// $id = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->uid;
     /// $update_bal1 = life_getbalance($id);
     /// return $txId;
	///}
      ///else{return null;}



    }  
	else {
        $result = null;
        return $result;
    }
}





/////////////////////////////////////////////////////////////////////
///  GET BALANCE ALL          ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function getbalanceAll($crypto) {
	if ($crypto == 'BTC') {
        //GET BALANCE
        $post = [
            'id' => 23
        ];

        $ch = curl_init(getinfoBTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    }
	elseif ($crypto == 'BCH') {
        //GET BALANCE
        $post = [
            'id' => 23
        ];

        $ch = curl_init(getinfoBCH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    }
	elseif ($crypto == 'LTC') {
        //GET BALANCE
        $post = [
            'id' => 23
        ];

        $ch = curl_init(getinfoLTC());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    }
	elseif ($crypto == 'DASH') {
        //GET BALANCE
        $post = [
            'id' => 23
        ];

        $ch = curl_init(getinfoDASH());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    }
	elseif ($crypto == 'DOGE') {
        //GET BALANCE
        $post = [
            'id' => 23
        ];

        $ch = curl_init(getinfoDOGE());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $wallet_balance = curl_exec($ch);

        curl_close($ch);
        return $wallet_balance;
    }
	 else {
    $wallet_balance = null;
    return $wallet_balance;
	}
	
}



/////////////////////////////////////////////////////////////////////
///  ADMIN GET BALANCE             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function test_getbalance($crypto, $label) {


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



}


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

	$updt = WalletAddress::where('label', $label)->where('crypto', 'BTC')
            ->update([
                 'available_balance' => $wallet_balance
            ]);

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

	$updt = WalletAddress::where('label', $label)->where('crypto', 'BCH')
            ->update([
                 'available_balance' => $wallet_balance
            ]);

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

	$updt = WalletAddress::where('label', $label)->where('crypto', 'LTC')
            ->update([
                 'available_balance' => $wallet_balance
            ]);


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

	$updt = WalletAddress::where('label', $label)->where('crypto', 'DASH')
            ->update([
                 'available_balance' => $wallet_balance
            ]);


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

	$updt = WalletAddress::where('label', $label)->where('crypto', 'DOGE')
            ->update([
                 'available_balance' => $wallet_balance
            ]);


        curl_close($ch);
        return $wallet_balance;
    } elseif ($crypto == 'ETH') {
$converter = new \Bezhanov\Ethereum\Converter();      
$user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
    $wallet_balance = Ethereum::eth_getBalance($user->address,'latest',TRUE);     //eth down  
//$wallet_balance = 0;  
   $float = number_format($wallet_balance, 0, '','');
      $value = round($converter->fromWei($float, 'ether'),5);

$updt = WalletAddress::where('label', $label)->where('crypto', 'ETH')
            ->update([
                 'available_balance' => $value
            ]);


      return $value;

  } elseif ($crypto == 'XLM') {
    $wallet_balance = '';
    return $wallet_balance;
} elseif ($crypto == 'XRP') {

$uid = WalletAddress::where('label',$label)->where('crypto','XRP')->first();

  //$client = new Client('https://s.altnet.rippletest.net:51234');
   $client = new Client('http://178.128.105.75:5005'); // Error

  $wallet_balance = null;
 
  $response = $client->send('account_info', [
    'account' => $uid->address
  ]);

// Set balance if successful.
  if ($response->isSuccess()) {
    $data = $response->getResult();
    $wallet_balance = $data['account_data']['Balance'];
  }

$updt = WalletAddress::where('label', $label)->where('crypto', 'XRP')
            ->update([
                 'available_balance' => $wallet_balance
            ]);


    return $wallet_balance;
} elseif ($crypto == 'LIFE') {
    $converter = new \Bezhanov\Ethereum\Converter();      
$user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
      $wallet_balance = Ethereum::eth_getBalance($user->address,'latest',TRUE);     //eth down
//$wallet_balance = '0';    
   //$float = number_format($wallet_balance, 0, '','');
$pari = new EthereumRPC('blappONE:bR4k82xIvhU7uI13E123n4ng2xIvTepiY417@bapp1.pinkexc.com', 443);    //eth down
	    $erc20 = new ERC20($pari);    //eth down
	    $token = $erc20->token("0xce61f5e6d1fe5a86e246f68aff956f7757282ef0");    //eth down
	    //dd($token);
	    $tokbal = $token->balanceOf($user->address);//"0x12E8962188B533E8FE53509B381dBfB31cc3fAA3");    //eth down

      $value = round($tokbal,8);    //eth down
//$value = 0;

$updt = WalletAddress::where('label', $label)->where('crypto', 'LIFE')
            ->update([
                 'available_balance' => $value
            ]);


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
    $maximum_price = round((1000 / $response_price['market_data']['current_price']['myr']),8);

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
 else if ($convertTo == "XRP") 
 {

  $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin?localization=false', false);
  $response = json_decode($get_data, true);

  $currentdata = $response['market_data']['current_price']['xrp'];

  $currentprice = $currentdata - ($currentdata * $rate);
  $displayprice = $currentdata;

}

else if ($convertTo == "XLM") 
 {

  $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin?localization=false', false);
  $response = json_decode($get_data, true);

  $currentdata = $response['market_data']['current_price']['xlm'];

  $currentprice = $currentdata - ($currentdata * $rate);
  $displayprice = $currentdata;

}

}
else if($convertFrom == "LTC")
{

  $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/litecoin?localization=false', false);
  $response_price = json_decode($get_data_price, true);

  $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
  $maximum_price = round((1000 / $response_price['market_data']['current_price']['myr']),8);


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
  else if ($convertTo == "XRP") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/litecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xrp'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "XLM") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/litecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xlm'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
}
else if($convertFrom == "DOGE")
{

  $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
  $response_price = json_decode($get_data_price, true);

  $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
  $maximum_price = round((1000 / $response_price['market_data']['current_price']['myr']),8);

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
  else if ($convertTo == "XRP") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xrp'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "XLM") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xlm'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
}
else if($convertFrom == "DASH")
{

  $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
  $response_price = json_decode($get_data_price, true);

  $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
  $maximum_price = round((1000 / $response_price['market_data']['current_price']['myr']),8);


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
  else if ($convertTo == "XRP") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xrp'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "XLM") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xlm'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
}
else if($convertFrom == "BCH")
{

  $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin-cash?localization=false', false);
  $response_price = json_decode($get_data_price, true);

  $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
  $maximum_price = round((1000 / $response_price['market_data']['current_price']['myr']),8);


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
  else if ($convertTo == "XRP") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin-cash?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xrp'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "XLM") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin-cash?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xlm'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
}
else if($convertFrom == "ETH")
{

  $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ethereum?localization=false', false);
  $response_price = json_decode($get_data_price, true);

  $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
  $maximum_price = round((1000 / $response_price['market_data']['current_price']['myr']),8);


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
  else if ($convertTo == "XRP") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ethereum?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xrp'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "XLM") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ethereum?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xlm'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
}
else if($convertFrom == "XRP")
{

  $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ripple?localization=false', false);
  $response_price = json_decode($get_data_price, true);

  $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
  $maximum_price = round((1000 / $response_price['market_data']['current_price']['myr']),8);


  if ($convertTo == "BTC") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ripple?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['btc'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "LTC") 
  {
    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ripple?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['ltc'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "DOGE") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ripple?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['btc'];

    $get_data1 = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response1 = json_decode($get_data1, true);

    $currentdata1 = $response1['market_data']['current_price']['btc'];

    $currentprice = ($currentdata / $currentdata1) - (($currentdata / $currentdata1) * $rate);
    $displayprice = ($currentdata / $currentdata1);

  }
  else if ($convertTo == "DASH") 
  {
    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ripple?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['btc'];

    $get_data1 = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
    $response1 = json_decode($get_data1, true);

    $currentdata1 = $response1['market_data']['current_price']['btc'];

    $currentprice = ($currentdata / $currentdata1) - (($currentdata / $currentdata1) * $rate);
    $displayprice = ($currentdata / $currentdata1);

  }
  else if ($convertTo == "BCH") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ripple?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['bch'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "ETH") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ripple?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['eth'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "XLM") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/ripple?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xlm'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
}
else if($convertFrom == "XLM")
{

  $get_data_price = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/stellar?localization=false', false);
  $response_price = json_decode($get_data_price, true);

  $minimum_price = round((10 / $response_price['market_data']['current_price']['myr']),8);
  $maximum_price = round((1000 / $response_price['market_data']['current_price']['myr']),8);


  if ($convertTo == "BTC") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/stellar?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['btc'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "LTC") 
  {
    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/stellar?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['ltc'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "DOGE") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/stellar?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['btc'];

    $get_data1 = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dogecoin?localization=false', false);
    $response1 = json_decode($get_data1, true);

    $currentdata1 = $response1['market_data']['current_price']['btc'];

    $currentprice = ($currentdata / $currentdata1) - (($currentdata / $currentdata1) * $rate);
    $displayprice = ($currentdata / $currentdata1);

  }
  else if ($convertTo == "DASH") 
  {
    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/stellar?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['btc'];

    $get_data1 = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/dash?localization=false', false);
    $response1 = json_decode($get_data1, true);

    $currentdata1 = $response1['market_data']['current_price']['btc'];

    $currentprice = ($currentdata / $currentdata1) - (($currentdata / $currentdata1) * $rate);
    $displayprice = ($currentdata / $currentdata1);

  }
  else if ($convertTo == "BCH") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/stellar?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['bch'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "ETH") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/stellar?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['eth'];

    $currentprice = $currentdata - ($currentdata * $rate);
    $displayprice = $currentdata;

  }
  else if ($convertTo == "XRP") 
  {

    $get_data = callAPI('GET', 'https://api.coingecko.com/api/v3/coins/stellar?localization=false', false);
    $response = json_decode($get_data, true);

    $currentdata = $response['market_data']['current_price']['xrp'];

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
/////SEND EMAIL SHOP//////////////////
//////////////////////////////////////////


function send_email_releaseShop($receipt_id,$order_id,$user_id,$type,$fees,$catuser)
{ 
	$Userdata = User::where('id',$user_id)->first();
	$shopOrder = ShopOrder::where('id',$order_id)->first();
	$shopPayment = ShopPayment::where('orderid',$shopOrder->orderid)->first();
	$shopRelease = ShopRelease::where('id',$shopOrder->releaseid)->first();
	
	 $orderP = ShopOrder::join('products','products.id','=','orders.productid')->where('orders.releaseid',$shopOrder->releaseid)->selectRaw('orders.quantity as quantity,products.title as title,products.price as price,orders.postage as postage')->get();
	  
	$txt_no = sprintf('%07d',$receipt_id);
	
	$currentrate = json_decode($shopPayment->allcurrentprice,true);
	if($catuser=='seller'){  // seller part
		
	if($shopRelease->paymethodS=='Online Banking'){  //release by online banking
		$paymethod = "Online Banking";
		$scharge = $fees;
		$coins = "";
		$crypto_rate = "";
		$cryptos = "";
		$stotal = number_format($shopRelease->myramountS+$fees,2);
		$ctotal = number_format($shopRelease->myramountS,2);
		}
	else{		//release by crypto
	
		$paymethod = "Digital Currency";
		$scharge = $fees;
		$coins = $shopRelease->paymethodS.'/';
		$crypto_rate = $currentrate[$shopRelease->paymethodS];
		$cryptos = $shopRelease->paymethodS;
		$fee_crypto = $fees/$currentrate[$shopRelease->paymethodS];
		$stotal = number_format($shopRelease->cryptoS+$fee_crypto,5).'/'.number_format($shopRelease->myramountS+$fees,2);
		$ctotal = number_format($shopRelease->cryptoS,5).'/'.number_format($shopRelease->myramountS,2);
		}
		
	}else{      //buyer part
		
	if($shopRelease->paymethodS=='Online Banking'){    //release by online banking
		$paymethod = "Online Banking";
		$scharge = $fees;
		$coins = "";
		$crypto_rate = "";
		$cryptos = "";
		$stotal = number_format($shopRelease->myramountB+$fees,2);
		$ctotal = number_format($shopRelease->myramountB,2);
		}
	else{    //release by crypto
	
		$paymethod = "Digital Currency";
		$scharge = $fees;
		$coins = $shopRelease->paymethodB.'/';
		$crypto_rate = $currentrate[$shopRelease->paymethodB];
		$cryptos = $shopRelease->paymethodB;
		$fee_crypto = $fees/$currentrate[$shopRelease->paymethodB];
		$stotal = number_format($shopRelease->cryptoB+$fee_crypto,5).'/'.number_format($shopRelease->myramountB+$fees,2);
		$ctotal = number_format($shopRelease->cryptoB,5).'/'.number_format($shopRelease->myramountB,2);
		}
	}	
		
     
    $to=$Userdata->email; //change to ur mail address
    $subject="Colony Shoppe | ".ucfirst($type)." Invoice";
     

	$logo_rep = asset('assets/assets/images/dashboard/pinkexc_logo_new_pink.png');

    $htmlContent = '
    <html><body>


    <center>
    <div style="max-width:800px;padding: 20px;border: 1px solid #eee;box-shadow: 0 0 10px rgba(0, 0, 0, .15);font-size: 14px;font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;color: #555;">
    <table class="table table-bordered" width="100%">
      <tr>
        <td colspan="5" align="center">  
			<img src="'.$logo_rep.'" style="width:200px;"> 
        </td> 
      </tr> 
      <tr>
        <td colspan="5" align="center">   
           <b>BLOCKCHAIN AND CRYPTOCURRENCY SOLUTION</b>  <br>
		  <b>Pinkexc (M). Sdn. Bhd. (1194957-P)</b><br>
		   1, Jln Meru Bestari A14, Medan Meru Bestari,<br> 30020 Ipoh, Perak Darul Ridzuan, Malaysia <br><br> Phone: +605 525 1866 Email: admin@pinkexc.com
        </td> 
      </tr>
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr>
      <tr>
        <td colspan="5" align="center">     
          Service Type : Colony Shoppe
        </td> 
      </tr>
      <tr>
        <td colspan="5" align="center">   
          TxnID : 1
        </td> 
      </tr>
      <tr>
        <td>   
          Receipt No: 
        </td>
        <td align="center">   
         '.$txt_no.'
        </td> 
        <td>    
		
        </td>
        <td>   
         Payment Method:
        </td>
        <td align="center">   
          '.$paymethod.'
        </td> 
      </tr>
      <tr>
        <td>   
          Date: 
        </td>
        <td align="center">   
		'.$shopRelease->updated_at.'
        </td>
        <td>    
		
        </td>
        <td>   
          Txn Type:
        </td> 
        <td align="center">   
          '.$type.'
        </td>
      </tr>
      <tr>
        <td>   
          Digital Currency: 
        </td>
        <td align="center">   
         '.$cryptos.'
        </td>
        <td>    
		
        </td>
        <td>   
          Rate (RM):
        </td> 
        <td align="center">   
          '.$crypto_rate.'
        </td>
      </tr> 
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr> 
      <tr>
        <td align="center">   
           Item(s)
        </td>
        <td align="center">   
           Price('.$coins.'MYR)
        </td>
        <td align="center">   
           Qty
        </td>
        <td align="center">   
           Total('.$coins.'MYR)
        </td>
        <td align="center">   
           Total Payment('.$coins.'MYR)
        </td> 
      </tr> 
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr>';
	  
	   if($shopRelease->uid==$user_id){
	  foreach($orderP as $data){
      $htmlContent .= '<tr>
        <td>   
		 '.$data->title.'
        </td>
        <td align="center">   
           '.$data->price.'
        </td>
        <td align="center">   
           '.$data->quantity.'
        </td>
        <td align="center">   
           '.$data->price*$data->quantity.'
        </td>
        <td align="center">   
          '.$data->price*$data->quantity.'
        </td> 
      </tr>';
	  }
	  }
	  
	  if(($shopRelease->post_product=='1' && $catuser=='seller') || ($shopRelease->post_product=='0' && $catuser=='buyer')){
	  $htmlContent .= '<tr>
        <td>   
		 Shipping Fee
        </td>
        <td align="center">   
           -
        </td>
        <td align="center">   
          -
        </td>
        <td align="center">   
           '.$shopOrder->postage.'
        </td>
        <td align="center">   
          '.$shopOrder->postage.'
        </td> 
      </tr>';  
	  }
	  
      $htmlContent .= '<tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr>
       <tr>
        <td colspan="2">    
        </td> 
        <td>   
          Subtotal:
        </td> 
        <td align="center">   
          
        </td>
        <td align="center">   
          '.$stotal.' 
        </td>
      </tr>
       <tr>
        <td colspan="2">    
        </td> 
        <td>   
          Service Charge:
        </td> 
        <td align="center">   
          
        </td>
        <td align="center">   
          '.$scharge.'
        </td>
      </tr>
	  <tr>
        <td colspan="2" >  
        </td> 
        <td colspan="3" >   
          <hr size="3">
        </td> 
      </tr>
       <tr>
        <td colspan="2">   
        </td> 
        <td>   
          Total Payment:
        </td> 
        <td align="center">   
          
        </td>
        <td align="center">   
          '.$ctotal.'
        </td>
      </tr>   
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr> 
    </table> 
	<table class="table table-bordered" width="100%">
      <tr>
        <td align="right">   
          Notes:
        </td> 
        <td colspan="2"> </td> 
      </tr> 
      <tr>
        <td width="15%" align="right"> 1.</td> 
        <td width="80%">    
		   All confirmed buy, sell, deposits and withdrawal on Colony are final. 
        </td> 
        <td width="5%"> </td> 
      </tr>
      <tr>
        <td align="right"> 2.</td> 
        <td>    
		  Please notifiy is if any discrepancy within seven (7) days otherwise this receipt will be
        </td> 
        <td> </td> 
      </tr>
      <tr>
        <td> </td> 
        <td>    
		  considered as correct.</font> 
        </td> 
        <td> </td> 
      </tr>
      <tr>
        <td colspan="3" align="center">    
		  <b>***** This is computer generated receipt, no signature required *****</b>
        </td>   
      </tr>
    </table>

    </div>
    </center>

    </body></html>

    ';
    $headers = 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
    $headers .= "From: no-reply@pinkexc.com "; 

           mail($to, $subject, $htmlContent, $headers);  // Mail Function

       }
	   
//////////////////////////////////////////
/////SEND EMAIL INSTANT SELL/BUY//////////////////
//////////////////////////////////////////


function send_email_instant($receipt_id,$instant_id,$user_id,$type,$crypto,$fees)
{ 
	$Userdata = User::where('id',$user_id)->first();
	if($type=='sell' && $crypto!='XLM'){
	$instant = Pinkexcsell::where('id',$instant_id)->first();
	}
        if($type=='sell' && $crypto=='XLM'){
	$instant = StellarPinkexcsell::where('id',$instant_id)->first();
	}

	if($type=='buy' && $crypto!='XLM'){
	$instant = Pinkexcbuy::where('id',$instant_id)->first();
	}
        if($type=='buy' && $crypto=='XLM'){
	$instant = StellarPinkexcbuy::where('id',$instant_id)->first();
	}

	$priceapi = PriceApi::where('crypto',$crypto)->first();
	  
    $txt_no = sprintf('%07d',$receipt_id); 
    
     $to=$Userdata->email; //change to ur mail address
    $subject="Colony | Instant ".ucfirst($type)." Invoice";
    if($type=='sell'){
    $wbuy = number_format(round($instant->myr_amount,2),2);
    $wsell = '-';
    }
    if($type=='buy'){
    $wbuy = '-';
    $wsell = $instant->crypto_amount;
    }

	$logo_rep = asset('assets/assets/images/dashboard/pinkexc_logo_new_pink.png');

    $htmlContent = '
    <html><body>


    <center>
    <div style="max-width:800px;padding: 20px;border: 1px solid #eee;box-shadow: 0 0 10px rgba(0, 0, 0, .15);font-size: 14px;font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;color: #555;">
    <table class="table table-bordered" width="100%">
      <tr>
        <td colspan="5" align="center">  
			<img src="'.$logo_rep.'" style="width:200px;"> 
        </td> 
      </tr> 
      <tr>
        <td colspan="5" align="center">   
           <b>BLOCKCHAIN AND CRYPTOCURRENCY SOLUTION</b>  <br>
		  <b>Pinkexc (M). Sdn. Bhd. (1194957-P)</b><br>
		   1, Jln Meru Bestari A14, Medan Meru Bestari,<br> 30020 Ipoh, Perak Darul Ridzuan, Malaysia <br><br> Phone: +605 525 1866 Email: admin@pinkexc.com
        </td> 
      </tr>
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr>
      <tr>
        <td colspan="5" align="center">     
          Service Type : Colony DAX
        </td> 
      </tr>
      <tr>
        <td colspan="5" align="center">   
          TxnID : '.$instant->txid.'
        </td> 
      </tr>
      <tr>
        <td>   
          Receipt No : 
        </td>
        <td align="center">   
         '.$txt_no.'
        </td>
        <td colspan="3" align="center">   
          Digital Currency '.strtoupper($type).' Receipt
        </td> 
      </tr>
      <tr>
        <td>   
          Date : 
        </td>
        <td align="center">   
		'.$instant->updated_at.'
        </td>
        <td colspan="2" align="center">   
          Txn Type:
        </td> 
        <td align="center">   
          '.strtoupper($type).'
        </td>
      </tr>
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr>
      <tr>
        <td width="20%">   
          Digital Currency : 
        </td>
        <td align="center" width="30%">   
          '.strtoupper($priceapi->name).'
        </td>
        <td width="20%">   
          Rate:
        </td> 
        <td align="center" width="15%">   
          RM
        </td>
        <td align="center" width="15%">   
          '.number_format(round($instant->rate,2),2).'
        </td>
      </tr>
       <tr>
        <td>   
          Amount : 
        </td>
        <td align="center">   
          '.$instant->crypto_amount.'
        </td>
        <td>   
          RM:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
		'.number_format(round($instant->myr_amount,2),2).'
        </td>
      </tr>
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr>
       <tr>
        <td colspan="2">    
        </td> 
        <td>   
          Total We Buy:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
          '.$wbuy.' 
        </td>
      </tr>
       <tr>
        <td colspan="2">    
        </td> 
        <td>   
          Total We Sell:
        </td> 
        <td align="center">   
          DC
        </td>
        <td align="center">   
          '.$wsell.'
        </td>
      </tr>
       <tr>
        <td colspan="2">   
        </td> 
        <td>   
          Subtotal:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
          '.number_format(round($instant->myr_amount,2),2).'
        </td>
      </tr>
       <tr>
        <td colspan="2">    
        </td> 
        <td>   
          Fees:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
          '.$fees.'
        </td>
      </tr>
       <tr>
        <td colspan="2">   
        </td> 
        <td>   
          Other:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
          -
        </td>
      </tr>
       <tr>
        <td colspan="2">   
        </td> 
        <td>   
          Total:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
          '.number_format(round($instant->myr_amount,2),2).'
        </td>
      </tr>
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr> 
    </table> 
	<table class="table table-bordered" width="100%">
      <tr>
        <td align="right">   
          Notes:
        </td> 
        <td colspan="2"> </td> 
      </tr> 
      <tr>
        <td width="15%" align="right"> 1.</td> 
        <td width="80%">    
		   All confirmed buy, sell, deposits and withdrawal on Colony are final. 
        </td> 
        <td width="5%"> </td> 
      </tr>
      <tr>
        <td align="right"> 2.</td> 
        <td>    
		  Please notifiy is if any discrepancy within seven (7) days otherwise this receipt will be
        </td> 
        <td> </td> 
      </tr>
      <tr>
        <td> </td> 
        <td>    
		  considered as correct.</font> 
        </td> 
        <td> </td> 
      </tr>
      <tr>
        <td colspan="3" align="center">    
		  <b>***** This is computer generated receipt, no signature required *****</b>
        </td>   
      </tr>
    </table>

    </div>
    </center>

    </body></html>

    ';

    $admin_mail = settings('sales_email');

    $headers = 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
    $headers .= "From: $admin_mail "; 

           mail($to, $subject, $htmlContent, $headers);  // Mail Function

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
    <img src="https://colony.pinkexc.com/assets/homepage/images/logo-black.png" style="width:150px;">
    </td> 
    <td colspan="2">
    P'.$invoice_no.''.$t.' <br>
    '.Carbon::now()->format('d-m-Y H:i:s').' <br>

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

    no-reply@pinkexc.com
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
    <h5> Note : The displayed price does not include 5% service charge. </h5>

    </div>
    </center>

    </body></html>

    ';
    $headers = 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
    $headers .= "From: no-reply@pinkexc.com "; 

           mail($to, $subject, $htmlContent, $headers);  // Mail Function
       }


function send_email_process_coinvata($id,$from,$to,$user_id)
{

    $invoice_no = $id;
    $txt_no = sprintf('%07d',$invoice_no); 
    $t=time();
	$crypto_to= $to;
    $Userdata = User::where('id',$user_id)->first();
    $to=$Userdata->email;  //change to ur mail address
    $subject="Colony | Coinvata";
   // $message =  file_get_contents('https://www.pinkexc.online/sources/template.php/?&b=edit&id='.$id); /* Your Template*/


    $htmlContent = "
    <html>
                    <head>
                    <title>Coinvata</title>
                    <style>
                    p.message {
                        color:#000000;
                    }
                    p.signature {
                        font-size: 11px;
                        color:#C3C3C3;
                    }
                    </style>
                    </head>
                    <body>
                    <p class='message'>Dear ".$Userdata->username.",</p>
                    <p class='message'>Your coinvata request from ".$from." to ".$crypto_to." is currently being process, please refer coinvata history page for more detail.</p>
                    <br/>
                    <br/>
                    
                    <p class='signature'>1, Jln Meru Bestari A14, Medan Meru Bestari, 30020 Ipoh, Perak.
                    <br />
                    Phone: +60 (5) 5251866
                    <br />
                    Email: no-reply@pinkexc.com 
                    <br />
                    Official Website: https://colony.pinkexc.com
                    <br />
                    Powered by Pinkexc (M) Sdn. Bhd.
                    </p>
                    
                    </body>
                    </html>
    ";
    $headers = 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
    $headers .= "From: no-reply@pinkexc.com "; 

           mail($to, $subject, $htmlContent, $headers);  // Mail Function
       }

///////////////////////////////////////////////
/////////////////// email support /////////////
///////////////////////////////////////////////
    
         
       function send_email_ticket($to, $subject, $name, $message,$message2){
  $setting = Setting::first();
//$img = 'https://colony.pinkexc.com/assets/homepage/images/logo-white.png';

  $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
  $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $template = $setting->template_ticket;
  $mm = str_replace("{{name}}",$name,$template);
  $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
  $url = str_replace("{{url}}",$message2, $supportemail);
  $message = str_replace("{{message}}",$message,$url);

  mail($to, $subject, $message, $headers);

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
    $fast = $fast1+10;
    
        $datamsg = response()->json([
            'normal' => $normal,
        'fast' => $fast
        ]);

        return $datamsg->content();

}


function send_email_toadmin($to, $subject,$name,$message,$orderid,$crypto){
  $setting = Setting::first();

  $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
  $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $template = $setting->template_sendtoadmin;
  $mm = str_replace("{{name}}",$name,$template);
  $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
  $url = str_replace("{{url}}",$setting->url.'admin/instant_buy/edit/'.$crypto.'/'.$orderid, $supportemail);
  $message = str_replace("{{message}}",$message,$url);


  $result = mail($to, $subject,$message, $headers);
return $result;
}







function send_email_touser($to, $subject,$name,$message,$orderid,$crypto){
  $setting = Setting::first();

  $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
  $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $template = $setting->template_sendtouser;
  $mm = str_replace("{{name}}",$name,$template);
  $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
  $url = str_replace("{{url}}",$setting->url.'admin/instant_buy/edit/'.$crypto.'/'.$orderid, $supportemail);
  $message = str_replace("{{message}}",$message,$url);


  $result = mail($to, $subject,$message, $headers);
return $result;
}



function withdraw_xrp($id,$amount,$crypto,$destination,$destination_tag,$fee)
        {
    //      $client = new Client('https://s.altnet.rippletest.net:51234');
     	$client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');
	$destination_tag = intval($destination_tag);
        $amount_conv = strval(floatval($amount)*1000000); //1000000 equivalent to 1XRP
        $tx_type = "Payment";
        $account = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->address; //rippleuser1 address
        $paytofee = WalletAddress::where('uid','888')->where('crypto',$crypto)->first()->address; //ripplebase address
        $acc_secret = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->secret;
        $currency = "XRP";

	//$client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');
        //$amount = '0.00001';
        //$amount_conv = strval(floatval($amount)*1000000); //1000000 equivalent to 1XRP
        //$tx_type = "Payment";
        //$account = "rwWWo43cE8BrZPwbmKVqsZ5UzAsFAhbFx"; //"rL2KALodNX65eSfQ72rKKwYZZsKq6gLCFF"; //rippleuser1 address
        //$paytofee = "rfDXhw5kDXLZKS6FUyPCixh244KBMqzbvV"; //ripplebase address
        //$acc_secret = "ssUxydcKhNL9sFv28EmoonunKjVkq"; //"shvhNvh23T1xJkfrNjrVzbpwwjJSz";
        //$destination = "rL2KALodNX65eSfQ72rKKwYZZsKq6gLCFF"; //rippleuser1 address
        //$currency = "XRP";
        //$destination_tag = "852456";
	

        //-------------------Payment Submission-----------------------------------
	if($destination_tag != '')
	{
        $txParams = [
          'TransactionType' => $tx_type,
          'Account' => $account,
          'Destination' => $destination,
          'DestinationTag' => $destination_tag,
          'Amount' => $amount_conv,
          'Fee' => '10'
        ];
	}
	else
	{
         $txParams = [
           'TransactionType' => $tx_type,
           'Account' => $account,
           'Destination' => $destination,
           //'DestinationTag' => $destination_tag,
           'Amount' => $amount_conv,
           'Fee' => '10'
         ];
	}
        $transaction = new \FOXRP\Rippled\Api\Transaction($txParams, $client);
	//dd($transaction);
//if(xrp_getbalance($id) > '20'){
        $responsePay = $transaction->submit(base64_decode($acc_secret));
	//dd($responsePay->isSuccess());
        if ($responsePay->isSuccess()) {
          $dataSubmit = $responsePay->getResult();


          $txid = $dataSubmit['tx_json']['hash'];
          $resStat = $dataSubmit['engine_result'];
          $resAcc = $dataSubmit['tx_json']['Account'];
          $resVal = $dataSubmit['tx_json']['Amount'];
          $resType = $dataSubmit['tx_json']['TransactionType'];
          $resDes = $dataSubmit['tx_json']['Destination'];
          $resFee = $dataSubmit['tx_json']['Fee'];

         //-------------------Fee Submission-----------------------------------
          $feeamount =  "1";//strval(floatval($fee)/1000000);
	//dd($feeamount);
          $feeParams = [
            'TransactionType' => $tx_type,
            'Account' => $account,
            'Destination' => $paytofee,
            'Amount' => $feeamount,
            'Fee' => '10'
          ];
	  $transaction = new \FOXRP\Rippled\Api\Transaction($feeParams , $client);
//dd($transaction);
          $responseFee = $transaction->submit(base64_decode($acc_secret));
//dd($responseFee );
          if ($responseFee->isSuccess()) {
            $dataSubmit_fee = $responseFee->getResult();
	//dd($dataSubmit_fee);
            $resHash_fee = $dataSubmit_fee['tx_json']['hash'];
            $resStat_fee = $dataSubmit_fee['engine_result'];
            $resAcc_fee = $dataSubmit_fee['tx_json']['Account'];
            $resVal_fee = $dataSubmit_fee['tx_json']['Amount'];
            $resType_fee = $dataSubmit_fee['tx_json']['TransactionType'];
            $resDes_fee = $dataSubmit_fee['tx_json']['Destination'];
            $resFee_fee = $dataSubmit_fee['tx_json']['Fee'];

            

          }

          $datamsg = response()->json([
            'txid' => $txid
          ]);

          return $datamsg->content();

        }
//}
//else {
	//dd("Insufficient Balance");
//}
        
      }






///////////////////////////////////////////shop email///////////////////////////////////////

function send_email_shop($receiveemail_username,$days,$senderemail_username,$receiveemail_email,$orderid,$totalAll,$message1,$subject,$message2)
{

$paymentdetails = ShopPayment::where('orderid',$orderid)->first();
  //  dd('sfdsdf',$receiveemail_username,$days,$senderemail_username,$receiveemail_email);
    $to=$receiveemail_email;  //change to ur mail address


    $subject="Colony Shop | ".$subject;

   //$message =  file_get_contents('https://www.pinkexc.online/sources/template.php/?&b=edit&id='.$id); /* Your Template*/
    $htmlContent = "
    <html>
                    <head>
                    <title>Colony Shop</title>
                    <style>
                    p.message {
                        color:#000000;
                    }
                    p.signature {
                        font-size: 11px;
                        color:#C3C3C3;
                    }
                    </style>
                    </head>
                    <body>
                    <p class='message'>Dear ".$receiveemail_username.",</p>
                    <p class='message'>".$message1."</p>
                    <br/>
                 
			 <p class='message'>$message2
			</p>


                     <br/>
                    <br/>

                    <p class='signature'>1, Jln Meru Bestari A14, Medan Meru Bestari, 30020 Ipoh, Perak.
                    <br />
                    Phone: +60 (5) 5251866
                    <br />
                    Email: no-reply@pinkexc.com 
                    <br />
                    Official Website: https://colony.pinkexc.com
                    <br />
                    Powered by Pinkexc (M) Sdn. Bhd.
                    </p>
                    
                    </body>
                    </html>
    ";
    $headers = 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
    $headers .= "From: no-reply@pinkexc.com "; 

           mail($to, $subject, $htmlContent, $headers);  // Mail Function
       }


function send_email_shop_tracking($receiveemail_username,$days,$senderemail_username,$receiveemail_email,$orderid,$trackno)
{

$paymentdetails = ShopPayment::where('orderid',$orderid)->first();
    //dd('sfdsdf',$receiveemail_username,$days,$senderemail_username,$receiveemail_email,$trackno);
    $to=$receiveemail_email;  //change to ur mail address
    $subject="Colony Shop | Check Your Tracking Number";

   //$message =  file_get_contents('https://www.pinkexc.online/sources/template.php/?&b=edit&id='.$id); /* Your Template*/
    $htmlContent = "
    <html>
                    <head>
                    <title>Colony Shop</title>
                    <style>
                    p.message {
                        color:#000000;
                    }
                    p.signature {
                        font-size: 11px;
                        color:#C3C3C3;
                    }
                    </style>
                    </head>
                    <body>
                    <p class='message'>Dear ".$receiveemail_username.",</p>
                    <p class='message'>Seller ".$senderemail_username." was shipped your items with tracking number ".$trackno." . Please wait for your item receive and click 'Received' button after you have received the items. Thank you</p>
                    <br/>
                 
			 <p class='message'>".$message2."</p>


                     <br/>
                    <br/>

                    <p class='signature'>1, Jln Meru Bestari A14, Medan Meru Bestari, 30020 Ipoh, Perak.
                    <br />
                    Phone: +60 (5) 5251866
                    <br />
                    Email: no-reply@pinkexc.com 
                    <br />
                    Official Website: https://colony.pinkexc.com
                    <br />
                    Powered by Pinkexc (M) Sdn. Bhd.
                    </p>
                    
                    </body>
                    </html>
    ";
    $headers = 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
    $headers .= "From: no-reply@pinkexc.com "; 

           mail($to, $subject, $htmlContent, $headers);  // Mail Function
       }

function coinvata_misunderstanding($convertFrom,$convertTo,$rate)
{
$displayprice ='0';
  if ($convertFrom == "BTC") 
  {

    if ($convertTo == "LTC") {

        $displayprice = $rate;


    }
    else if ($convertTo == "DOGE") {

       $displayprice = (1 / $rate);

   }
   else if ($convertTo == "DASH") {

       $displayprice = (1 / $rate);

   }
   else if ($convertTo == "ETH") {

        $displayprice = $rate;

}
else if ($convertTo == "BCH") {

   $displayprice = $rate;

}

}
else if($convertFrom == "LTC")
{

  if ($convertTo == "BTC") {

    $displayprice = $rate;

}
else if ($convertTo == "DOGE") {

    $displayprice = (1 / $rate);


}
else if ($convertTo == "DASH") 
{
    $displayprice = (1 / $rate);

}
else if ($convertTo == "ETH") 
{

        $displayprice = $rate;

}
else if ($convertTo == "BCH") 
{

    $displayprice = $rate;

}
}
else if($convertFrom == "DOGE")
{

  if ($convertTo == "BTC") {

    $displayprice = $rate;

}
else if ($convertTo == "LTC") 
{

    $displayprice = $rate;

}
else if ($convertTo == "DASH") 
{

    $displayprice = ($rate);

}
else if ($convertTo == "ETH") {

    $displayprice = $rate;

}
else if ($convertTo == "BCH") 
{
    $displayprice = $rate;

}
}
else if($convertFrom == "DASH")
{

    if ($convertTo == "BTC") 
    {
        $displayprice = $rate;

    }
    else if ($convertTo == "LTC") 
    {
        $displayprice = $rate;


    }
    else if ($convertTo == "DOGE") 
    {

        $displayprice = $rate;

    }
    else if ($convertTo == "ETH") 
    {

        $displayprice = $rate;

 }
 else if ($convertTo == "BCH") 
 {

    $displayprice = $rate;


}
}
else if($convertFrom == "BCH")
{

    if ($convertTo == "BTC") 
    {

        $displayprice = $rate;

    }
    else if ($convertTo == "LTC") 
    {
        $displayprice = $rate;

    }
    else if ($convertTo == "DOGE") 
    {
        $displayprice = (1 / $rate);

    }
    else if ($convertTo == "DASH") 
    {
      

        $displayprice = (1 / $rate);

    }
    else if ($convertTo == "ETH") 
    {

      
        $displayprice = $rate;

    }
}
else if($convertFrom == "ETH")
{


    if ($convertTo == "BTC") 
    {

        $displayprice = $rate;

    }
    else if ($convertTo == "LTC") 
    {
      
        $displayprice = $rate;

    }
    else if ($convertTo == "DOGE") 
    {

        $displayprice = (1 / $rate);

    }
    else if ($convertTo == "DASH") 
    {
        $displayprice = (1 / $rate);

    }
    else if ($convertTo == "BCH") 
    {
        $displayprice = $rate;

    }
}

return response()->json([
    'displayprice' => $displayprice
]);

}

function meletop($array) {
  if (!is_array($array)) return FALSE;
  $result = array();
  foreach ($array as $key => $value) {
    if (is_array($value))
      $result = array_merge($result, meletop($value));
    else $result[$key] = $value;
  }
  return $result;
}

function dumpkey($label,$crypto)
{

if($crypto == "BTC")
{
$post = [
    'id' => 22,
    'label' => $label
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);
  
}
else if($crypto == "LTC")
{
  $post = [
    'id' => 22,
    'label' => $label
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);

}
else if($crypto == "DOGE")
{
  $post = [
    'id' => 22,
    'label' => $label
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);

}
else if($crypto == "DASH")
{
  $post = [
    'id' => 22,
    'label' => $label
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);

}
else if($crypto == "BCH")
{

$post = [
    'id' => 22,
    'label' => $label
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    curl_close($ch);


}
return $balance;
}


function coinvata_stellar($type,$memo,$txtmemo,$num,$ids,$current_price,$displayprice,$platform)
{

  $rows_price = PriceApi::where('crypto','XLM')->first(); 
  $rate_pricemyr = $rows_price->price;

  $price2 = round($rate_pricemyr,2);

  $rows = StellarInfo::where('id',2)->first(); 

    //horizon
  $horizon = $rows->str_horizon.'accounts/';

  $setting = Setting::where('id',1)->first(); 

  $total_fee =  $setting->network_fee_xlm;    

  $amount = number_format($num, 7, '.', '');

  $acc_id = '';
  $rows = StellarInfo::where('id',3)->first(); 
  $fix_limit = $rows->fix_limit;
  $balance = $fix_limit + $amount; 
  $pop = 0;



  if($type == "sell coinvata")
  {
    $balusr = round(xlm_getbalance_pod($ids),7);
  }
  else
  {
    $balusr = round(xlm_getbalance(3),7);
  }

  $fee = $total_fee;

  $amt_can_withdraw =  $balusr - $fee; 

  $wait_str = 0;  

  $rowsy = StellarPod::where('str_status','pending')->where('source_id',$ids)->first();
  if(isset($rowsy)){$wait_str = 1;}  

  $rowsw = StellarPod::where('str_status','pending')->where('destination_id',$ids)->first();
  if(isset($rowsw)){$wait_str = 1;} 

  $rowss = User::where('xlm_block','1')->where('id',$ids)->first();
  if(isset($rowss)){$wait_str = 1;} 

  $check_users = User::where('id', $ids)->first();


 if($wait_str == 1){ 
  $arr = '[{  
    "msj": "Sorry, wait for the second. Please try again later.Please contact Administrator." 
  }]'; 
}     
elseif($amount>$amt_can_withdraw && $type == "sell coinvata"){
  if($amt_can_withdraw<=0){$amt_can_withdraw = 0;}else{$amt_can_withdraw = $amt_can_withdraw;}
  $arr = '[{  
    "msj": "Insufficient funds. Your maximum amount you can withdraw is '.$amt_can_withdraw.'" 
  }]'; 
}   
elseif(xlm_getbalance(2) < $balance && $type == "sell coinvata") // lumen stellar not enough
{

  $arr = '[{  
    "msj": "Sorry, your stellar is not enough to process the coinvata 1."
  }]'; 
} 
elseif(xlm_getbalance(3) < $balance && $type == "buy coinvata") // lumen stellar not enough
{

  $arr = '[{  
    "msj": "Sorry, your stellar is not enough to process the coinvata 2."
  }]'; 
} 
else // success
{   
  $e = insert_coinvatapod($type,$amount,$memo,$ids,$txtmemo,$total_fee,$current_price,$displayprice,$platform);
         
          $arr = '[{
           "msj": "None error stellar POD"
         }]'; 
 
 }

return $arr;

}




function insert_coinvatapod($type,$amount,$memo,$ids,$txtmemo,$total_fee,$current_price,$displayprice,$platform){

  $fee = settings('network_fee_xlm');

  $ins_wdraw = new StellarCoinvata;
  $ins_wdraw->uid = $ids;
  $ins_wdraw->stellar_pod_id = '';
  $ins_wdraw->token = 'native';
  $ins_wdraw->send_token = $amount;
  $ins_wdraw->status = 'completed';
  $ins_wdraw->memo = $memo;
  $ins_wdraw->txtmemo = $txtmemo;
  $ins_wdraw->save();

  $new_wdraw = $ins_wdraw->id;

$rows_price = PriceApi::where('crypto','XLM')->first(); 
  $rate_pricemyr = $rows_price->price;

  $price2 = round($rate_pricemyr,2);

$d_price = $price2 - ($price2 * 0.05);

  $myr_withdrawal = round(($d_price*$amount),2);

  $current_price = $current_price;

  $ins_pod = new StellarPod;
  $ins_pod->type = $type;
  $ins_pod->pod_id = $new_wdraw;
  if($type == "buy coinvata")
  {
    $ins_pod->source_id = 'coinvata';
    $ins_pod->balance_source = '0';
    $ins_pod->destination_id = $ids;
    $ins_pod->balance_destination = number_format((xlm_getbalance_pod($ids) + $amount),7,'.','');
 $ins_pod->status = 'receive';
  }
  else
  {
    $ins_pod->source_id = $ids;
    $ins_pod->balance_source = number_format((xlm_getbalance_pod($ids) - $amount - $fee),7,'.','');
    $ins_pod->destination_id = 'coinvata';
    $ins_pod->balance_destination = '0';
$ins_pod->status = 'send';  
  }
  
  $ins_pod->send_token = number_format($amount,7,'.','');
  $ins_pod->memo = $memo;
  $ins_pod->txtmemo = $txtmemo;
 
  $ins_pod->str_status = 'pending';
  $ins_pod->str_transaction_id = '';
  $ins_pod->myr_amount = $myr_withdrawal;
  $ins_pod->rate = $d_price;
  $ins_pod->current_price = $price2;
  $ins_pod->probBy = $platform;
  $ins_pod->save();

  $new_pod = $ins_pod->id;

  $upd_wdraw = StellarCoinvata::findOrFail($new_wdraw);
  $upd_wdraw->stellar_pod_id = $new_pod;
  $upd_wdraw->save();

    //notification
  $price = PriceApi::where('crypto','XLM')->first()->price;
  $username = User::where('id',$ids)->first()->username;
  $myrAmount = round(($price * $amount),2);
  $xlm_amnt = round($amount,5);

}

////////////////////////////////////////////////////////////////////
////////////////////////////balance by crypto////////////////////////
//////////////////////////////////////////////////////////////////////
function all_getbalance($id, $crypto) {

    $uid = WalletAddress::where('uid', $id)->where('crypto',$crypto)->first();

  if($crypto=='BTC'){$link = getinfoBTC();}  
  elseif($crypto=='LTC'){$link = getinfoLTC();}  
  elseif($crypto=='DOGE'){$link = getinfoDOGE();}  
  elseif($crypto=='DASH'){$link = getinfoDASH();}  
  elseif($crypto=='BCH'){$link = getinfoBCH();}  
  //GET BALANCE
  $post = [
      'id' => 4,
      'label' => $uid->label
  ];

  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

  $wallet_balance = curl_exec($ch);

  curl_close($ch);


  //UPDATE BALANCE INTO TABLE WALLETADDRESS
  // $user = WalletAddress::findOrFail($uid->id);
  // $user->available_balance = $wallet_balance;
  //$user->save();

 $updt = WalletAddress::where('uid', $id)->where('crypto', $crypto)
          ->update([
               'available_balance' => $wallet_balance
          ]);
    




  return $wallet_balance;
}



/////////////////////////////////////////ESTIMATE FEE////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
function getestimatefee($crypto) {

  $link = PriceApi::where('crypto', $crypto)->first()->ip_getinfo;

if($crypto == 'BCH'){


    //GET user transaction
    $post = [
        'id' => 24,
        
    ];
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

  $feerate = curl_exec($ch);


}elseif($crypto == 'DOGE'){

  $setting = Setting::first();
$estimatefee_myr = $setting->network_fee_doge;


}elseif($crypto == 'BTC'){
 
    //GET user transaction
    $post = [
        'id' => 24,
        
    ];

    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $fee = curl_exec($ch);
    $feerate = json_decode($fee);  


}else{
 
    //GET user transaction
    $post = [
        'id' => 24,
        
    ];

    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $fee = curl_exec($ch);
    $feerate = json_decode($fee);  

 }



$rate = PriceApi::where('crypto', $crypto)->first()->price;

$estimatefee = $feerate;

if($crypto == 'BCH'){
$estimatefee = number_format($estimatefee,8);
}elseif($crypto == 'BTC'){
$estimatefee = number_format($estimatefee->feerate,8);
}else{
$estimatefee = number_format($estimatefee->feerate,8);
}

if($crypto == 'DOGE'){
$estimatefee_myr = $estimatefee_myr;
}else{
$estimatefee_myr = number_format($estimatefee * $rate,4);
}

  return $estimatefee_myr;
}


///////////////////////////////////////////////////////////////
/// LIST BALANCE EACH USER/////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

function list_user_balance($crypto){
   if($crypto=='BTC'){
		
	$post = [
        'id' => 1
    ];

    $ch = curl_init(getinfoBTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
 
	   $val = str_replace('\n','',$bit_trans);
	   $val = str_replace('"Array(   ','',$val);
	   $val = str_replace(')','',$val);
	   $val = str_replace(' ','',$val); 
	   $result = explode("[",$val);
	   
    curl_close($ch); 

    return $result;
	
	}elseif($crypto=='BCH'){
		
	$post = [
        'id' => 1
    ];

    $ch = curl_init(getinfoBCH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
 
	   $val = str_replace('\n','',$bit_trans);
	   $val = str_replace('"Array(   ','',$val);
	   $val = str_replace(')','',$val);
	   $val = str_replace(' ','',$val); 
	   $result = explode("[",$val);
	   
    curl_close($ch); 

    return $result;

	}elseif($crypto=='LTC'){
		
	$post = [
        'id' => 1
    ];

    $ch = curl_init(getinfoLTC());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
 
	   $val = str_replace('\n','',$bit_trans);
	   $val = str_replace('"Array(   ','',$val);
	   $val = str_replace(')','',$val);
	   $val = str_replace(' ','',$val); 
	   $result = explode("[",$val);
	   
    curl_close($ch); 

    return $result;
	
	}elseif($crypto=='DASH'){
		
	$post = [
        'id' => 1
    ];

    $ch = curl_init(getinfoDASH());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
 
	   $val = str_replace('\n','',$bit_trans);
	   $val = str_replace('"Array(   ','',$val);
	   $val = str_replace(')','',$val);
	   $val = str_replace(' ','',$val); 
	   $result = explode("[",$val);
	   
    curl_close($ch); 

    return $result;
	
	}elseif($crypto=='DOGE'){
		
	$post = [
        'id' => 1
    ];

    $ch = curl_init(getinfoDOGE());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
 
	   $val = str_replace('\n','',$bit_trans);
	   $val = str_replace('"Array(   ','',$val);
	   $val = str_replace(')','',$val);
	   $val = str_replace(' ','',$val); 
	   $result = explode("[",$val);
	   
    curl_close($ch); 

    return $result;
	
	}else{
		
	$result = array();

    return $result;
	}

}

/////////////////////////REF NUMBER FOR BUY/////////////////////////////////

function refforbuy($platform, $crypto, $buyid){

	if($platform == 'mobile'){$code='M';}
	else{$code='W';}

	if($crypto == 'BTC'){$refnum = $code.'COLB' . $buyid;}
	elseif($crypto == 'BCH'){$refnum = $code.'COLBC' . $buyid;}
	elseif($crypto == 'LTC'){$refnum = $code.'COLL' . $buyid;}
	elseif($crypto == 'DASH'){$refnum = $code.'COLDH' . $buyid;}
	elseif($crypto == 'DOGE'){$refnum = $code.'COLD' . $buyid;}
	elseif($crypto == 'XLM'){$refnum = $code.'COLX' . $buyid;}
	elseif($crypto == 'ETH'){$refnum = $code.'COLE' . $buyid;}
	elseif($crypto == 'XRP'){$refnum = $code.'COLR' . $buyid;}

	return $refnum;
}

 
/////////////////////////////XLM SEND TO ADMIN///////////////////////////////////////

 function xlmtoadmin($crypto_amount,$memo){
        
		$account_id = StellarInfo::where('id',1)->first()->account_id;
		//$memo = '2;'.time();
		$num = $crypto_amount;
		$seed_id = StellarInfo::where('id',2)->first()->seed_id;

		$server = Server::publicNet();

		$sourceKeypair = Keypair::newFromSeed($seed_id);

		$destinationAccountId = $account_id;

		$destinationAccount = $server->getAccount($destinationAccountId);

		$transaction = \ZuluCrypto\StellarSdk\Server::publicNet()
		->buildTransaction($sourceKeypair->getPublicKey())
		->addOperation(
			PaymentOp::newNativePayment($destinationAccountId, $num)
		)
        	->setTextMemo($memo);  
        
		$response = $transaction->submit($sourceKeypair->getSecret());
		return $response;
    }

/////////////////////////////XLM SEND///////////////////////////////////////

 function xlmsend2($crypto_amount,$memo,$account_id,$account_type){
        
		//$account_id = StellarInfo::where('id',1)->first()->account_id;
		//$memo = '2;'.time();
		$num = $crypto_amount;
		if($account_type=='admin'){$seed_id = StellarInfo::where('id',1)->first()->seed_id;}
		elseif($account_type=='user'){$seed_id = StellarInfo::where('id',2)->first()->seed_id;}
		elseif($account_type=='coinvata'){$seed_id = StellarInfo::where('id',3)->first()->seed_id;}
		else{return 'error';}
		
		$server = Server::publicNet();

		$sourceKeypair = Keypair::newFromSeed($seed_id);

		$destinationAccountId = $account_id;

		$destinationAccount = $server->getAccount($destinationAccountId);

		$transaction = \ZuluCrypto\StellarSdk\Server::publicNet()
		->buildTransaction($sourceKeypair->getPublicKey())
		->addOperation(
			PaymentOp::newNativePayment($destinationAccountId, $num)
		)
        	->setTextMemo($memo);  
        
		$response = $transaction->submit($sourceKeypair->getSecret());
		return $response;
    }
////////////////////ETH LABEL//////////////////////////////
function send_eth_label($sender,$receiver,$amount){
    $converter = new \Bezhanov\Ethereum\Converter();
       $user = WalletAddress::where('label',$sender)->where('crypto','ETH')->first();
       $from = $user->address;
       $to = WalletAddress::where('label',$receiver)->where('crypto','ETH')->first()->address;
       $gas = '0x'.dec2hex('100000');
       $gasPrice = '0x'.dec2hex('5000000000');

       $value = '0x'.dec2hex($converter->toWei($amount, 'ether'));
     //$value = '0x9184e72a';

       $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);

//if(Auth::id()=="29285"){
//dd($transaction);

    $bal1 = getbalance('ETH', $sender);

       //return Ethereum::personal_sendTransaction($transaction,'P-HY,mUr)PfGQ9NW/BNs:+q3>)YLb+Q8uz"gq;(!*Avd*EQd');
	return Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');


}
////////////////////////CUSTOM ETHEREUM//////////////////////
##========ETH TX BULK=======##
function transactions_etharr($address) {
  $json_url = "https://api.etherscan.io/api?module=account&action=txlist&address=$address&startblock=0&endblock=99999999&sort=asc&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB";
  $json = file_get_contents($json_url);
  $data = json_decode($json);

  return $data->result;
}
##========SEND BULK=======##

function ethbulk($sender,$amount){
	$converter = new \Bezhanov\Ethereum\Converter();
    $from = $sender;
    $to = "0x0fb4761988aac63d87f629a38bec7c3d6ad078a8";
	$dustdat = WalletAddress::where('crypto', 'ETH')->where('address',$sender)->first();
    $gas = '0x186a0';
    $gasPrice = '0x2540be400';
    $value = $amount;
	$spend_eth = substr(number_format($converter->fromWei(hexdec($value), 'ether'), 18, '.', ''), 0, -10);
	$transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);
//dd($transaction);	
	$txid = Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');
	if($txid != null){
		$eth_dep = ETHDeplete::create([
			'uid' => $dustdat->uid,
			'username' => $dustdat->label,
			'datetime' => Carbon::now(),
			'from_address' => $from,
			'to_address' => $to,
			'category' => 'OUT',
			'amount' => $spend_eth,
			'txid' => $txid,
			'confirmation' => '',
			'status' => 'INITIATED'
		]);
	}
	else{
		$eth_dep = ETHDeplete::create([
			'uid' => $dustdat->uid,
			'username' => $dustdat->label,
			'datetime' => Carbon::now(),
			'from_address' => $from,
			'to_address' => $to,
			'category' => 'OUT',
			'amount' => $spend_eth,
			'txid' => $txid,
			'confirmation' => '',
			'status' => 'FAILED'
		]);
	}
	$balup = getbalanceeth($from);
	return $txid;
}

##========GETBALANCE======##
function getbalanceeth($from){
	$converter = new \Bezhanov\Ethereum\Converter();
	$data= json_decode(file_get_contents("https://api.etherscan.io/api?module=account&action=balance&address=".$from."&tag=latest&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB"));
	$balraw = substr(number_format($converter->fromWei($data->result, 'ether'), 18, '.', ''), 0, -10);
	$balup = WalletAddress::where('crypto','ETH')->where('address', $from)->update([
		'available_balance' => $balraw,
		'ethbalupflag' => "UP"
	]);
}
####################anypay#########################
function anypaybalance(){
        $account_details = accountdetail();
        //dd($account_details);
        $data = array("command"=>1,"account_details"=>$account_details);
        $data_string = json_encode($data);

        $response = anypaycurl($data_string);

        if(isset($response->Response[0]->response_desc)){
            return $response->Response[0]->response_desc;
        }
        else{
            return $response->Response[0]->balance;
        }
    }

    function anypaytopup($operator_code,$order_req_id,$reload_number,$amount){
        $account_details = accountdetail();
 
        //dd($account_details);
        $data = array("command"=>2,"account_details"=>$account_details,"operator_code"=>$operator_code,
                    "order_req_id"=>$order_req_id,"reload_number"=>$reload_number,"amount"=>$amount);
        $data_string = json_encode($data);

        $response = anypaycurl($data_string);

        if($response->Response[0]->response_desc=='InProcess'){
            return $response->Response[0];
        }
        else{
            return $response->Response[0]->response_desc;
        }
    }

    function anypaytopupstatus($order_req_id){
        $account_details = accountdetail();
        //dd($account_details);
        $data = array("command"=>3,"account_details"=>$account_details,"order_req_id"=>$order_req_id);
        $data_string = json_encode($data);

        $response = anypaycurl($data_string);

        if($response->Response[0]->response_desc=='Success'){
            return $response->Response[0];
        }
        elseif($response->Response[0]->response_desc=='Failed'){
            return $response->Response[0];
        }
        else{
            return $response->Response[0]->response_desc;
        }
    }

    function anypayrecharge($operator_code,$order_req_id,$amount){
        $account_details = accountdetail();

        //dd($account_details);
        $data = array("command"=>4,"account_details"=>$account_details,"operator_code"=>$operator_code,
        "order_req_id"=>$order_req_id,
        "amount"=>$amount);
        $data_string = json_encode($data);

        $response = anypaycurl($data_string);

        if($response->Response[0]->response_desc=='Success'){
            return $response->Response[0];
        }
        else{
            return $response->Response[0];
        }
    }

    function accountdetail(){
        $loginid = '01120511577';
        $password = 'UaR34vvE$oMe';

        $account_details = $loginid.'|'.$password;
        $encrypted_account = base64_encode($account_details);

        return $encrypted_account;
    }

    function anypaycurl($data_string){
        $ch = curl_init('https://api.anypay.my/user/api/v4/request');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        $result = json_decode($result); 

        return $result;
    }

/////////////////////ANYPAY IN COLONY//////////////////////

function anypay_transaction($id,$label,$crypto,$crypto_balance,$myr_amount,$reload_number,$operator_code,$platform){

    $curr_price = PriceApi::where('crypto',$crypto)->first()->price;
    $crypto_amount = round($myr_amount/$curr_price,5);
    $crypto_commission = $operator_code["commission"] * $crypto_amount;
    $after_balance = $crypto_balance - $crypto_amount;

    $move = move_crypto_comment($crypto, $label,'usr_jompay', $crypto_amount,'sell');

    if($crypto == 'ETH' && $move == null){$hash = "error";}
    elseif($crypto =='ETH'){$hash = $move;}
    else{$hash = '1';}

    $anypay_trans = Anypaytrans::create([
        'uid'=>$id,
	'before_bal'=>round($crypto_balance,8),	
        'myr_amount'=>$myr_amount,	
        'crypto_amount'=>$crypto_amount,
	'after_bal'=>round($after_balance,8),
        'curr_price'=>round($curr_price,5),	
        'crypto'=>$crypto,
        'txid'=>$hash,
        'crypto_release'=>'1',	
        'commission'=>$operator_code["commission"],
	'crypto_commission'=>round($crypto_commission,8),
	'reload_number'=>$reload_number,
        'platform'=>$platform,
	'operator_name'=>$operator_code["name"],
    ]);
    $order_req_id = refforbuy($platform, $crypto, $anypay_trans->id);
    
    if($hash == "error"){return "error";}
    else{$response = anypaytopup($operator_code["code"],$order_req_id,$reload_number,$myr_amount);}

    if($response->response_desc=='InProcess'){
        $anypay_update = Anypaytrans::where('id',$anypay_trans->id)->update([
            'order_req_id'=>$order_req_id,
            'txnid'=>$response->txnid,
            'status'=>'success'
        ]);

        //dd($response);

        $end = $response;
    }
    else{
	$response_status=$response->response_status;
	$response_desc=$response->response_desc;
        $anypay_update = Anypaytrans::where('id',$anypay_trans->id)->update([
            'order_req_id'=>$order_req_id,
            'status'=>'failed',
	    'response_status'=>$response_status,
	    'response_desc'=>$response_desc
        ]);

        //dd($response);

        $end = 'error';
    }
    
    return $end;

}

function anypay_code($operator_name){
    $operator_list = Anypayop::where('name',$operator_name)->first();
    if(isset($operator_list)){
        $operator_code = array('code'=>$operator_list->code,
                            'commission'=>$operator_list->commission,
			'name'=>$operator_list->name);
    }
    else{$operator_code = 'error';}
    return $operator_code;
}

function correct_number($reload_number){

  $reload_number = str_replace("+6","",$reload_number);
  $reload_number = str_replace(" ","",$reload_number);
  $reload_number = str_replace("-","",$reload_number);

  return $reload_number;
}