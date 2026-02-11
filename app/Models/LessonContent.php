<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'type',
        'content',
        'order'
    ];

    protected $casts = [
        'type' => 'string'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function scopeLearn($query)
    {
        return $query->where('type', 'learn');
    }

    public function scopeQuiz($query)
    {
        return $query->where('type', 'quiz');        
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function isQuiz()
    {
        return $this->type === 'quiz';
    }
}
