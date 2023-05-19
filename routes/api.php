<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user(); 
});

Route::get('/', [LocationController::class, 'get_locations']);

Route::get('get_states/{country_id}', [LocationController::class, 'get_states']);
Route::get('get_cities/{state_id}', [LocationController::class, 'get_cities']);