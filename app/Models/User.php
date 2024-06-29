<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'name',
        'birthdate',
        'address',
        'sex',
        'status',
        'phone',
        'role',

    ];

    public function scopeSearch($query, $keyword)
    {
        return $query->where('firstname', 'LIKE', "%{$keyword}%")
                     ->orWhere('middlename', 'LIKE', "%{$keyword}%")
                     ->orWhere('lastname', 'LIKE', "%{$keyword}%")
                     ->orWhere('status', 'LIKE', "%{$keyword}%")
                     ->orWhere('role', 'LIKE', "%{$keyword}%");

    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
