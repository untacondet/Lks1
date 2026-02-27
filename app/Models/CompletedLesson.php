<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Lesson;
use App\Models\User;

class CompletedLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
