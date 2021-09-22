<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user/register','Api\UserController@store')->name('user.store');
Route::post('/auth/login', 'Api\AuthController@login')->name('login');

//jwt (json web token)
Route::group(['middleware' => ['apiJwt']], function(){

    Route::get('/user/me','Api\UserController@me')->name('user.me');

    Route::get('/painel/permission','Api\PainelController@permission')->name('painel.permission');

    Route::get('/transaction/history','Api\TransactionController@history')->name('transaction.history');
    Route::post('/transaction/send','Api\TransactionController@store')->name('transaction.send');
    Route::post('/transaction/rollback','Api\TransactionController@rollback')->name('transaction.rollback');
    
    Route::get('/wallet/balance','Api\WalletController@balance')->name('wallet.balance');
    Route::post('/wallet/update','Api\WalletController@update')->name('wallet.update');

    Route::post('/notify/send','Api\NotifyController@balance')->name('notify.send');

});
