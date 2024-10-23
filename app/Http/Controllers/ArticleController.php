<?php
namespace App\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\EstimatesAdd;
use App\Models\Estimates;
use App\Models\EstimateDetail;
use App\Models\EstimateAction;
use App\Models\AcheteurActions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Mail\EstimateStatusUpdate;
use Illuminate\Support\Facades\Mail;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::paginate(3);
        
        // Récupérer les quantités demandées pour chaque article en fonction du statut "Validée"
        $articlesWithRequests = $articles->map(function($article) {
            $article->demand = EstimatesAdd::where('item', $article->name)
            ->whereIn('status', ['Validée', 'commandé', 'recu', 'En cours'])
            ->sum('qty');
            $article->ad = $article->stock - $article->demand;
            $article->save();
            
            return $article;
        });
    
        return view('articles.index', compact('articles', 'articlesWithRequests'));
    }
    
    public function deliver(Request $request, $estimateNumber)
    {
        try {
            $estimate = Estimates::where('estimate_number', $estimateNumber)->firstOrFail();
            $estimatesAdd = EstimatesAdd::where('estimate_number', $estimateNumber)->get();
        
            foreach ($estimatesAdd as $item) {
                $article = Article::where('name', $item->item)->first();
        
                if (!$article || $article->stock < $item->qty) {
                    return response()->json(['error' => 'Not enough stock to deliver the estimate.'], 400);
                }
        
                $article->stock -= $item->qty;
                $article->demand -= $item->qty;
                $article->save();
            }
        
            $estimate->status = 'Livré';
            $estimate->save();
        
            foreach ($estimatesAdd as $estimateAdd) {
                $estimateAdd->status = 'Livré';
                $estimateAdd->save();
            }
    
            // Log the action with acheteur_id
            $this->logAcheteurAction($estimateNumber, 'Livré', Auth::id());
    
            // Send email notification to the estimate creator
            $this->sendStatusUpdateEmail($estimate);
        
            return response()->json(['success' => 'Articles livrés avec succès.']);
        } catch (\Exception $e) {
            Log::error('Error delivering articles: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during delivery.'], 500);
        }
    }
    
    
    public function commande(Request $request, $estimate_number)
    {
        $estimate = Estimates::where('estimate_number', $estimate_number)->firstOrFail();
        $estimatesAdd = EstimatesAdd::where('estimate_number', $estimate_number)->get();
        
        // Logic to set the estimate and its adds to "Commandé" status
        $estimate->status = 'Commandé';
        $estimate->save();
        
        foreach ($estimatesAdd as $estimateAdd) {
            $estimateAdd->status = 'Commandé';
            $estimateAdd->save();
        }

        // Log the action with acheteur_id
        $this->logAcheteurAction($estimate_number, 'Commandé', Auth::id());

        // Send email notification to the estimate creator
        $this->sendStatusUpdateEmail($estimate);
        
        return response()->json(['success' => 'Les articles ont été commandés avec succès.']);
    }
    
    public function receive(Request $request, $estimate_number)
    {
        $estimate = Estimates::where('estimate_number', $estimate_number)->firstOrFail();
        $estimateAdd = EstimatesAdd::where('estimate_number', $estimate_number)->get();
        
        foreach ($estimateAdd as $item) {
            $article = Article::where('name', $item->item)->first();
            $article->stock += $item->qty;
            $article->save();
        }
        
        $estimate->status = 'Reçu';
        $estimate->save();
    
        foreach ($estimateAdd as $estimateAdd) {
            $estimateAdd->status = 'Reçu';
            $estimateAdd->save();
        }

        // Log the action with acheteur_id
        $this->logAcheteurAction($estimate_number, 'Reçu', Auth::id());

        // Send email notification to the estimate creator
        $this->sendStatusUpdateEmail($estimate);
    
        return response()->json(['success' => 'Les articles ont été réceptionnés avec succès.']);
    }
    
    public function annuler($estimate_number)
    {
        $estimate = Estimates::where('estimate_number', $estimate_number)->firstOrFail();
        $estimate->status = 'Refusé';
        $estimate->save();

        $estimateAdds = EstimatesAdd::where('estimate_number', $estimate_number)->get();
        foreach ($estimateAdds as $estimateAdd) {
            $estimateAdd->status = 'Refusé';
            $estimateAdd->save();
        }

        // Log the action with acheteur_id
        $this->logAcheteurAction($estimate_number, 'Refusé', Auth::id());

        // Send email notification to the estimate creator
        $this->sendStatusUpdateEmail($estimate);

        return response()->json(['success' => 'La demande et les articles associés ont été annulés avec succès.']);
    }

    public function receiveItem(Request $request, $estimate_number, $item_id)
    {
        $item = EstimatesAdd::find($item_id);
        if ($item) {
            $article = Article::where('name', $item->item)->first();
            if ($article) {
                $article->stock += $item->qty;
                $article->save();
            }
            $item->status = 'Reçu';
            $item->save();

            session()->put('hide_top_buttons_' . $estimate_number, true);

            $allReceived = EstimatesAdd::where('estimate_number', $estimate_number)->where('status', '!=', 'Reçu')->count() == 0;
            $estimate = Estimates::where('estimate_number', $estimate_number)->first();

            if ($allReceived) {
                $estimate->status = 'Reçu';
                $this->logAcheteurAction($estimate_number, 'Reçu', Auth::id());
            } else {
                $estimate->status = 'En cours de traitement';
            }

            $estimate->save();

            // Send email notification to the estimate creator
            $this->sendStatusUpdateEmail($estimate);

            return response()->json(['success' => 'Item received successfully.']);
        }

        return response()->json(['error' => 'Item not found.'], 404);
    }

    public function commandeItem(Request $request, $estimate_number, $item_id)
    {
        $item = EstimatesAdd::find($item_id);
        if ($item) {
            $item->status = 'Commandé';
            $item->save();

            session()->put('hide_top_buttons_' . $estimate_number, true);

            $allCommande = EstimatesAdd::where('estimate_number', $estimate_number)->where('status', '!=', 'Commandé')->count() == 0;
            $estimate = Estimates::where('estimate_number', $estimate_number)->first();

            if ($allCommande) {
                $estimate->status = 'Commandé';
                $this->logAcheteurAction($estimate_number, 'Commandé', Auth::id());
            } else {
                $estimate->status = 'En cours de traitement';
            }

            $estimate->save();

            // Send email notification to the estimate creator
            $this->sendStatusUpdateEmail($estimate);

            return response()->json(['success' => 'Item ordered successfully.']);
        }

        return response()->json(['error' => 'Item not found.'], 404);
    }

    public function deliverItem(Request $request, $estimate_number, $item_id)
    {
        $item = EstimatesAdd::find($item_id);
        if ($item) {
            $article = Article::where('name', $item->item)->first();
            if ($article && $article->stock >= $item->qty) {
                $article->stock -= $item->qty;
                $article->save();
            } else {
                return response()->json(['error' => 'Not enough stock to deliver the item.'], 400);
            }
            $item->status = 'Livré';
            $item->save();

            session()->put('hide_top_buttons_' . $estimate_number, true);

            $allDelivered = EstimatesAdd::where('estimate_number', $estimate_number)->where('status', '!=', 'Livré')->count() == 0;
            $estimate = Estimates::where('estimate_number', $estimate_number)->first();

            if ($allDelivered) {
                $estimate->status = 'Livré';
                $this->logAcheteurAction($estimate_number, 'Livré', Auth::id());
            } else {
                $estimate->status = 'En cours de traitement';
            }

            $estimate->save();

            // Send email notification to the estimate creator
            $this->sendStatusUpdateEmail($estimate);

            return response()->json(['success' => 'Item delivered successfully.']);
        }

        return response()->json(['error' => 'Item not found.'], 404);
    }

    public function annulerItem(Request $request, $estimate_number, $item_id)
    {
        $item = EstimatesAdd::find($item_id);
        if ($item) {
            $item->status = 'Refusé';
            $item->save();

            session()->put('hide_top_buttons_' . $estimate_number, true);

            $allRefused = EstimatesAdd::where('estimate_number', $estimate_number)->where('status', '!=', 'Refusé')->count() == 0;
            $estimate = Estimates::where('estimate_number', $estimate_number)->first();

            if ($allRefused) {
                $estimate->status = 'Refusé';
                $this->logAcheteurAction($estimate_number, 'Refusé', Auth::id());
            } else {
                $estimate->status = 'En cours de traitement';
            }

            $estimate->save();

            // Send email notification to the estimate creator
            $this->sendStatusUpdateEmail($estimate);

            // Log the action with acheteur_id
            $this->logAcheteurAction($estimate_number, 'Refusé', Auth::id());

            return response()->json(['success' => 'Item refused successfully.']);
        }

        return response()->json(['error' => 'Item not found.'], 404);
    }

    // Method to send email notification about status update
    private function sendStatusUpdateEmail($estimate)
    {
        $creator = User::find($estimate->user_id);
        if ($creator) {
            try {
                Mail::to($creator->email)->send(new EstimateStatusUpdate($creator, $estimate));
                Log::info("Email sent to estimate creator: {$creator->email} for estimate: {$estimate->estimate_number}");
            } catch (\Exception $e) {
                Log::error("Failed to send email to {$creator->email}: " . $e->getMessage());
            }
        } else {
            Log::warning("Creator user with ID {$estimate->user_id} not found for estimate: {$estimate->estimate_number}");
        }
    }

    // Log actions for acheteurs
    private function logAcheteurAction($estimateNumber, $action, $acheteurId)
    {
        AcheteurActions::create([
            'estimate_number' => $estimateNumber,
            'acheteur_id' => $acheteurId,
            'action' => $action,
        ]);
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer',
            'stockmin' => 'required|integer',
        ]);

        Article::create($request->all());

        return redirect()->route('articles.index')->with('success', 'Article ajouté avec succès.');
    }
    
    public function edit($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'stockmin' => 'required|integer|min:0',
        ]);

        $article = Article::findOrFail($id);
        $article->update($request->all());

        Toastr::success('Article mis à jour avec succès!', 'Succès');
        return redirect()->route('articles.index');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Article supprimé avec succès');
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return view('admin.articles.show', compact('article'));
    }
}
