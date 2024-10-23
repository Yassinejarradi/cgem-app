<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleTypeUser extends Model
{
    protected $table = 'role_type_users';
    protected $fillable = ['role_type'];
}
