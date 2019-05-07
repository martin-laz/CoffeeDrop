<?php

use Illuminate\Http\Request;
use App\CoffeeDrop;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('cashback', 'CoffeeDropController@calculate');
Route::get('coffeedrop/{postcode}', 'CoffeeDropController@find');
Route::post('coffeedrop', 'CoffeeDropController@create');
