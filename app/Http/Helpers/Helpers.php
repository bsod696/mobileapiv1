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

function send_reset_password($to, $name, $message,$hash){
  $msgData = array(
    "uname" => $name,
    "msg" => $message,
    "hash" => $hash
  );
  // dd($to, $msgData);
  Mail::to($to)->send(new ForgotPasswordMail($msgData));
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