<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile_setting(Request $request){
        $user  = Auth::user();
        if($request->user_password == null || $request->user_password == '' || $request->user_password == 'undefined'){
            User::where("id", $user->id)->update([
                "name" => $request->user_name
            ]);
            return response()->json([
                "success" => "user_success",
                "user_data" => User::where("id", $user->id)->first()
            ]);
        }else{
            if($request->user_password != $request->user_reset_password){
                return response()->json([
                    "success" => "mismatch"
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'user_password' => 'required|string|min:6'
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        "success" => 'error',
                        "error_messages" => $validator->errors()
                    ]);
                }
                    $password_encrypted = bcrypt($request->user_password);
                    $name = ($request->user_name) ? $request->user_name : $user->name; 
                    User::where("id", $user->id)->update([
                        "name" => $name,
                        "password" => $password_encrypted
                    ]);
                    return response()->json([
                        "success" => 1,
                        "user_data" => User::where("id", $user->id)->first()
                    ]);
            }
        }
    }

    public function createAuthToken($token){
        return response()->json([
            "access_token" => $token,
            "token_type" => 'bearer',
            "expires_in" => JWTAuth::factory()->getTTL()*60,
            "user" => Auth::user()
        ]);
    }
}
