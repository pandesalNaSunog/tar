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
use App\Models\OTP;

class AuthController extends Controller
{
    public function register(Request $request){
        function generateOTP(){
            $numbers = "1234567890";
            $otp = "";
            for($i = 0; $i < 6; $i++){
                $index = rand(0, strlen($numbers) - 1);
                $otp .= $numbers[$index];
            }

            return $otp;
        }

        $request->validate([
            'user_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'contact_number' => 'required',
            'valid_id' => 'required',
            'password' => 'required',
            'email' => 'required'
        ]);

        $checkUser = User::where('contact_number', $request['contact_number'])->orWhere('email', $request['email'])->first();

        if($checkUser){
            return response([
                'message' => 'contact/email address already exists'
            ], 401);
        }


        $validId = base64_decode($request['valid_id']);
        $filepath = uniqid() . ".jpg";
        file_put_contents($filepath, $validId);

        


        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'contact_number' => $request['contact_number'],
            'password' => bcrypt($request['password']),
            'user_type' => $request['user_type'],
            'email' => $request['email'],
            'valid_id' => $filepath,
            'approval_status' => 'Pending',
            'status' => 'idle',
            'verified' => 'no',
            'shop_name' => $request['shop_name'],
            'shop_address' => $request['shop_address']
        ]);


        $token = $user->createToken('myAppToken')->plainTextToken;

        $otpText = generateOTP();

        $otp = OTP::where('user_id', $user->id)->first();

        if(!$otp){
            $otp = OTP::create([
                'user_id' => $user->id,
                'otp' => $otpText
            ]);
        }else{
            $otp->update([
                'otp' => $otpText
            ]);
        }
        

        


        $mail = new PHPMailer(true);

        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tapandrepair@gmail.com';
        $mail->Password = 'jamxdnzynricpvlr';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('tapandrepair@gmail.com', 'Tap And Repair');
        $mail->addAddress($request['email']);
        $mail->isHTML(true);

        $mail->Subject = 'One Time Password';
        $mail->Body = 'Your OTP is ' . $otpText . ". ";

        if(!$mail->send()){
            $user->delete();
            return response ([
                'message' => 'email is invalid'
            ], 401);
        }

        return response([
            'message' => 'we have sent you an OTP.',
            'token' => $token
        ], 200);
        
    }

    public function sendOtp(Request $request){
        $request->validate([
            'otp' => 'required'
        ]);

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $otp = OTP::where('otp', $request['otp'])->where('user_id', $id)->first();

        if($otp){

            $otp->delete();
            $user = User::where('id', $id)->first();
            $user->update([
                'verified' => 'yes',
            ]);
            return response([
                'message' => 'verified',
            ], 200);
        }else{
            return response([
                'message' => 'invalid otp'
            ], 400);
        }
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
            ], 404);
        }

        if($user->verified == 'no'){
            return response([
                'message' => 'your account is not verified'
            ], 400);
        }

        if($user->approval_status != 'Approved'){
            return response([
                'message' => 'Your account is not yet approved. Please wait for the administrator to approve your account.'
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
        $mail->Username = 'tapandrepair@gmail.com';
        $mail->Password = 'jamxdnzynricpvlr';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('tapandrepair@gmail.com', 'Tap And Repair');
        $mail->addAddress('floresjem8@gmail.com');
        $mail->isHTML(true);

        $mail->Subject = 'Sample Subject';
        $mail->Body = 'Sample Body';

        $mail->send();
    }
}
