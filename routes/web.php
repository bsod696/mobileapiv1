<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
header('Access-Control-Allow-Origin: *');
header( 'Access-Control-Allow-Headers: Authorization, Content-Type' );
header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token, x_csrftoken');


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::group(['middleware'=>'cors','prefix' => 'api'], function () {
  Route::get('login','APIControllerv1@loginProc');
  Route::get('signup','APIControllerv1@signup');
  Route::get('change_password','APIControllerv1@change_password');
  //Route::get('reset_password','APIControllerv1@reset_password');
  Route::get('generateOTP','APIControllerv1@generateOTP');
  Route::get('streamCam','APIControllerv1@streamCam');
});
