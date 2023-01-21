<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register (request $request){

        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'succes' => true,
            'data' => $user,
            'acces_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
    public function login(Request $request){
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'succes' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'succes' => true,
            'message' => 'Login succes',
            'acces_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
    public function logout(){

        Auth::user()->tokens()->delete();
        return response()->json([
            'succes' => true,
            'message'=> 'logout succes'
        ]);
    } 
}
