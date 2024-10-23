<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estimates;
use Illuminate\Support\Facades\Auth;

class DemanderecuController extends Controller
{
    public function index()
    {
     // Fetch all estimates with status 'validation partielle', 'commander', or 'livrer'
     $statuses = ['Validée', 'Commandé', 'Livré','Reçu','Refusée','En cours de traitement'];
     $estimates = Estimates::whereIn('status', $statuses)->get();
    
        // Pass the estimates to the view
        return view('acheteur.demanderecu', compact('estimates'));
    }
}
