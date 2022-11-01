<?php

use Illuminate\Support\Facades\Route;
use Modules\SupportChat\Http\Controllers\LoginController;
use Modules\SupportChat\Http\Controllers\Telegram\MessagesController;
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

Route::prefix('supportchat')->group(function() {
    Route::get('/', 'SupportChatController@index');
    Route::match(['post', 'get'], '/webhook', [MessagesController::class, 'webhook']);
    Route::get('/login',[LoginController::class,'login'])->name('supportchat.login');
    Route::get('/question',[LoginController::class,'question']);
    Route::post('login_store',[LoginController::class,'login_store'])->name('supportchat.login.store');
    Route::post('verify_store/{user}',[LoginController::class,'verify_store'])->name('supportchat.verify.store');
    Route::get('/lang/{lang}', [LoginController::class, 'lang'])->name('supportchat.lang');
});

