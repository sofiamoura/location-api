<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TimezoneController;
use App\Http\Controllers\CityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LocationController::class, 'get_locations']);

Route::get('get_states/{country_id}', [LocationController::class, 'get_states']);
Route::get('get_cities/{state_id}', [LocationController::class, 'get_cities']);

Route::get('/get_country/{state_id}', [LocationController::class, 'get_country']);
Route::get('/get_state/{city_id}', [LocationController::class, 'get_state']);
Route::get('/get_timezone/{city_id}', [LocationController::class, 'get_timezone']);


Route::get('/get_timezone/{city_id}', [LocationController::class, 'get_timezone']);