<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Sanctum;
use App\Models\Message;
use Laravel\Sanctum\PersonalAccessToken;
class MessageController extends Controller
{
    public function sendMessage(Request $request){
        $request->validate([
            'message' => 'required',
            'receiver_id' => 'required'
        ]);

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;


        $receiver = User::where('id', $request['receiver_id'])->first();

        if(!$receiver){
            return response([
                'message' => 'receiver does not exist'
            ], 404);
        }

        $message = Message::create([
            'sender_id' => $id,
            'receiver_id' => $request['receiver_id'],
            'message' => htmlspecialchars($request['message'])
        ]);

        return response($message, 200);
    }

    public function conversation(Request $request){
        $request->validate([
            'receiver_id' => 'required'
        ]);

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $id = $token->tokenable->id;

        $messages = Message::where('sender_id', $id)->where('receiver_id', $request['receiver_id'])->orWhere('receiver_id', $id)->where('sender_id', $request['receiver_id'])->get();
        $response = array();
        $receiver = User::where('id', $request['receiver_id'])->first();

        foreach($messages as $messageItem){
            if($messageItem->sender_id == $id){
                $mine = true;
            }else{
                $mine = false;
            }

            $response[] = [
                'message' => $messageItem->message,
                'mine' => $mine,
                'date' => date_format(date_create($messageItem->created_at), "M d, Y h:i A")
            ];
        }

        return response([
            'receiver' => $receiver,
            'conversation' => $response
        ], 200);
    }
}
