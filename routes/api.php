<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\LessonController;
use App\Http\Middleware\EnsureValidToken;

//Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course_slug}', [CourseController::class, 'show']);
Route::post('/courses/{course_slug}', [SetController::class, 'store']);

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json($request->user());
});


Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function(){

    Route::post('/logout', function(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout success (force)'
        ]);
    })->middleware('auth:sanctum');

    Route::post('/courses/{course_slug}/register', [UserController::class, 'registerToCourse']);
    Route::get('/users/progress', [UserController::class, 'getProgress']);

    Route::post('/lesson-contents/{content_id}/check-answer', [LessonController::class, 'checkAnswer']);
    Route::put('/lessons/{lesson_id}/complete', [LessonController::class, 'complete']);

    Route::middleware(AdminMiddleware::class)->group(function() {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{course_slug}', [CourseController::class, 'update']);
        Route::delete('/courses/{course_slug}', [CourseController::class, 'destroy']);
        Route::post('/courses/{course_slug}/sets', [SetController::class, 'store']);
        Route::delete('/courses/{course_slug}/sets/{set_id}',[SetController::class, 'destroy']);
        Route::post('/lessons', [LessonController::class, 'store']);
        Route::delete('/lessons/{lesson_id}', [LessonController::class, 'destroy']);     
    });
});

Route::middleware('auth:sanctum')->get('/test-auth', function(Request $request){
    return response()->json([
        'message' => 'Authenticated',
        'user' => $request->user(),
    ]);
});