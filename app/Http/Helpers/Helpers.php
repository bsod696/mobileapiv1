<?php

use App\User;
use App\StreamLink;

function urlss(){
	return realpath(base_path().'/../assets');
   
}

function ImageCheck($ext){
    if($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'bnp'){
        $ext = "";
    }
    return $ext;
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

function apiToken($session_uid){
  $key=md5('FaceSec'.$session_uid);
  return hash('sha256', $key);
}

function isValidUsername($str) {
  return preg_match('/^[a-zA-Z0-9-_]+$/',$str);
}

function isValidEmail($str) {
  return filter_var($str, FILTER_VALIDATE_EMAIL);
}