<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    // public function decrementStock($quantity)
    // {
    //     $this->stock -= $quantity;
    //     $this->save();
    // }
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'stockmin',
        'demand',
        'ad'
        
    ];
}
