<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if($response->headers->get('Content-Type') == 'applicatio/json'){
            return $response;
        }

        switch ($response->status()){
            case 401:
                return response()->json([
                    'status' => 'invalid_token',
                    'message' => 'Invalid or expired token'
                ],401);

                case 403:
                    return response()->json([
                        'status' => 'insufficient_permission',
                        'message' => 'Access forbidden'
                    ],403);

                case 404:
                    return response()->json([
                        'status' => 'not_found',
                        'message' => 'Resource not found'
                    ],404);

                    default:
                        return $response;
        }
    }
}
