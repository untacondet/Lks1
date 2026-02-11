<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next) {
        if(!$request->bearerToken()) {
            return response()->json([
                'status' => 'invalid_token',
                'message' => 'Invalid or expired token'
            ],401);
        }

        if(!$request->user()){
            return response()->json([
                'status' => 'invalid_token',
                'message' => 'Invalid or expired token'
            ],401);
        }

        $isAdmin = Admin::where('username', $request->user()->username)->exists();

        if(!$request->user()->tokenCan('admin')) {
            return response()->json([
                'status' => 'insufficient_permisson',
                'message' => 'Access forbidden'
            ],403);
        }
        return $next($request);
    }
}
