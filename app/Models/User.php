<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Validator;
use Illuminate\Database\Eloquent\Model;
use DB;


use App\Events\UserCreated;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'prenom', 
        'user_id', 
        'email', 
        'join_date', 
        'phone_number', 
        'status', 
        'role_name', 
        'admin', 
        'avatar', 
        'position', 
        'department', 
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the estimates for the user.
     */
    public function estimates()
    {
        return $this->hasMany(Estimates::class);
    }
    public function validators()
    {
        return $this->belongsToMany(Validator::class, 'user_validator_assignments', 'user_id', 'validator_id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'user_validator_assignments', 'validator_id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            event(new UserCreated($user));
        });
    }   

    
}
