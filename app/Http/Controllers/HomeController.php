<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use PDF;
use App\Models\User;
use App\Models\Estimates; // Add this import
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // main dashboard
    public function index()
    {
        $userId = Auth::id();

        // Fetch the count of estimates for the logged-in user
        $totalEstimates = Estimates::where('user_id', $userId)->count();
        $validatedEstimates = Estimates::where('user_id', $userId)->where('status', 'Validée')->count();
        $rejectedEstimates = Estimates::where('user_id', $userId)->where('status', 'Refusée')->count();
        $pendingEstimates = Estimates::where('user_id', $userId)->where('status', 'En cours')->count();
        $livrerEstimates =Estimates::where('user_id', $userId)->where('status', 'livrer')->count();
        // Fetch recent estimates
        $recentEstimates = Estimates::where('user_id', $userId)->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard.dashboard', compact(
            'totalEstimates', 
            'validatedEstimates', 
            'rejectedEstimates', 
            'pendingEstimates', 
            'livrerEstimates',
            'recentEstimates'
        ));
    }

    // employee dashboard
    public function emDashboard()
    {
        $dt = Carbon::now();
        $todayDate = $dt->toDayDateTimeString();
        return view('dashboard.emdashboard', compact('todayDate'));
    }

    public function generatePDF(Request $request)
    {
        // selecting PDF view
        $pdf = PDF::loadView('payroll.salaryview');
        // download pdf file
        return $pdf->download('pdfview.pdf');
    }
}
