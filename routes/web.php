<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;

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
Route::post('/get-states', [LocationController::class, 'get_states']);
Route::post('/get-cities', [LocationController::class, 'get_cities']);

Route::post('/', [LocationController::class, 'submit_location']);