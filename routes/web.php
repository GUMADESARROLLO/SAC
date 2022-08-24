<?php

use Illuminate\Support\Facades\Route;

Route::get('Home', 'HomeController@getHome')->name('Home');
Route::get('/', 'HomeController@getHome');
Route::get('getData', 'HomeController@getData')->name('getData');
Route::get('getDataCliente', 'HomeController@getDataCliente')->name('getDataCliente');
Route::get('getDataCliente/{ID}', 'HomeController@getDataCliente')->name('getDataCliente');