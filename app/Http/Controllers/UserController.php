<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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

    public function get_users(Request $request){
        $users = User::orderBy("id", "DESC")
        ->where(function ($query) use ($request) {
            if($request->search_term !== ""){
                $query->where('users.name', 'LIKE', '%' . $request->search_term . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $request->search_term . '%')
                    ->orWhere('users.phone', 'LIKE', '%' . $request->search_term . '%');
            }
        })
        ->get();
        return response()->json([
            "users" => $users,
            "success" => 1
        ]);
    }

    public function change_status_user(Request $request, $id){
        $result = DB::table("users")->where("id", $id)->update([
            "active" => ($request->status == true) ? 1 : 0,
        ]);
        if ($result == 1){
            return response()->json([
                "success" => 1
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    }

    public function add_new_user(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => 'repeat',
                "error_messages" => $validator->errors()
            ]);
        }

        $password = "123456";

        $password_encrypted = bcrypt($password);
        $user  = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $password_encrypted;
        $user->active = 1;
        $user->phone = $request->phone;
        if($user->save()){
            $users = User::orderBy("id", "DESC")->get();
            return response()->json([
                "success" => 1,
                "users" => $users,
                "message" => 'User is successfully created'
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }

}

public function update_user(Request $request, $id){
       $result = DB::table("users")->where("id", $id)->update([
        "name" => $request->name,
        "email" => $request->email,
        "phone" => $request->phone
    ]);
    if ($result == 1 || $result == 0){
        $users = DB::table("users")->orderBy('id', 'DESC')->get();
        return response()->json([
            "success" => 1,
            "users" => $users
        ]);
    }else{
        return response()->json([
            "success" => 0
        ]);
    }
}

}
