<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)  {
        $validator = Validator :: make($request->all(), [
            'full_name' => 'required',
            'username' => 'required|min:3|unique:users|regex:/^[a-zA-Z0-9._-]+$/',
            'password' => 'required|min:6',
        ]);

        if($validator -> fails()){
            return response()->json([
                "status" => "error",
                "message"=> "Invalid field(s) in request",
                "errors" => $validator->errors(),
            ],400);
        };

        
        $user = User :: create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'password' => Hash :: make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->token = $token;
        $user->role = 'user';

        return response()->json([
        "status" => "success",
        "message" => "User registration successful",
        "data" => $user ,
        ],201);
    }

    public function login(Request $request)  {
        $validator = Validator :: make($request->all(),[
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field(s) in request',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = Admin :: where('username', $request ->username) -> first();

        if($user){
             $user->role='admin';
        }else{
            $user = User :: where('username',$request->username)->first();
            if($user){
                $user->role='user';
            }
        }


        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json([
                "status"=> "authentication_failed",
                "message"=> "The username or password you entered is incorrect"
            ],400);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->token = $token;

        return response()->json([
            "status"=> "success",
            "message"=> "Login successful",
            "data"=>$user,
        ]);

    }

    public function logout(Request $request) {
        
    $request->user()->tokens()->delete();
    return response()->json([
        "status"=> "success",
        "message"=> "Logout successful",
    ],200);
    }
}