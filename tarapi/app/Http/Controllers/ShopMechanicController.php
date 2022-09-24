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
        $response = array();
        foreach($mechanics as $mechanicItem){
            $mechanicId = $mechanicItem->id;
            $ratingItems = 0;
            $totalRatings = 0;
            $ratings = Rating::where('mechanic_shop_id', $mechanicId)->get();
            foreach($ratings as $ratingItem){
                $totalRatings += $ratingItem->rating;
                $ratingItems++;
            }
            if($totalRatings == 0 || $ratingItems == 0){
                $averageRating = 0;
            }else{
                $averageRating = $totalRatings / $ratingItems;
            }
        
            $response[] = array(
                'mechanic' => $mechanicItem,
                'average_rating' => round($averageRating, 2)
            );
        }

        return response($response, 200);
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
            'booking_id' => $booking->id
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
        $user->update([
            'status' => 'busy',
        ]);

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

    public function denyBooking(Request $request){
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
            'status' => 'denied'
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
        $status = $booking->status;

        if($status == 'denied'){
            $booking->delete();
        }
        

        return response([
            'status' => $status
        ], 200);
    }

    public function cancelBooking(Request $request){

        $request->validate([
            'booking_id' => 'required'
        ]);

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $booking = Booking::where('id', $request['booking_id'])->where('customer_id', $id)->first();
        $shopId = $booking->shop_mechanic_id;

        $shop = User::where('id', $shopId)->first();

        $shop->update([
            'status' => 'idle',
        ]);
        
        if(!$booking){
            return response([
                'message' => 'does not exist'
            ]);
        }
        $booking->update([
            'status' => 'cancelled by the customer'
        ]);

        $response = [
            'status' => $bookinh->status
        ];

        return response($booking, 200);
    }

    public function getShops(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;
        $bookings = Booking::where('customer_id', $id)->first();

        if($bookings){
            return response([
                'message' => 'you are already book to a mechanic/shop',
            ], 401);
        }

        $shops = User::where('user_type', 'owner')->where('status', 'idle')->get();

        foreach($shops as $mechanicItem){
            $mechanicId = $mechanicItem->id;
            $ratingItems = 0;
            $totalRatings = 0;
            $ratings = Rating::where('mechanic_shop_id', $mechanicId)->get();
            foreach($ratings as $ratingItem){
                $totalRatings += $ratingItem->rating;
                $ratingItems++;
            }
            

            if($totalRatings == 0 || $ratingItems == 0){
                $averageRating = 0;
            }else{
                $averageRating = $totalRatings / $ratingItems;
            }
            

            $response[] = array(
                'mechanic' => $mechanicItem,
                'average_rating' => round($averageRating, 2)
            );
        }

        return response($response, 200);
    }

    public function hasBooking(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;
        $booking = Booking::where('customer_id', $id)->first();
        if($booking){
            return response([
                'message' => 'has booking',
                'status' => $booking->status,
                'id' => $booking->id
            ], 200);
        }

        return response([
            'message' => 'no booking',
            'status' => 'no booking',
            'id' => 0
        ], 200);
    }

    public function getMechanicBooking(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $booking = Booking::where('shop_mechanic_id', $id)->where('status', 'pending')->first();

        if(!$booking){
            return response([
                'message' => 'no bookings',
            ], 401);
        }
        $customerId = $booking->customer_id;
        $user = User::where('id', $customerId)->first();

        return response([
            'booking_id' => $booking->id,
            'customer' => $user,
            'long' => $booking->long,
            'lat' => $booking->lat,
            'service' => $booking->service,
            'vehicle_type' => $booking->vehicle_type
        ]);
    }
}
