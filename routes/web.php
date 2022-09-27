<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('Home', 'HomeController@getHome')->name('Home');
//Route::get('/', 'HomeController@getHome');
Auth::routes();
Route::get('/', 'Auth\LoginController@showLoginForm');
Route::get('/logout', 'Auth\LoginController@logout');



Route::get('ArticuloFavorito', 'HomeController@getArticuloFavorito')->name('ArticuloFavorito');
Route::get('getData', 'HomeController@getData')->name('getData');
Route::get('getArticulosFavoritos', 'HomeController@getArticulosFavoritos')->name('getArticulosFavoritos');
Route::get('getDataArticulo/{ID}', 'HomeController@getDataArticulo')->name('getDataArticulo');
Route::post('AddFavs', 'HomeController@AddFavs')->name('AddFavs');
Route::get('getLotes/{BODEGA}/{ARTICULO}', 'HomeController@getLotes')->name('getLotes');
Route::get('getDetallesFactura/{ID}', 'HomeController@getDetallesFactura')->name('getDetallesFactura');

Route::get('getDataCliente', 'HomeController@getDataCliente')->name('getDataCliente');
Route::get('getDataCliente/{ID}', 'HomeController@getDataCliente')->name('getDataCliente');


Route::get('Usuarios', 'UsuarioController@getUsuarios')->name('Usuarios');
Route::post('SaveUsuario', 'UsuarioController@SaveUsuario')->name('SaveUsuario');
Route::post('DeleteUsuario', 'UsuarioController@DeleteUsuario')->name('DeleteUsuario');
Route::get('getRutas', 'UsuarioController@getRutas')->name('getRutas');

Route::post('AsignarRuta', 'UsuarioController@AsignarRuta')->name('AsignarRuta');
Route::post('RemoverRutaAsignada', 'UsuarioController@RemoverRutaAsignada')->name('RemoverRutaAsignada');

Route::post('ChancesStatus', 'HomeController@ChancesStatus')->name('ChancesStatus');
Route::post('getCommentPedido', 'HomeController@getCommentPedido')->name('getCommentPedido');
Route::post('AddCommentPedido', 'HomeController@AddCommentPedido')->name('AddCommentPedido');
Route::post('DeleteCommentPedido', 'HomeController@DeleteCommentPedido')->name('DeleteCommentPedido');
Route::post('getPedidosRangeDates', 'HomeController@getPedidosRangeDates')->name('getPedidosRangeDates');
