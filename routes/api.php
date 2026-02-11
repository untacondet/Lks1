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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/courses', [CourseController::class, 'index']);
Route::post('/courses/{course_slug}', [CourseController::class, 'show']);
Route::post('/courses/{course_slug}', [SetController::class, 'store']);

Route::post('auth:sanctum', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth:sanctum', EnsureValidToken::class])->group(function(){
    Route::post('/courses/{course_slug}/register', [UserController::class, 'registerToCourse']);
    Route::get('/users/progress', [UserController::class, 'getProgress']);

    Route::post('/lesson-contents/{content_id}/check-answer', [LessonController::class, 'checkAnswer']);
    Route::post('/lessons{lesson_id}/contents/{content_id}/check', [LessonController::class,'checkAnswer']);
    Route::put('/lessons/{lesson_id}/complete', [LessonController::class, 'complete']);

    Route::middleware(AdminMiddleware::class)->group(function() {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{course_slug}', [CourseController::class, 'update']);
        Route::delete('/courses/{course_slug}', [CourseController::class, 'destroy']);
        Route::post('/courses/{course_slug}/sets', [SetController::class], 'store');
        Route::delete('/courses/{course_slug}/sets/{set_id}',[SetController::class, 'destroy']);
        Route::post('/lessons', [LessonController::class, 'store']);
        Route::delete('/lessons/{lesson_id}', [LessonController::class, 'destroy']);     
    });
});