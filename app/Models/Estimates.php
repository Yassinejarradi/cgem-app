<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimates extends Model
{
    use HasFactory;
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'estimates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'estimate_number',
        'type_demande',
        'estimate_date',
        'expiry_date',
        'status',
        'managed_by',   
        'managed_at',
        'statut_v',
        'validators',
        'validation_orther',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'estimate_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Get the user that owns the estimate.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the items for the estimate.
     */
    public function items()
    {
        return $this->hasMany(EstimatesAdd::class);
    }

    /**
     * Get the details for the estimate.
     */
    public function details()
    {
        return $this->hasMany(EstimateDetail::class);
    }
    public function actions()
    {
        return $this->hasMany(EstimateAction::class, 'estimate_number', 'estimate_number');
    }
}
