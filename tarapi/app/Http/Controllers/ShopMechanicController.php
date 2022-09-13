<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
class ShopMechanicController extends Controller
{
    public function getMechanics(){
        $mechanics = User::where('user_type', 'mechanic')->get();

        return response($mechanics, 200);
    }

    public function book(Request $request){
        $request->validate([
            'customer_id' => 'required',
            'shop_mechanic_id' => 'required',
            'lat' => 'required',
            'long' => 'required'
        ]);
        $booking = Booking::create($request->all());

        return response([
            'message' => 'successfully booked'
        ]);
    }
}
