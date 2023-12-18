<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => 'authspo'], function () {
    Route::get('/change_password', App\Http\Controllers\ChangePasswordController::class)->name('change_password');
    Route::get('/logout', App\Http\Controllers\LogoutController::class)->name('logout');

    Route::get('/{any}', [IndexController::class, 'index'])->where('any', '^(?!api).*$');
});

