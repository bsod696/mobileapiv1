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

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware'=>'cors','prefix' => 'api'], function () {
  Route::get('login','APIControllerv3@loginProc');
  Route::get('signup','APIControllerv3@signup');
  Route::get('updatePIN','APIControllerv3@updatePIN');
  Route::get('guestHistory','APIControllerv3@guestHistory');
  Route::get('generateOTP','APIControllerv3@generateOTP');
  Route::get('streamCam','APIControllerv3@streamCam');
  Route::get('stopCam','APIControllerv3@stopCam');
});
