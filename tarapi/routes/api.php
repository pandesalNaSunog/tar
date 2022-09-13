<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/register',[AuthController::class, 'register']);
Route::post('/register-mechanic', [AuthController::class, 'registerMechanic']);
Route::post('/register-owner', [AuthController::class, 'registerOwner']);
Route::get('/mechanics', [ShopMechanicController::class, 'getMechanics']);
Route::post('/login', [AuthController::class, 'login']);
Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/book', [ShopMechanicController::class, 'book']);
});


