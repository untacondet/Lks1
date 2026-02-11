<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_content_id',
        'option_text',
        'is_correct'
    ];

    protected $hidden = [
        'is_correct'
    ];

    public function lessonContent()
    {
        return $this->belongsTo(LessonContent::class);
    }
}
