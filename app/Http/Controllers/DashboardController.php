<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estimates;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


use App\Models\Estimate;
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Statistiques globales
        $totalEstimates = Estimate::count();
        $validatedEstimates = Estimate::where('status', 'Validée')->count();
        $rejectedEstimates = Estimate::where('status', 'Refusée')->count();
        $livrerEstimates = Estimate::where('status', 'livrer')->count();
        $pendingEstimates = Estimate::where('status', 'En cours')->count();

        // Dernières demandes
        $recentEstimates = Estimate::orderBy('created_at', 'desc')->limit(5)->get();

        // Demandes spécifiques pour l'acheteur
        $estimates = null;
        if ($user->role_name == 'Acheteur') {
            $estimates = Estimate::whereIn('status', ['livrer', 'Refusée', 'Commander', 'Reception'])
                                 ->paginate(10);
        }
// Récupération des données nécessaires
$estimates = Estimate::all(); // Par exemple

        return view('dashboard',compact(
            'user', 'totalEstimates', 'estimates' ,'validatedEstimates', 'rejectedEstimates', 'livrerEstimates', 'pendingEstimates', 'recentEstimates', 'estimates'
        ));
    }


}
