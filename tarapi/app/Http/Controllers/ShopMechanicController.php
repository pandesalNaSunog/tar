<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use Laravel\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
class ShopMechanicController extends Controller
{
    public function getMechanics(Request $request){

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $bookings = Booking::where('customer_id', $id)->first();

        return response($bookings, 200);
        // if($bookings){
        //     return response([
        //         'message' => 'you are currently booked to a mechanic/shop'
        //     ], 401);
        // }
        $mechanics = User::where('user_type', 'mechanic')->where('status', 'idle')->get();

        return response($mechanics, 200);
    }

    public function book(Request $request){
        $request->validate([
            'shop_mechanic_id' => 'required',
            'vehicle_type' => 'required',
            'service' => 'required',
            'lat' => 'required',
            'long' => 'required'
        ]);

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $checkBooking = Booking::where('customer_id', $id)->first();
        if($checkBooking){
            return response([
                'message' => 'you are currently booked'
            ], 401);
        }
        $booking = Booking::create([
            'customer_id' => $id,
            'vehicle_type' => $request['vehicle_type'],
            'service' => $request['service'],
            'shop_mechanic_id' => $request['shop_mechanic_id'],
            'lat' => $request['lat'],
            'long' => $request['long'],
        ]);

        return response([
            'message' => 'successfully booked'
        ], 200);
    }
}
