<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request) 
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username',
            'password'  => 'required|string|min:6',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'User registered successfully',
            'data'    => [
                'id'       => $user->id,
                'username' => $user->username,
                'role'     => 'user',
                'token'    => $token,
            ],
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // cek user dulu
        $user = User::where('username', $request->username)->first();
        $role = 'user';

        // kalau tidak ada di users, cek admin
        if (! $user) {
            $user = Admin::where('username', $request->username)->first();
            $role = 'admin';
        }

        // kalau tidak ditemukan atau password salah
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credential',
            ], 401);
        }

        $token = $user->createToken('login-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful',
            'data'    => [
                'id'       => $user->id,
                'username' => $user->username,
                'role'     => $role,
                'token'    => $token,
            ],
        ], 200);
    }

    // LOGOUT
    public function logout(Request $request)
{
    if (!$request->user()) {
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    $request->user()->tokens()->delete();

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
}
}