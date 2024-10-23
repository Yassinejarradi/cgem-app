<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_number',
        'validator_id',
        'user_id',
        'action',
        'acheteur_id',
        'created_at',
    ];

    // Define the relationship with the Validator model
    public function validator()
    {
        return $this->belongsTo(Validator::class);
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
