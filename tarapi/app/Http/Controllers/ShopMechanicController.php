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

    public function fix(Request $request){
        $request->validate([
            'booking_id' => 'required'
        ]);

        $booking = Booking::where('id', $request['booking_id'])->first();
        if(!$booking){
            return response([
                'message' => 'does not exist'
            ], 400);
        }
        $booking->update([
            'status' => 'fixing'
        ]);

        return response([
            'status' => $booking->status
        ], 200);
    }

    public function done(Request $request){
        $request->validate([
            'booking_id' => 'required'
        ]);

        $booking = Booking::where('id', $request['booking_id'])->first();
        if(!$booking){
            return response([
                'message' => 'does not exist'
            ], 400);
        }
        $booking->update([
            'status' => 'done'
        ]);

        return response([
            'status' => $booking->status
        ], 200);
    }

    public function mechanicData(Request $request){
        function getAcceptancePercentage($id, $type){
            $bookings = Booking::where('shop_mechanic_id', $id)->get();
            $bookingItems = 0;
            $acceptedBookings = 0;
            foreach($bookings as $bookingItem){
                $bookingItems++;
                if($bookingItem->status == "accepted"){
                    $acceptedBookings++;
                }
            }

            if($bookingItems == 0){
                $rate = 0.0;
            }else{
                if($type == 'acceptance'){
                    if($acceptedBookings != 0){
                        $rate = ($bookingItems / $acceptedBookings) * 100;
                    }else{
                        $rate = 0.0;
                    }
                    
                }else{
                    if($bookingItems - $acceptedBookings != 0){
                        $rate = ($bookingItems / ($bookingItems - $acceptedBookings)) * 100;
                    }else{
                        $rate = 0.0;
                    }
                }
            }
            
            

            $rounded = round($rate, 2);

            return $rounded;
        }
        
        function calculateRatings($id){
            $ratings = Rating::where('mechanic_shop_id', $id)->get();
            $ratingItems = 0;
            $totalRatings = 0.0;
            foreach($ratings as $ratingItem){
                $ratingItems++;
                $totalRatings += $ratingItem['rating'];
            }
            if($ratingItems != 0){
                $averageRating = round(($totalRatings / $ratingItems), 2);
            }else{
                $averageRating = 0.0;
            }
            

            return $averageRating;
        }

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $mechanic = User::where('id', $id)->first();
        
        $averageRating = calculateRatings($id);
        $acceptanceRate = getAcceptancePercentage($id, "acceptance");
        $cancelationRate = getAcceptancePercentage($id, "cancellation");


        return response([
            'mechanic' => $mechanic,
            'rating' => $averageRating,
            'acceptance' => $acceptanceRate,
            'cancellation' => $cancelationRate
        ], 200);

        
    }
    public function mechanicLocation(Request $request){

        function calculateDistance($latFrom, $longFrom, $latTo, $longTo){
            $earthRadius = 6371;

            $latitudeFrom = deg2rad($latFrom);
            $longitudeFrom = deg2rad($longFrom);
            $latitudeTo = deg2rad($latTo);
            $longitudeTo = deg2rad($longTo);

            $latDelta = $latitudeTo - $latitudeFrom;
            $longDelta = $longitudeTo - $longitudeFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latitudeFrom) * cos($latitudeTo) * pow(sin($longDelta / 2), 2)));
            
            return round(($angle * $earthRadius), 1);
        }

        $request->validate([
            'lat' => 'required',
            'long' => 'required',
            'mechanic_id' => 'required'
        ]);


        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $user = User::where('id', $id)->first();

        $user->update([
            'lat' => $request['lat'],
            'long' => $request['long']
        ]);

        $mechanic = User::where('id', $request['mechanic_id'])->first();

        $distance = calculateDistance($user->lat, $user->long, $mechanic->lat, $mechanic->long);
        $speed = 40;

        $booking = Booking::where('customer_id', $user->id)->where('shop_mechanic_id', $mechanic->id)->first();

        //speed = distance / time
        //time * speed = distance
        //time = distance / speed
        
        $time = 60 * ($distance / $speed);
        return response([
            'mechanic' => [
                'name' => $mechanic->first_name . " " . $mechanic->last_name,
                'lat' => $mechanic->lat,
                'long' => $mechanic->long
            ],
            'me' => [
                'name' => 'Your Location',
                'lat' => $user->lat,
                'long' => $user->long
            ],
            'travel' => [
                'distance' => $distance,
                'time' => round($time, 2)
            ],
            'booking_status' => $booking->status
        ], 200);

    }
    public function getMechanics(Request $request){

        function calculateDistance($latFrom, $longFrom, $latTo, $longTo){
            $earthRadius = 6371;

            $latitudeFrom = deg2rad($latFrom);
            $longitudeFrom = deg2rad($longFrom);
            $latitudeTo = deg2rad($latTo);
            $longitudeTo = deg2rad($longTo);

            $latDelta = $latitudeTo - $latitudeFrom;
            $longDelta = $longitudeTo - $longitudeFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latitudeFrom) * cos($latitudeTo) * pow(sin($longDelta / 2), 2)));

            return round(($angle * $earthRadius), 1);
        }

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $bookings = Booking::where('customer_id', $id)->where(function($query){
            $query->where('status', 'accepted')->orWhere('status', 'pending',);
        })->first();
        if($bookings){
            return response([
                'message' => 'you are currently booked to a mechanic/shop'
            ], 401);
        }



        $me = User::where('id', $id)->first();

        $myLat = $me->lat;
        $myLong = $me->long;

        $mechanics = User::where('user_type', 'mechanic')->where('status', 'idle')->get();
        $response = array();
        foreach($mechanics as $mechanicItem){
            $mechanicId = $mechanicItem->id;

            $mechanicLong = $mechanicItem->long;
            $mechanicLat = $mechanicItem->lat;

            $ratingItems = 0;
            $totalRatings = 0;
            $ratings = Rating::where('mechanic_shop_id', $mechanicId)->get();



            $distance = calculateDistance($myLat,$myLong,$mechanicLat, $mechanicLong);
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
                'distance' => $distance,
                'average_rating' => round($averageRating, 2)
            );
        }
        $distances = array();
        foreach($response as $responseItem){
            $distances[] = $responseItem['distance'];
        }

        array_multisort($distances, SORT_ASC, $response);

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

        $checkBooking = Booking::where('customer_id', $id)->where('status','pending')->first();
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

        return response([
            'customer_id' => $booking->customer_id,
        ], 200);
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
        if($user->status == 'busy'){
            $user->update([
                'status' => 'idle'
            ]);
        }
        $booking->update([
            'status' => 'denied',
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
        $shopMechanicId = $booking->shop_mechanic_id;

        $shopMechanicUser = User::where('id', $shopMechanicId)->first();
        $shopMechanicName = $shopMechanicUser->first_name . " " . $shopMechanicUser->last_name;
        if($status == 'denied'){
            $booking->delete();
        }
        

        return response([
            'status' => $status,
            'shop_mechanic_id' => $shopMechanicId,
            'shop_mechanic_name' => $shopMechanicName
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
            'status' => $booking->status
        ];

        return response($booking, 200);
    }

    public function getShops(Request $request){
        function calculateDistance($latFrom, $longFrom, $latTo, $longTo){
            $earthRadius = 6371;

            $latitudeFrom = deg2rad($latFrom);
            $longitudeFrom = deg2rad($longFrom);
            $latitudeTo = deg2rad($latTo);
            $longitudeTo = deg2rad($longTo);

            $latDelta = $latitudeTo - $latitudeFrom;
            $longDelta = $longitudeTo - $longitudeFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latitudeFrom) * cos($latitudeTo) * pow(sin($longDelta / 2), 2)));

            return round(($angle * $earthRadius), 1);
        }
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;
        $bookings = Booking::where('customer_id', $id)->where(function($query){
            $query->where('status', 'accepted')->orWhere('status', 'pending',)->first();
        });

        if($bookings){
            return response([
                'message' => 'you are already book to a mechanic/shop',
            ], 401);
        }

        $me = User::where('id', $id)->first();

        $myLat = $me->lat;
        $myLong = $me->long;

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

            $shopLat = $mechanicItem->lat;
            $shopLong = $mechanicItem->long;

            $distance = calculateDistance($myLat, $myLong, $shopLat, $shopLong);

            if($totalRatings == 0 || $ratingItems == 0){
                $averageRating = 0;
            }else{
                $averageRating = $totalRatings / $ratingItems;
            }
            

            $response[] = array(
                'mechanic' => $mechanicItem,
                'distance' => $distance,
                'average_rating' => round($averageRating, 2)
            );
        }
        $distances = array();
        foreach($response as $responseItem){
            $distances[] = $responseItem['distance'];
        }

        array_multisort($distances, SORT_ASC, $response);

        return response($response, 200);
    }

    public function hasBooking(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;
        $booking = Booking::where('customer_id', $id)->where(function($query){
            $query->where('status','pending')
                ->orWhere('status', 'accepted');
        })->first();
                        
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

    public function hasAcceptedBooking(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $booking = Booking::where('shop_mechanic_id', $id)->where('status', 'accepted')->first();

        if(!$booking){
            return response([
                'has_booking' => false,
                'customer_id' => 0,
                'booking_id' => 0,
            ], 200);
        }

        return response([
            'has_booking' => true,
            'customer_id' => $booking->customer_id,
            'booking_id' => $booking->id
        ], 200);
    }
}
