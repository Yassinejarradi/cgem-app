<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validator extends Model
{
    use HasFactory;

    protected $table = 'validators';

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
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_validator_assignments');
    }
}
