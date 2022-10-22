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
Route::get('/send', [AuthController::class, 'sampleMail']);
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
    Route::get('/user-type', [AuthController::class, 'getUserType']);
    Route::get('/mechanic-booking', [ShopMechanicController::class, 'getMechanicBooking']);
    Route::post('/deny-booking', [ShopMechanicController::class, 'denyBooking']);
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/mechanic-location', [ShopMechanicController::class, 'mechanicLocation']);
    Route::post('/send-message', [MessageController::class, 'sendMessage']);
    Route::post('/conversation', [MessageController::class, 'conversation']);
    Route::get('/mechanic-data', [ShopMechanicController::class, 'mechanicData']);
    Route::get('/has-accepted-booking', [ShopMechanicController::class, 'hasAcceptedBooking']);
    Route::post('/fix', [ShopMechanicController::class, 'fix']);
    Route::post('/done', [ShopMechanicController::class, 'done']);
    Route::post('/submit-violation', [ShopMechanicController::class, 'submitReport']);
    Route::get('/shop-locations', [ShopMechanicController::class, 'showShopLocations']);
    Route::get('/customer-transaction', [ShopMechanicController::class, 'customerTransaction']);
    Route::post('/mark-as-paid', [ShopMechanicController::class, 'markAsPaid']);
});


