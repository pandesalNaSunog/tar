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
Route::post('/login', [AuthController::class, 'login']);
Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/book', [ShopMechanicController::class, 'book']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/mechanics', [ShopMechanicController::class, 'getMechanics']);
    Route::post('/rate', [ShopMechanicController::class, 'submitRating']);
    Route::post('/accept-booking', [ShopMechanicController::class, 'acceptBooking']);
    Route::post('/check-booking-status', [ShopMechanicController::class, 'checkBookingStatus']);
    Route::post('/cancel-booking', [ShopMechanicController::class, 'cancelBooking']);
    Route::get('/shops', [ShopMechanicController::class, 'getShops']);
    Route::get('/has-booking', [ShopMechanicController::class, 'hasBooking']);
});


