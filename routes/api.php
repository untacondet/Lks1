<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\setController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route :: post('/register',[AuthController :: class, 'register']);
Route :: post('/login',[AuthController::class,'login']);

Route :: middleware('auth:sanctum')->group(function() {

    Route :: get('/courses',[CourseController::class,'getpublished']);
    Route :: get('courses/{course_sluf}',[CourseController :: class,'getcoursedetail']);

    Route :: post('lesson-contents/{content_id}/check-answer',[LessonController :: class , 'checkanswer']);
    Route :: post('/lessons/{lessons_id}/contents/{contens_id}/check',[LessonController :: class , 'checkanswer']);

    Route :: put('/lessons/{lesson_id}/complete',[LessonController :: class,'completedlesson']);

    Route :: post('/courses/{slug}/register',[UserController :: class, 'RegisterToCourse']);

    Route :: get('/users/progress',[UserController::class,'GetProgress']);

    Route :: post('/logout',[AuthController::class,'logout']);


    Route :: middleware('is_admin')->group(function ()  {
        Route :: post('/courses',[CourseController::class, 'store']);
        Route :: put('/courses/{courses_slug}',[CourseController::class,'update']);
        Route :: delete('/courses/{courses_slug}',[CourseController::class,'destroy']);
        Route :: post('/courses/{courses}/sets',[setController::class,'add']);
        Route :: delete('/courses/{course}/sets/{set_id}',[setController::class,'delete']);
        Route :: post('/lessons',[LessonController :: class,'store']);
        Route :: delete('/lessons/{lesson_id}',[LessonController :: class , 'destroy']);
        
    });
    
    
});