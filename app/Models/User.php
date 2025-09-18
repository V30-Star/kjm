<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['username', 'full_name', 'email', 'password', 'role', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'created_at' => 'datetime'];
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
