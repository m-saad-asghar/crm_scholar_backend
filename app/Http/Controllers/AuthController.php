<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
    use Tymon\JWTAuth\Facades\JWTAuth;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Http\Request;

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

    public function createAuthToken($token){
        return response()->json([
            "access_token" => $token,
            "token_type" => 'bearer',
            "expires_in" => JWTAuth::factory()->getTTL()*60,
            "user" => Auth::user()
        ]);
    }

    public function logout(Request $request){
        return "debug";
        auth()->logout();

            return response()->json([
                "success" => 1,
                "message" => 'User is successfully logout'
            ]);
    }

    }

