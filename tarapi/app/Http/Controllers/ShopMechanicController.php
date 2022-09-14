<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use Laravel\Sanctum;
use App\Models\Rating;
use Laravel\Sanctum\PersonalAccessToken;
class ShopMechanicController extends Controller
{
    public function getMechanics(Request $request){

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $bookings = Booking::where('customer_id', $id)->first();
        if($bookings){
            return response([
                'message' => 'you are currently booked to a mechanic/shop'
            ], 401);
        }
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
            'status' => 'pending',
        ]);

        return response([
            'message' => 'successfully booked'
        ], 200);
    }

    public function submitRating(Request $request){
        $request->validate([
            'mechanic_shop_id' => 'required',
            'rating' => 'required',
        ]);

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;
        $rating = Rating::create([
            'user_id' => $id,
            'mechanic_shop_id' => $request['mechanic_shop_id'],
            'rating' => $request['rating']
        ]);

        return response($rating,200);
    }

    public function acceptBooking(Request $request){
        $request->validate([
            'booking_id' => 'required'
        ]);
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $user = User::where('id', $id)->first();

        $userType = $user->user_type;

        if($userType != 'mechanic' && $userType != 'owner'){
            return response([
                'message' => 'you are not mechanic/shop',
            ], 401);
        }

        $booking = Booking::where('id', $request['booking_id'])->first();

        $booking->update([
            'status' => 'accepted'
        ]);

        return response($booking, 200);
    }

    public function checkBookingStatus(Request $request){
        $request->validate([
            'booking_id' => 'required'
        ]);

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $booking = Booking::where('id', $request['booking_id'])->where('customer_id', $id)->first();

        return response([
            'status' => $booking->status
        ], 200);
    }
}
