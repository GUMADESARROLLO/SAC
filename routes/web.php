<?php

use Illuminate\Support\Facades\Route;

Route::get('Home', 'HomeController@getHome')->name('Home');
Route::get('/', 'HomeController@getHome');

Route::get('ArticuloFavorito', 'HomeController@getArticuloFavorito')->name('ArticuloFavorito');
Route::get('getData', 'HomeController@getData')->name('getData');
Route::get('getArticulosFavoritos', 'HomeController@getArticulosFavoritos')->name('getArticulosFavoritos');

Route::get('getDataCliente', 'HomeController@getDataCliente')->name('getDataCliente');
Route::get('getDataCliente/{ID}', 'HomeController@getDataCliente')->name('getDataCliente');
Route::get('getDataArticulo/{ID}', 'HomeController@getDataArticulo')->name('getDataArticulo');

Route::post('AddFavs', 'HomeController@AddFavs')->name('AddFavs');