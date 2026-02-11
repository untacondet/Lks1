<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticable;

class Admin extends Authenticable
{
    use HasApiTokens;

    protected $table = 'administrators';
    
    protected $fillable = [
        'username',
        'password',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }
}
