<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

    class AuthController extends Controller
    {
        public function register(Request $request){

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6',
                'repeat_password' => 'required|string|same:password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => 'error',
                    "error_messages" => $validator->errors()
                ]);
            }

            $password_encrypted = bcrypt($request->password);
            $user  = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $request->password;
            if($user->save()){
                return response()->json([
                    "success" => 1,
                    "message" => 'User is successfully created'
                ]);
            }else{
                return response()->json([
                    "success" => 0
                ]);
            }

    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => 'error',
                "error_messages" => $validator->errors()
            ]);
        }

        if (!$token = JWTAuth::attempt($validator->validated())) {
            return response()->json([
                "success" => 'unauthorize'
            ]);
        }

        return response()->json([
            "success" => 1,
            "jwt_token" => $this->createAuthToken($token)
        ]);

    }

    public function reset_password(Request $request){
        $user = User::where("email", $request->email)->first();
        if(!$user){
            return response()->json([
                "success" => 'unauthorize'
            ]);
        }else{
            $user = User::where("email", $request->email)->first();
            $token = Str::random(32);
            DB::table("password_reset_tokens")->where("email", $request->email)->delete();
            DB::table("password_reset_tokens")->insert(['email' => $request->email, 'token' => $token]);
            $reset_link = env('FRONT_END_BASE_URL').'change_password/'.$token;
            $email = $request->email;
             $data = 
             [
                'user_name' => $user->name,
                'user_id' => $user->id,
                'reset_password_link' => $reset_link
            ];

        Mail::to($email)->send(new PasswordResetMail($data));
            return response()->json([
                "success" => 1
            ]);
        }

    }

    public function change_password(Request $request){
        if($request->password === $request->repeat_password){
            $record = DB::table("password_reset_tokens")->where("token", $request->token)->first();
            $password_encrypted = bcrypt($request->password);
            User::where("email", $record->email)->update([
                "password" => $password_encrypted
            ]);

            DB::table("password_reset_tokens")->where("token", $request->token)->delete();

            return response()->json([
                "success" => 1
            ]);
        }else{
            return response()->json([
                "success" => 'mismatch'
            ]);
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

    public function logout(Request $request){
        auth()->logout();

            return response()->json([
                "success" => 1,
                "message" => 'User is successfully logout'
            ]);
    }

    }

