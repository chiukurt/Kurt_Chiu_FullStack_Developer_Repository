<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::namespace('Admin')->prefix('admin')->name('admin.')->middleware('can:drop_pins')->group(function(){
    Route::resource('/pins','UsersController', ['except'=>['show','create', 'store']]);
});

Route::get('/import', 'Admin\UsersController@importFile');
Route::post('/import', 'Admin\UsersController@importExcel');
