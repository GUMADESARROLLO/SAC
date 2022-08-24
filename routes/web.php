<?php

use Illuminate\Support\Facades\Route;

Route::get('Home', 'HomeController@getHome')->name('Home');
Route::get('/', 'HomeController@getHome');