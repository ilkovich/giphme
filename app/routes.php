<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function() { return Response::make('ok', 200); });

Route::post('/email/hook', ['uses' => 'EmailController@postHookContextio']);

Route::post('/email/hook_sendgrid', ['uses' => 'EmailController@postHookSendgrid']);

