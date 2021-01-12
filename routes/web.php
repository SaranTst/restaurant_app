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
    return view('restaurant');
});

Route::get('/api/restaurant/getCsrf', 'Api\ApiRestaurantController@getCsrf');
Route::get('/api/restaurant/getCache', 'Api\ApiRestaurantController@getCache');
Route::post('/api/restaurant/searchRestaurants', 'Api\ApiRestaurantController@searchRestaurants');
