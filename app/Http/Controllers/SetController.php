<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\CourseSlug;
use App\Models\Set;

class SetController extends Controller
{
    public function index($courseSlug){
        $course = Course::where('slug', 'courseSlug')->first();
        $sets = $course->sets;

        return response()->json([
            'status' => 'success',
            'message' => 'Sets retrieved successfuly'
        ], 200);
    }

    public function store(Request $request, $courseSlug){
        try{
            $course = Course::where('slug', $courseSlug)->firstOrfail();

            $request->validate([
                'name' => 'required|string'
            ]);

            $maxOrder = Set::where('course_id', $course->id)
                        ->max('order') ?? -1;
            $set = Set::create([
                'name' => $request->name,
                'course_id' => $course->id,
                'order' => $maxOrder + 1
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Set successfuly added',
                'data' => [
                    'name' => $set->name,
                    'order' => $set->order,
                    'id' => $set->id
                ]
                ], 201);
        } catch(ValidationException $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field(s) in request',
                'errors' => $e->errors()
            ],400);
        }
    }

    public function destroy(Set $set, $set_id)
    {
        $set = Set::where('id', $set_id)->first();
        if($set == null){
            return response()->json([
                'status' => 'error',
                'message' => 'Set not found'
            ], 200);
        }
    }
}
