<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;
use App\Models\LessonContent;
use App\Models\Option;
use App\Models\CompletedLesson;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class LessonController extends Controller
{
public function store (Request $request) {
    try {
        $validated = $request->validate([
            'name' => 'required|string',
            'set_id' => 'required|exists:sets,id',
            'contents' => 'required|array',
            'contents.*.type' => 'required|in:learn,quiz',
            'contents.*.content' => 'required|string',
            'contents.*.order' => 'required|integer|min:0',
            'contents.*.options' => 'required_if:contents.*.type,quiz|array',
            'contents.*.options.*.option_text' => 'required-if:contents.*.type,quiz|string',
            'contents.*.options.*.is_correct' => 'required_if:contents.*.type,quiz|boolean',
        ]);

        DB::beginTransaction();

        $lesson = Lesson::create([
            'name' => $request->name,
            'set_id' => $request->set_id,
            'order' => $request->order ?? 0
        ]);

        foreach($request->contents as $index => $contentData){
            $content = LessonContent::create([
                'lesson_id' => $request->lesson_id,
                'type' => $request->type,
                'content' => $request->content,
                'order' => $request->order ?? 0
            ]);
        

        if($contentData['type'] === 'quiz' && isset($contentData['options'])) {
            foreach ($contentData['options'] as $option) {
                Option::create([
                    'lesson_content_id' => $content->id,
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct']
                ]);
            } 
        }
        }
    

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Lesson successfully added',
            'data' => [
                'name' => $lesson->name,
                'order' => $lesson->order,
                'id' => $lesson->id
            ]
        ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field(s) in request',
                'errors' => $e->errors()
            ],400);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;

        }
    }

    public function destroy($lessonId) {
        $lesson = Lesson::find($lessonId);

        if(!$lesson) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Resource not found'
            ],404);
        }

        $lesson->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Lesson successfully deleted'
        ], 200);
    }

    public function checkAnswer(Request $request, $contentId)
    {
        $lessonContent = LessonContent::find($contentId);

        if(!$lessonContent) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Resource not found'
            ], 404);
        }
        
        $request->validate([
            'option_id' => 'required|exists:options,id'
        ]);

        if($lessonContent->type !== 'quiz'){
            return response()->json([
                'status' => 'error',
                'message' => 'Only for quiz content'
            ],400);
        }

        $selectedOption = Option::find($request->option_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Check answer success',
            'data' => [
                'question' => $lessonContent->content,
                'user_answer' => $selectedOption->option_text,
                'is_correct' => $selectedOption->is_correct
            ]
        ], 200);
    }
    public function complete(Request $request, $lessonId) 
    {
        try {
            $lesson = Lesson::find($lessonId);

            $existingComplete = CompletedLesson::where([
                'user_id' => $request->user()->id,
                'lesson_id' => $lessonId
            ])->exists();

            if($existingComplete) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lesson already completed'
                ], 400);
            }

            CompletedLesson::create([
                'user_id' => $request->user()->id,
                'lesson_id' => $lessonId
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Lesson successfully completed'
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Resource not found'
            ], 404);
        }
    }
}