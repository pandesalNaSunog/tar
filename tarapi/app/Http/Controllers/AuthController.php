<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Booking;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'user_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'contact_number' => 'required',
            'password' => 'required',
        ]);

        $checkUser = User::where('contact_number', $request['contact_number'])->first();

        if($checkUser){
            return response([
                'message' => 'contact number already exists'
            ], 401);
        }


        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'contact_number' => $request['contact_number'],
            'password' => bcrypt($request['password']),
            'user_type' => $request['user_type'],
            'email' => $request['email'],
            'approval_status' => 'Pending',
            'status' => 'idle',
        ]);

        return response([
            'message' => 'registered'
        ]
        ,200);
    }

    public function login(Request $request){
        $request->validate([
            'contact_number' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('contact_number', $request['contact_number'])->orWhere('email', $request['contact_number'])->first();

        if(!$user || !Hash::check($request['password'], $user->password)){
            return response([
                'message' => 'invalid contact and password'
            ], 401);
        }

        $token = $user->createToken('myAppToken')->plainTextToken;

        return response([
            'type' => $user->user_type,
            'token' => $token
        ], 200);
    }

    public function profile(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;
        $user = User::where('id', $id)->first(); 
        $booking = Booking::where('customer_id', $id)->get();
        $bookingResponse = array();
        foreach($booking as $bookingItem){
            $bookingId = $bookingItem->id;
            $mechanicOrShopId = $bookingItem->shop_mechanic_id;

            $mechanic = User::where('id', $mechanicOrShopId)->first();

            $name = $mechanic->last_name . "," . $mechanic->first_name;
            $status = $bookingItem->status;

            $bookingResponse[] = array(
                'booking_id' => $bookingId,
                'name' => $name,
                'status' => $status
            );
        }

        return response([
            'user' => $user,
            'bookings' => $bookingResponse
        ], 200);
    }

    public function getUserType(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $user = User::where('id', $id)->first();

        $userType = $user->user_type;

        return response([
            'user_type' => $userType
        ], 200);
    }

    public function sampleMail(){
        $mail = new PHPMailer(true);

        $mail->SMTPDebug = 4;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'floresjem8@gmail.com';
        $mail->Password = 'glbviqmhqvzxoqre';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('admin@tapandrepair.online', 'Tap And Repair');
        $mail->addAddress('floresjem8@gmail.com');
        $mail->isHTML(true);

        $mail->Subject = 'Sample Subject';
        $mail->Body = 'Sample Body';

        $mail->send();

        return response([
            'message' => 'sent'
        ]);
    }
}
