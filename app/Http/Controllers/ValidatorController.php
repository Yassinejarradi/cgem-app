<?php

// app/Http/Controllers/ValidatorController.php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Estimates;
use App\Models\Validator;

use App\Models\UserValidatorAssignment;
use App\Models\User;

class ValidatorController extends Controller
{
    

    
    
    public function showRequests()
    {
        // Get the logged-in user
        $user = Auth::user();
    
        // Get the validator information from the validators table using the logged-in user's ID
        $validator = Validator::where('user_id', $user->id)->first();
    
        if ($validator) {
            // Fetch the estimates assigned to the logged-in validator with visibility set to true
            $estimates = Estimates::whereRaw("JSON_CONTAINS(validators, '\"{$validator->user_id}\"')")
                ->whereRaw("JSON_CONTAINS(visibility, 'true', '$.\"{$validator->user_id}\"')")
                ->get();
        } else {
            // If no validator found, return an empty collection
            $estimates = collect();
        }
    
        return view('validator.requests', compact('estimates'));
    }
    
    
}
