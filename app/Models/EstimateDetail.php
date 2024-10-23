<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateDetail extends Model
{
    protected $table = 'estimate_details';
    protected $fillable = ['estimate_id', 'detail_type', 'detail_value'];

    public function estimate()
    {
        return $this->belongsTo(Estimates::class);
    }
}
