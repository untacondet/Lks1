<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use  Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'set_id',
        'order'
    ];

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function contents()
    {
        return $this->hasMany(LessonContent::class);
    }

    public function completions()
    {
        return $this->hasMany(CompletedLesson::class);
    }

    public function completedByUsers()
    {
        return $this->belongsToMany(User::class, 'completed_lessons')
        ->withTimestamps();
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function isCompletedBy(User $user)
    {
        return $this->completions()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
