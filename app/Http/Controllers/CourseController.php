<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Validated;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\errors;

use function Laravel\Prompts\error;

class CourseController extends Controller
{
    public function store(Request $request) {
      $validator = Validator::make($request->all,[
            'name' => 'required|string',
            'description' => 'nullable|string',
            'slug' => 'required|string|unique:courses,slug'
      ]);

        $course = Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => $request->slug,
            'is_published' => false
        ]);

        return response()->json([
            'status' => 'Error',
            'messages' => 'Invalid field(s) request',
            'Error' => $validator->errors()
        ],400);
    }

    public function update(Request $request, $courseSlug) {
        $course = Course::where('slug', $courseSlug)->first();

        if(!$course) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Course not found'
            ],400);
        }
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'is_published' => 'required|boolean'
        ]);

        $course->update($validatedData);
        return response()->json([
            'name' => $request->name,
            'description' => $request->description,
            'data' => $course
        ]);

        return response()->json([
            'status' => 'not_found',
            'message' => 'Resource not found'
        ]);
    }
    
    public function destroy($courseSlug) {
        $course = Course::where('slug', $courseSlug)->first();

        if(!$course){
            return response()->json([
                'status' => 'Error',
                'message' => 'Course not found'
            ],404);
        }
        $course->delete();

        return response()->json([
            'status' => 'Success',
            'message' => 'Course successfully deleted'
        ],200);
    }

    public function index() {
        $courses = Course::where('is_published', true)->get();

        if($courses->isEmpty()) {
            return response()->json([
                'status' => 'failed',
            ]);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Courses retrieved successfully',
            'data' => [
                'courses' => $courses
            ]
        ],200);
    }

    public function show($courseSlug){
        $course = Course::with([
            'sets' => function($query) {
                $query->orderBy('order')
                ->with(['lessons' => function ($query){
                    $query->orderBy('order')
                    ->with(['contents' => function ($query){
                        $query->orderBy('order')
                        ->with(['options' => function ($query){
                            $query->select('id', 'lesson_content_id', 'option_text');
                        }]);
                    }]);
                }]);
            }])->where('slug', '$courseSlug')->first();
            
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid token'
            ],200);
    }
}