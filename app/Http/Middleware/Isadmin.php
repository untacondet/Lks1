<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if($request->user() && $request->user() instanceof \App\Models\Admin){
            return $next($request);
        }

        return response()->json([
            "status"=> "insufficient_permissions",
            "message"=> "Access forbidden",
        ],403);
    }
    }