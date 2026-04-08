<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function registerToCourse(Request $request, $courseSlug)
    {
        try{
            $course = Course::where('slug', $courseSlug)->firstOrFail();

            $existingEnrollment = Enrollment::where([
                'user_id' => $request->user()->id,
                'course_id' => $course->id
            ])->exists();

            if($existingEnrollment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The user is already registered for this course'
                ],400);
            }

            Enrollment::create([
                'user_id' => $request->user()->id,
                'course_id' => $course->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successful'
            ], 201);

        }catch(ModelNotFoundException $e){
            return response()->json([
                'status' => 'not_found',
                'message' => 'Resource not found'
            ],404);
        }
    }

    public function getProgress(Request $request){
         $user = $request->user();

    $progress = $user->enrollments()->
    with(['course' => function ($query) {
        $query ->select('id', 'name', 'slug', 'description', 'is_published', 'created_at', 'updated_at');
    }])->get()
    ->map(function ($enrollment) use ($user) {
        $completedlesson =$user->completedlesson()
    ->join('lessons', 'completed_lessons.lesson_id', '=', 'lessons.id')
    ->join('sets', 'lessons.set_id', '=', 'sets.id')                
    ->where('sets.course_id', $enrollment->course_id)              
    ->select('lessons.id', 'lessons.name', 'lessons.order')         
    ->get();

        return[
            'course' => $enrollment->course,
            'completed_lessons'=>$completedlesson
        ];
    });

    return response()->json([
            'status' => 'success',
            'message' => 'User progress retrieved successfully',
            'data' => [
                'progress' => $progress
            ]
    ],200);
    }
}
