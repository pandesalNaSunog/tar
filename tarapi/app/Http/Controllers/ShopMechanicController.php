<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class ShopMechanicController extends Controller
{
    public function getMechanics(){
        $mechanics = User::where('user_type', 'mechanic')->get();

        return response($mechanics, 200);
    }
}
