<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcheteurActions extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_number', 
        'acheteur_id', 
        'action',
    ];

    // Relationship to the User model
    public function acheteur()
    {
        return $this->belongsTo(User::class, 'acheteur_id');
    }
}
