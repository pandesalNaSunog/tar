<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use Laravel\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
class ShopMechanicController extends Controller
{
    public function getMechanics(){
        $mechanics = User::where('user_type', 'mechanic')->get();

        return response($mechanics, 200);
    }

    public function book(Request $request){
        $request->validate([
            'shop_mechanic_id' => 'required',
            'lat' => 'required',
            'long' => 'required'
        ]);

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;
        $booking = Booking::create([
            'customer_id' => $id,
            'shop_mechanic_id' => $request['shop_mechanic_id'],
            'lat' => $request['lat'],
            'long' => $request['long'],
        ]);

        return response([
            'message' => 'successfully booked'
        ], 200);
    }
}
