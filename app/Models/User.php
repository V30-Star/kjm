<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];
}
