<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Expense;
use App\Models\Article;

use App\Models\UserValidatorAssignment;

use App\Models\Estimates;
use App\Models\EstimatesAdd;
use App\Models\EstimateDetail;
use App\Models\EstimateAction;
use App\Models\Validator;
use App\Models\Role;
use App\Models\AcheteurActions;



use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\EstimateCreated;
use App\Mail\EstimateValidated;
use App\Mail\EstimateStatusUpdate;



class SalesController extends Controller
{
    /** page estimates */


    public function estimatesIndex()
    {
        // Fetch only the estimates that belong to the logged-in user
        $userId = Auth::id(); // Get the ID of the currently logged-in user
        
        // Use Eloquent ORM to fetch estimates belonging to the user
        $estimatesQuery = Estimates::where('user_id', $userId);
    
        // Paginate the results
        $estimates = $estimatesQuery->paginate(3); // You can adjust the pagination size as needed
    
        // Optionally, you can also join with estimates_adds table if necessary
        $estimatesJoin = $estimatesQuery
            ->join('estimates_adds', 'estimates.estimate_number', '=', 'estimates_adds.estimate_number')
            ->select('estimates.*', 'estimates_adds.*')
            ->paginate(3); // Paginate the joined results
    
        return view('sales.estimates', compact('estimates', 'estimatesJoin'));
        
}

    /** page create estimates */
    public function createEstimateIndex()
    {
        $articles = Article::all();
    
        return view('sales.createestimate', compact('articles'));
    }
    

    /** page edit estimates */
    public function editEstimateIndex($estimate_number)
    {
        $estimates          = DB::table('estimates') ->where('estimate_number',$estimate_number)->first();
        $estimatesJoin = DB::table('estimates')
            ->join('estimates_adds', 'estimates.estimate_number', '=', 'estimates_adds.estimate_number')
            ->select('estimates.*', 'estimates_adds.*')
            ->where('estimates_adds.estimate_number',$estimate_number)
            ->get();
        return view('sales.editestimate',compact('estimates','estimatesJoin'));
    }
    public function createEstimateForUser(Request $request)
{
    // Validation des données de la requête
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'type_demande' => 'required|string|in:fourniture,achat',
        'item' => 'required|array',
        'item.*' => 'required|string',
        'description' => 'required|array',
        'description.*' => 'required|string',
        'qty' => 'required|array',
        'qty.*' => 'required|integer',
        'motif' => 'required|array',
        'motif.*' => 'required|string',
    ]);

    // Vérification de stock pour les demandes de fourniture
    if ($request->type_demande === 'fourniture') {
        $insufficientStockItems = [];

        foreach ($request->item as $key => $item) {
            $article = Article::where('name', $item)->first();
            if ($article && $article->stock < $request->qty[$key]) {
                $insufficientStockItems[] = [
                    'name' => $item,
                    'requested_qty' => $request->qty[$key],
                    'available_stock' => $article->stock
                ];
            }
        }

        if (!empty($insufficientStockItems)) {
            // Stock insuffisant
            session()->flash('error_message', [
                'message' => 'Stock insuffisant pour article(s)',
                'insufficient_stock_items' => $insufficientStockItems
            ]);
            return redirect()->back()->withErrors(['Stock insuffisant pour certains articles.']);
        }
    }

    DB::beginTransaction();
    try {
        // Création de l'estimation
        $estimate = new Estimates;
        $estimate->type_demande = $request->type_demande;
        $estimate->estimate_date = now()->locale('fr_FR');
        $estimate->expiry_date = $request->expiry_date;
        $estimate->user_id = $request->user_id; // L'utilisateur sélectionné
        $estimate->created_by_acheteur = Auth::id(); // L'acheteur qui a créé la demande
        $estimate->status = 'En cours'; // Définir le statut initial

        // Définir les validateurs et autres champs en fonction du type de demande
        if ($request->type_demande === 'achat') {
            $userValidatorIds = DB::table('user_validator_assignments')
                ->where('user_id', $request->user_id)
                ->pluck('validator_id')
                ->toArray();

            $userIds = DB::table('validators')
                ->whereIn('id', $userValidatorIds)
                ->pluck('user_id')
                ->toArray();

            $visibility = [];
            foreach ($userIds as $index => $userId) {
                $visibility[$userId] = ($index == 0);
            }

            $estimate->validators = json_encode($userIds);
            $estimate->visibility = json_encode($visibility);
            $estimate->validation_orther = 0;
            $estimate->current_validator = $userIds[0] ?? null;
        } else {
            $estimate->validators = json_encode([]);
            $estimate->visibility = json_encode([]);
            $estimate->validation_orther = null;
            $estimate->current_validator = null;
            $estimate->status = 'Validée';
        }

        $estimate->save();

        $prefix = $estimate->type_demande === 'fourniture' ? 'FOUR-' : 'ACH-';
        $estimate_number = $prefix . str_pad($estimate->id, 8, '0', STR_PAD_LEFT);
        $estimate->estimate_number = $estimate_number;
        $estimate->save();

        if (!$estimate_number) {
            throw new \Exception('Échec de la génération du numéro de l\'estimation.');
        }

        foreach ($request->item as $key => $item) {
            $estimatesAdd = [
                'item' => $item,
                'estimate_number' => $estimate_number,
                'description' => $request->description[$key],
                'qty' => $request->qty[$key],
                'motif' => $request->motif[$key],
            ];

            EstimatesAdd::create($estimatesAdd);

            $article = Article::where('name', $item)->first();
            if ($article) {
                $article->demand += $request->qty[$key];
                $article->save();
            }
        }

        if ($request->type_demande === 'achat') {
            $details = [];
            if (isset($request->piece_joint)) {
                foreach ($request->piece_joint as $piece) {
                    $details[] = [
                        'estimate_id' => $estimate->id,
                        'detail_type' => 'Pieces to Request',
                        'detail_value' => $piece
                    ];
                }
            }

            if (isset($request->element_exiges_lors_de_la_reception)) {
                foreach ($request->element_exiges_lors_de_la_reception as $element) {
                    $details[] = [
                        'estimate_id' => $estimate->id,
                        'detail_type' => 'Elements Required at Reception',
                        'detail_value' => $element
                    ];
                }
            }

            if ($request->has('participation_a_la_consultation_selection')) {
                $details[] = [
                    'estimate_id' => $estimate->id,
                    'detail_type' => 'Participation in Consultation/Selection',
                    'detail_value' => $request->participation_a_la_consultation_selection
                ];
            }

            if (isset($request->achat_demande)) {
                foreach ($request->achat_demande as $status) {
                    $details[] = [
                        'estimate_id' => $estimate->id,
                        'detail_type' => 'Budgetary Status of Purchase',
                        'detail_value' => $status
                    ];
                }
            }

            if ($estimate->current_validator) {
                $currentValidator = User::find($estimate->current_validator);
                if ($currentValidator) {
                    Mail::to($currentValidator->email)->send(new EstimateCreated($estimate, $currentValidator));
                }
            }

            EstimateDetail::insert($details);
        }

        DB::commit();
        Toastr::success('Ajout de la demande réussi', 'Success');
        return redirect()->route('demanderecu');
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Erreur lors de l\'ajout de la demande: ' . $e->getMessage());
        Toastr::error('Ajout de la demande échoué: ' . $e->getMessage(), 'Error');
        return redirect()->back();
    }
}

    
    public function showDemandesRecues()
{
    $estimates = Estimates::where('status', 'Validée')->get();
    $users = User::all(); // Fetch all users

    return view('acheteur.demanderecu', compact('estimates', 'users'));
}
    public function showCreateEstimateForUser()
 {
     $users = User::all(); // Fetch all users
     $articles = Article::all(); // Fetch all articles
     return view('acheteur.create_estimate_for_user', compact('users','articles'));
 }
 
    /** view page estimate */
    public function viewEstimateIndex($estimate_number)
{
    // Get the current authenticated user
    $user = Auth::user();

    // Fetch the estimate
    $estimate = Estimates::where('estimate_number', $estimate_number)->first();

    if (!$estimate) {
        return redirect()->back()->with('error', 'Estimate not found.');
    }

    // Fetch the acheteur who created the estimate, if applicable
    $acheteur = null;
    if ($estimate->created_by_acheteur) {
        $acheteur = User::find($estimate->created_by_acheteur);
    }

    // Check if the current user is the creator of the estimate
    $isCreator = $estimate->user_id == $user->id;

    // Check if the current user is a validator for this estimate
    $validatorIds = json_decode($estimate->validators, true);
    if (!is_array($validatorIds)) {
        $validatorIds = [];
    }
    $isValidator = in_array($user->id, $validatorIds);

    // Check if the user has the role "Acheteur"
    $isAcheteur = $user->role_name === 'Acheteur';

    // Check if the user is the manager of this estimate
    $isManager = $estimate->managed_by == $user->id;

    // If the user is neither the creator, a validator, an Acheteur, nor the manager, deny access
    if (!$isCreator && !$isValidator && !$isAcheteur && !$isManager) {
        return redirect()->back()->with('error', 'You are not authorized to view this estimate.');
    }

    // Fetch the manager of the estimate, if applicable
    $manager = null;
    if ($estimate->managed_by) {
        $manager = User::find($estimate->managed_by);
    }

    // Fetch related items
    $estimatesJoin = EstimatesAdd::where('estimate_number', $estimate_number)->get();

    // Fetch the actions for this estimate
    $estimateActions = EstimateAction::where('estimate_number', $estimate_number)
                                      ->with('user')
                                      ->get();

    // Fetch the details for this estimate and group by detail type
    $estimateDetails = EstimateDetail::where('estimate_id', $estimate->id)->get()->groupBy('detail_type');

    // Check if the logged-in validator has validated this estimate
    $hasValidated = $estimateActions->where('action', 'validated')->where('user_id', $user->id)->isNotEmpty();

    // Fetch the actions performed by the acheteur
    $acheteurActions = AcheteurActions::where('estimate_number', $estimate->estimate_number)->with('acheteur')->get();

    // Pass necessary data to the view
    return view('Sales.estimateview', compact('estimate', 'estimatesJoin', 'isValidator', 'estimateActions', 'estimateDetails', 'isAcheteur', 'hasValidated', 'acheteurActions', 'manager', 'isManager'));
}

    
    



public function updateManageBy(Request $request, $estimate_number)
{
    // Assuming you're using authentication
    $userId = auth()->user()->id;

    // Find the estimate
    $estimate = Estimates::where('estimate_number', $estimate_number)->first();

    if ($estimate) {
        if ($request->action === 'set') {
            // Update the manage_by column with the current user's ID
            $estimate->managed_by = $userId;
        } else if ($request->action === 'unset') {
            // Clear the manage_by column
            $estimate->managed_by = null;
        }
        $estimate->save();

        return response()->json(['success' => 'manage_by updated successfully']);
    } else {
        return response()->json(['error' => 'Estimate not found'], 404);
    }
}
























    
    
    public function createEstimateSaveRecord(Request $request)
    {
        $request->validate([
            'type_demande' => 'required|string|in:fourniture,achat',
            'item' => 'required|array',
            'item.*' => 'required|string',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'qty' => 'required|array',
            'qty.*' => 'required|integer',
            'motif' => 'required|array',
            'motif.*' => 'required|string',
        ]);
    
        if ($request->type_demande === 'fourniture') {
            // Vérification du stock
            $insufficientStockItems = [];
    
            foreach ($request->item as $key => $items) {
                $article = Article::where('name', $items)->first();
                if ($article && $article->stock < $request->qty[$key]) {
                    $insufficientStockItems[] = [
                        'name' => $items,
                        'requested_qty' => $request->qty[$key],
                        'available_stock' => $article->stock
                    ];
                }
            }
    
            if (count($insufficientStockItems) > 0) {
                // Ajout du message d'erreur dans la session
                session()->flash('error_message', [
                    'message' => 'Stock insuffisant pour article(s)',
                    'insufficient_stock_items' => $insufficientStockItems
                ]);
    
                // Rediriger ou retourner à la vue où afficher le message
                return redirect()->back()->withErrors(['Stock insuffisant pour certains articles.']);
            }
        }
    
        DB::beginTransaction();
        try {
            $estimate = new Estimates;
            $estimate->type_demande = $request->type_demande;
            $estimate->estimate_date = \Carbon\Carbon::now()->locale('fr_FR'); // Date actuelle en français
            $estimate->expiry_date = $request->expiry_date;
            $estimate->user_id = Auth::id(); // Enregistrer avec l'identifiant de l'utilisateur connecté
            $estimate->status = 'En cours'; // Définir le statut sur 'En cours'
    
            if ($request->type_demande === 'achat') {
                // Récupération des validateurs assignés à l'utilisateur
                $userValidatorIds = DB::table('user_validator_assignments')
                    ->where('user_id', Auth::id())
                    ->pluck('validator_id')
                    ->toArray();
    
                // Récupération des identifiants des utilisateurs validateurs
                $userIds = DB::table('validators')
                    ->whereIn('id', $userValidatorIds)
                    ->pluck('user_id')
                    ->toArray();
    
                // Initialisation de la visibilité des validateurs
                $visibility = [];
                foreach ($userIds as $index => $userId) {
                    $visibility[$userId] = ($index == 0);
                }
    
                // Enregistrer les validateurs et la visibilité en JSON
                $estimate->validators = json_encode($userIds);
                $estimate->visibility = json_encode($visibility);
                $estimate->validation_orther = 0; // Initialiser l'ordre de validation à 0
                $estimate->current_validator = $userIds[0] ?? null; // Définir le premier validateur comme validateur actuel
            } else {
                // Pour les demandes de fourniture, pas de validateurs
                $estimate->validators = json_encode([]);
                $estimate->visibility = json_encode([]);
                $estimate->validation_orther = null;
                $estimate->current_validator = null;
                $estimate->status = 'Validée'; // Définir directement le statut sur 'Validée'
            }
    
            // Sauvegarde de l'estimation
            $estimate->save();
    
            // Récupération du numéro de l'estimation généré et mise à jour
            // Choix du préfixe basé sur le type de demande
            $prefix = $estimate->type_demande === 'fourniture' ? 'FOUR-' : 'ACH-';
            $estimate_number = $prefix . str_pad($estimate->id, 8, '0', STR_PAD_LEFT);
            $estimate->estimate_number = $estimate_number;
            $estimate->save();

    
            // Vérification si le numéro de l'estimation est récupéré correctement
            if (!$estimate_number) {
                throw new \Exception('Échec de la génération du numéro de l\'estimation.');
            }
    
            // Enregistrement des articles de l'estimation
            foreach ($request->item as $key => $items) {
                $estimatesAdd = [
                    'item' => $items,
                    'estimate_number' => $estimate_number,
                    'description' => $request->description[$key],
                    'qty' => $request->qty[$key],
                    'motif' => $request->motif[$key],
                ];
    
                EstimatesAdd::create($estimatesAdd);
    
                // Update the demande_en_cours for the related article
                $article = Article::where('name', $items)->first();
                if ($article) {
                    $article->demand += $request->qty[$key];
                    $article->save();
                }
            }
    
            if ($request->type_demande === 'achat') {
                $details = [];
                // Enregistrement des détails de l'estimation (pièces jointes, éléments exigés, etc.)
                if (isset($request->piece_joint)) {
                    foreach ($request->piece_joint as $piece) {
                        $details[] = [
                            'estimate_id' => $estimate->id,
                            'detail_type' => 'Pieces to Request',
                            'detail_value' => $piece
                        ];
                    }
                }
    
                if (isset($request->element_exiges_lors_de_la_reception)) {
                    foreach ($request->element_exiges_lors_de_la_reception as $element) {
                        $details[] = [
                            'estimate_id' => $estimate->id,
                            'detail_type' => 'Elements Required at Reception',
                            'detail_value' => $element
                        ];
                    }
                }
    
                if ($request->has('participation_a_la_consultation_selection')) {
                    $details[] = [
                        'estimate_id' => $estimate->id,
                        'detail_type' => 'Participation in Consultation/Selection',
                        'detail_value' => $request->participation_a_la_consultation_selection
                    ];
                }
    
                if (isset($request->achat_demande)) {
                    foreach ($request->achat_demande as $status) {
                        $details[] = [
                            'estimate_id' => $estimate->id,
                            'detail_type' => 'Budgetary Status of Purchase',
                            'detail_value' => $status
                        ];
                    }
                }
    
                // Envoi d'un e-mail au validateur actuel
                if ($estimate->current_validator) {
                    $currentValidator = User::find($estimate->current_validator);
                    if ($currentValidator) {
                        Mail::to($currentValidator->email)->send(new EstimateCreated($estimate, $currentValidator));
                    }
                }
    
                // Insertion des détails de l'estimation dans la base de données
                EstimateDetail::insert($details);
            }
    
            // Validation de la transaction
            DB::commit();
            Toastr::success('Ajout de la demande réussi', 'Success');
            return redirect()->route('form/estimates/page');
        } catch (\Exception $e) {
            // Annulation de la transaction en cas d'erreur
            DB::rollback();
            Toastr::error('Ajout de la demande échoué: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }
    
    










public function index(Request $request)
{
    $userId = Auth::id(); // Get the ID of the currently logged-in user
    $query = Estimates::where('user_id', $userId); // Start the query with a filter for the logged-in user

    // Filter by type of demand
    if ($request->filled('type_demande')) {
        $query->where('type_demande', $request->type_demande);
    }

    // Filter by date range
    if ($request->filled('date_from')) {
        $query->whereDate('estimate_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('expiry_date', '<=', $request->date_to);
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Retrieve the results with pagination
    $estimates = $query->paginate(3);

    return view('sales.estimates', compact('estimates'));
}


/**
     * Validate an estimate by a validator.
     *
     * @param int $estimate
     * @return \Illuminate\Http\Response
     */





     public function validateEstimateByNumber(Request $request, $estimateNumber)
{
    // Obtenir l'utilisateur actuellement authentifié
    $user = Auth::user();
    Log::info("User attempting validation: {$user->id}");

    // Récupérer l'estimation par estimate_number
    $estimate = Estimates::where('estimate_number', $estimateNumber)->first();

    if (!$estimate) {
        Log::error("Estimate not found: {$estimateNumber}");
        return response()->json(['warning' => 'Demande introuvable'], 404);
    }

    Log::info("Estimate found: {$estimate->id}, current validator: {$estimate->current_validator}");

    // Vérifier si l'utilisateur actuel est le current_validator pour cette estimation
    if ($estimate->current_validator != $user->id) {
        Log::warning("Unauthorized validator attempt by user: {$user->id}");
        return response()->json(['warning' => 'Vous n\'êtes pas autorisé à valider cette demande'], 403);
    }

    // Incrémenter le compteur validation_orther
    $estimate->validation_orther += 1;

    // Récupérer la liste des validateurs
    $validatorIds = json_decode($estimate->validators, true);
    Log::info("Validator IDs: " . implode(',', $validatorIds));

    // Obtenir la visibilité
    $visibility = json_decode($estimate->visibility, true);

    // Déterminer le prochain validateur
    $currentValidatorIndex = array_search($user->id, $validatorIds);
    $nextValidatorIndex = $currentValidatorIndex + 1;

    if ($nextValidatorIndex < count($validatorIds)) {
        // Définir le prochain validateur
        $nextValidatorId = $validatorIds[$nextValidatorIndex];
        $estimate->current_validator = $nextValidatorId;

        // Définir la visibilité pour le prochain validateur à true
        $visibility[$nextValidatorId] = true;

        $nextValidator = User::find($nextValidatorId);
        if ($nextValidator) {
            try {
                Mail::to($nextValidator->email)->send(new EstimateValidated($estimate, $nextValidator));
                Log::info("Email sent to next validator: {$nextValidator->email} for estimate: {$estimate->estimate_number}");
            } catch (\Exception $e) {
                Log::error("Failed to send email to {$nextValidator->email}: " . $e->getMessage());
            }
        } else {
            Log::warning("Next validator with ID $nextValidatorId not found for estimate: {$estimate->estimate_number}");
        }

        // Définir statut_v à 'En cours' pour le prochain validateur
        $estimate->statut_v = 'Validée partiellement';
    } else {
        // Tous les validateurs ont validé, définir le statut à 'Validée'
        $estimate->status = 'Validée';
        $estimate->statut_v = 'Validée';
        $estimate->current_validator = null; // Plus de validateurs

        // Mettre à jour le statut de chaque item dans estimate_add
        EstimatesAdd::where('estimate_number', $estimateNumber)->update(['status' => 'Validée']);
    }

    // Mettre à jour la visibilité
    $estimate->visibility = json_encode($visibility);

    // Enregistrer les modifications
    $estimate->save();

    // Enregistrer l'action
    $validator = Validator::where('user_id', $user->id)->first();
    EstimateAction::create([
        'estimate_number' => $estimateNumber,
        'validator_id' => $validator->id,
        'user_id' => $user->id,
        'action' => 'validated',
        'created_at' => now()
    ]);

    // Send email to the creator of the estimate when the status is 'Validée'
    $creator = User::find($estimate->user_id);
    if ($creator) {
        Log::info("Attempting to send email to estimate creator: {$creator->email} for estimate: {$estimate->estimate_number}");
        try {
            Mail::to($creator->email)->send(new EstimateStatusUpdate($creator, $estimate));
            Log::info("Email sent to estimate creator: {$creator->email} for estimate: {$estimate->estimate_number}");
        } catch (\Exception $e) {
            Log::error("Failed to send email to {$creator->email}: " . $e->getMessage());
        }
    } else {
        Log::warning("Creator user with ID {$estimate->user_id} not found for estimate: {$estimate->estimate_number}");
    }

    return response()->json(['success' => 'Demande validée', 'status' => $estimate->status]);
}

     
     
public function refuseEstimate(Request $request, $estimateNumber)
{
    // Get the authenticated user
    $user = Auth::user();
    Log::info("User attempting to refuse estimate: {$user->id}");

    // Find the estimate by estimate_number
    $estimate = Estimates::where('estimate_number', $estimateNumber)->first();

    if (!$estimate) {
        Log::error("Estimate not found: {$estimateNumber}");
        return response()->json(['warning' => 'Demande introuvable'], 404);
    }

    Log::info("Estimate found: {$estimate->id}, current validator: {$estimate->current_validator}");

    // Check if the current user is the current validator for this estimate
    if ($estimate->current_validator != $user->id) {
        Log::warning("Unauthorized refusal attempt by user: {$user->id}");
        return response()->json(['warning' => 'Vous n\'êtes pas autorisé à refuser cette demande'], 403);
    }

    // Set the status to 'Refusée'
    $estimate->status = 'Refusée';
    $estimate->statut_v = 'Refusée';
    $estimate->current_validator = null;

    // Update the status of each item in estimate_add
    EstimatesAdd::where('estimate_number', $estimateNumber)->update(['status' => 'Refusée']);

    // Save the changes
    $estimate->save();

    // Log the action
    $validator = Validator::where('user_id', $user->id)->first();
    EstimateAction::create([
        'estimate_number' => $estimateNumber,
        'validator_id' => $validator->id,
        'user_id' => $user->id,
        'action' => 'refused',
        'created_at' => now()
    ]);

    // Send email to the creator of the estimate
    $creator = User::find($estimate->user_id);
    if ($creator) {
        Log::info("Attempting to send email to estimate creator: {$creator->email} for estimate: {$estimate->estimate_number}");
        try {
            Mail::to($creator->email)->send(new EstimateStatusUpdate($creator, $estimate));
            Log::info("Email sent to estimate creator: {$creator->email} for estimate: {$estimate->estimate_number}");
        } catch (\Exception $e) {
            Log::error("Failed to send email to {$creator->email}: " . $e->getMessage());
        }
    } else {
        Log::warning("Creator user with ID {$estimate->user_id} not found for estimate: {$estimate->estimate_number}");
    }

    return response()->json(['success' => 'Demande refusée', 'status' => $estimate->status]);
}

  

    /** update record estimate */
    public function updateEstimateRecord(Request $request)
{
    $request->validate([
        'type_demande' => 'required|string|in:fourniture,achat',
        'estimate_date' => 'required|date',
        'expiry_date' => 'required|date',
        'id' => 'required|integer|exists:estimates,id', // Make sure the estimate exists
    ]);

    DB::beginTransaction();
    try {
        $estimates = Estimates::findOrFail($request->id);
        $estimates->type_demande = $request->type_demande;
        $estimates->estimate_date = $request->estimate_date;
        $estimates->expiry_date = $request->expiry_date;
        
        $estimates->save();

        // Update related items
        foreach ($request->item as $key => $item) {
            $estimateAddId = $request->estimates_adds[$key];
            $estimatesAdd = EstimatesAdd::find($estimateAddId);
            if ($estimatesAdd) {
                $estimatesAdd->item = $item;
                $estimatesAdd->description = $request->description[$key];
                $estimatesAdd->qty = $request->qty[$key];
                $estimatesAdd->motif = $request->motif[$key];
                $estimatesAdd->save();
            }
        }

        // Handling additional options if 'achat' is selected
        if ($request->type_demande === 'achat') {
            $details = [];

            EstimateDetail::where('estimate_id', $estimates->id)->delete(); // Clear existing details

            $this->processDetails($details, $request, $estimates->id);

            // Save all new details
            EstimateDetail::insert($details);
        }

        DB::commit();
        Toastr::success('Mise à jour de la demande réussie', 'Success');
        return redirect()->route('form/estimates/page');
    } catch (\Exception $e) {
        DB::rollback();
        Toastr::error('Mise à jour de la demande échouée : ' . $e->getMessage(), 'Error');
        return redirect()->back();
    }
}
public function printEstimate($estimate_number)
{
    // Fetch the estimate
    $estimate = Estimates::where('estimate_number', $estimate_number)->first();

    if (!$estimate) {
        return redirect()->back()->with('error', 'Estimate not found.');
    }

    // Fetch related items
    $estimatesJoin = EstimatesAdd::where('estimate_number', $estimate_number)->get();

    // Fetch the actions for this estimate
    $estimateActions = EstimateAction::where('estimate_number', $estimate_number)
                                    ->with('user') // Ensure the related user is loaded
                                    ->get();

    // Fetch the actions performed by the acheteur
    $acheteurActions = AcheteurActions::where('estimate_number', $estimate_number)
                                      ->with('acheteur') // Ensure the related acheteur is loaded
                                      ->get();

    // Fetch the details for this estimate and group by detail type
    $estimateDetails = EstimateDetail::where('estimate_id', $estimate->id)->get()->groupBy('detail_type');

    return view('sales.estimate_print', compact('estimate', 'estimatesJoin', 'estimateActions', 'acheteurActions', 'estimateDetails'));
}

private function processDetails(&$details, $request, $estimateId)
{
    // Pieces to request
    if (isset($request->piece_joint)) {
        foreach ($request->piece_joint as $piece) {
            $details[] = [
                'estimate_id' => $estimateId,
                'detail_type' => 'Pieces to Request',
                'detail_value' => $piece
            ];
        }
    }

    // Elements required at reception
    if (isset($request->element_exiges_lors_de_la_reception)) {
        foreach ($request->element_exiges_lors_de_la_reception as $element) {
            $details[] = [
                'estimate_id' => $estimateId,
                'detail_type' => 'Elements Required at Reception',
                'detail_value' => $element
            ];
        }
    }

    // Participation in consultation/selection
    if ($request->has('participation_a_la_consultation_selection')) {
        $details[] = [
            'estimate_id' => $estimateId,
            'detail_type' => 'Participation in Consultation/Selection',
            'detail_value' => $request->participation_a_la_consultation_selection
        ];
    }

    // Budgetary status of purchase
    if (isset($request->achat_demande)) {
        foreach ($request->achat_demande as $status) {
            $details[] = [
                'estimate_id' => $estimateId,
                'detail_type' => 'Budgetary Status of Purchase',
                'detail_value' => $status
            ];
        }
    }
}


    /** delete record estimate add */
    public function EstimateAddDeleteRecord(Request $request)
    {
        DB::beginTransaction();
        try {

            EstimatesAdd::destroy($request->id);

            DB::commit();
            Toastr::success('Demande supprimé :)','Success');
            return redirect()->back();
            
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Estimates deleted fail :)','Error');
            return redirect()->back();
        }
    }
    
    /** delete record estimate */
    public function EstimateDeleteRecord(Request $request)
    {
        DB::beginTransaction();
        try {

            /** delete record table estimates_adds */
            $estimate_number = DB::table('estimates_adds')->where('estimate_number',$request->estimate_number)->get();
            foreach ($estimate_number as $key => $id_estimate_number) {
                DB::table('estimates_adds')->where('id', $id_estimate_number->id)->delete();
            }

            /** delete record table estimates */
            Estimates::destroy($request->id);

            DB::commit();
            Toastr::success('Demande supprimé :)','Success');
            return redirect()->back();
            
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Estimates deleted fail :)','Error');
            return redirect()->back();
        }
    }

  
    public function startManagingEstimate($estimate_number)
{
    $estimate = Estimates::where('estimate_number', $estimate_number)->firstOrFail();

    if ($estimate->managed_by && $estimate->managed_by != Auth::id()) {
        $manager = User::find($estimate->managed_by);
        return response()->json(['error' => "This estimate is currently being managed by {$manager->name}."], 403);
    }

    // Lock the estimate for the current acheteur
    $estimate->managed_by = Auth::id();
    $estimate->managed_at = now();
    $estimate->save();

    return response()->json(['success' => 'You have started managing the estimate.']);
}

public function stopManagingEstimate($estimate_number)
{
    $estimate = Estimates::where('estimate_number', $estimate_number)->firstOrFail();

    if ($estimate->managed_by != Auth::id()) {
        return response()->json(['error' => 'You cannot release this estimate because you are not the one managing it.'], 403);
    }

    // Unlock the estimate
    $estimate->managed_by = null;
    $estimate->managed_at = null;
    $estimate->save();

    return response()->json(['success' => 'You have stopped managing the estimate.']);
}


   



}
