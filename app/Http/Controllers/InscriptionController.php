<?php

namespace App\Http\Controllers;
use App\Models\User; // Ajouté
use App\Models\Administration; // Ajouté
use App\Models\Classe; // Ajouté
use App\Models\Inscription;
use App\Models\Etudiant;
use Illuminate\Http\Request;
use App\Traits\GenerateApiResponse;
use Exception;
use App\Mail\InscriptionValideeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;  // ← Ajoutez cette ligne
use Maatwebsite\Excel\Facades\Excel; // Ajouté
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class InscriptionController extends Controller
{
    use GenerateApiResponse;

    public function create()
    {
        $etudiants = Etudiant::with('personne')->get();
        $classes = Classe::all();
        return view('administration.inscriptions.inscrire', compact('etudiants', 'classes'));
    }

    public function index()
    {
        try {
            // Récupérer la liste des classes pour le filtre
            $classes = Classe::orderBy('libelle')->get();

            // Récupérer les années académiques uniques pour le filtre
            $anneesAcademiques = Inscription::select('annee_academique')
                ->distinct()
                ->orderBy('annee_academique', 'desc')
                ->pluck('annee_academique')
                ->filter();

            // Vérifier les valeurs de statut uniques dans la table
            $statutsUniques = Inscription::select('statut')
                ->distinct()
                ->pluck('statut')
                ->filter()
                ->values();

            Log::info('Valeurs de statut uniques dans la base de données : ' . $statutsUniques->toJson());

            $query = Inscription::with(['etudiant.personne', 'classe', 'administration.personne']);

            // Filtrage par statut
            if (request()->has('statut') && request('statut') !== 'all') {
                $query->where('statut', request('statut'));
            }

            // Filtrage par classe
            if (request()->has('classe_id') && !empty(request('classe_id'))) {
                $query->where('classe_id', request('classe_id'));
            }

            // Filtrage par année académique
            if (request()->has('annee_academique') && !empty(request('annee_academique'))) {
                $query->where('annee_academique', request('annee_academique'));
            }

            // Recherche par nom/prénom d'étudiant
            if (request()->has('search')) {
                $search = request('search');
                $query->whereHas('etudiant.personne', function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%");
                });
            }

            // Utilisation de paginate() au lieu de get()
            $inscriptions = $query->latest()->paginate(10);
            $toutesInscriptions = Inscription::all();

            // Initialisation de la variable statistiques avec des valeurs par défaut
            $statistiques = [
                'total' => 0,
                'validee' => 0,
                'en_attente' => 0,
                'rejetee' => 0,
                'taux_validation' => 0,
                'evolution' => 0,
            ];

            // Calcul des statistiques uniquement si on a des inscriptions
            if ($toutesInscriptions->isNotEmpty()) {
                $totalInscriptions = $toutesInscriptions->count();

                // Compter les inscriptions par statut
                $statuts = $toutesInscriptions->groupBy('statut')->map->count();
                Log::info('Statuts comptés : ' . json_encode($statuts->toArray()));

                $statistiques = [
                    'total' => $totalInscriptions,
                    'validee' => $statuts->get('validee', 0),
                    'en_attente' => $statuts->get('en_attente', 0),
                    'rejetee' => $statuts->get('rejetee', 0),
                    'taux_validation' => $totalInscriptions > 0 ?
                        round(($statuts->get('validee', 0) / $totalInscriptions) * 100) : 0,
                    'evolution' => 0,
                ];

                Log::info('Statistiques calculées : ' . json_encode($statistiques));
            }

            return view('administration.inscriptions.index',
                compact('inscriptions', 'toutesInscriptions', 'statistiques', 'classes', 'anneesAcademiques'));

        } catch (Exception $e) {
            Log::error('Erreur dans InscriptionController@index : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la récupération des inscriptions : ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $inscription = new Inscription();
            $inscription->etudiant_id = $request->etudiant_id;
            $inscription->classe_id = $request->classe_id;

            // On récupère l'ID de l'administration liée à l'utilisateur connecté
            $user = Auth::user();
/** @var User $user */
$administration = $user->administration;
            if (!$administration) {
                return redirect()->back()->with('error', 'Impossible de récupérer l\'administration de l\'utilisateur connecté.');
            }
            $inscription->administration_id = $administration->id;

            $inscription->annee_academique = $request->annee_academique;
            $inscription->date_inscription = $request->date_inscription;
            $inscription->statut = 'en_attente';
            $inscription->save();

            return redirect()->route('administration.dashboard')
                ->with('success', 'Inscription créée avec succès. L\'étudiant a été inscrit en attente de validation.');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'inscription : ' . $e->getMessage());
        }
    }

    public function update(Request $request, Inscription $inscription)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'etudiant_id' => 'required|exists:etudiants,id',
                'classe_id' => 'required|exists:classes,id',
                'annee_academique' => 'required|regex:/^\d{4}-\d{4}$/',
                'date_inscription' => 'required|date',
                'statut' => 'required|in:en_attente,validee,rejetee',
            ]);

            // Vérifier si le statut change à 'valide' ou 'rejeté' et enregistrer l'administrateur
            if (($validated['statut'] === 'validee' || $validated['statut'] === 'rejetee') && !$inscription->administration_id) {
                $validated['administration_id'] = auth('admin')->id();
            }

            // Mise à jour de l'inscription
            $inscription->update($validated);

            // Si le statut est validé, on peut déclencher des actions supplémentaires ici
            if ($validated['statut'] === 'validee') {
                // Par exemple, envoyer un email de confirmation
                // Mail::to($inscription->etudiant->personne->email)->send(new InscriptionValideeMail($inscription));
            }

            return redirect()->route('administration.inscriptions.index')
                ->with('success', 'L\'inscription a été mise à jour avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'inscription : ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'inscription : ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $inscription = Inscription::findOrFail($id);
            $inscription->delete();

            return $this->successResponse($inscription, 'Suppression réussie');
        } catch (Exception $e) {
            return $this->errorResponse('Suppression échouée', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $inscription = Inscription::with(['etudiant.personne', 'classe', 'administration.personne'])
                ->findOrFail($id);

            return view('administration.inscriptions.show', compact('inscription'));
        } catch (Exception $e) {
            return redirect()->route('administration.inscriptions.index')
                ->with('error', 'Inscription introuvable.');
        }
    }

    public function getformdetails()
    {
        try {
            return $this->successResponse([], 'Données du formulaire récupérées avec succès');
        } catch (Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des données du formulaire', 500, $e->getMessage());
        }
    }

    public function validateInscription(Inscription $inscription)
{
    // Garder votre logique de validation originale
    if ($inscription->statut !== 'en_attente') {
        return redirect()->route('admin.inscriptions.index')
            ->with('error', 'Cette inscription ne peut pas être validée.');
    }

    $user = Auth::user();
    /** @var User $user */
    $administration = $user->administration;
    if (!$administration) {
        return redirect()->back()->with('error', 'Impossible de récupérer l\'administration de l\'utilisateur connecté.');
    }

    // Mise à jour (garder votre méthode originale)
    $inscription->statut = 'validee';
    $inscription->administration_id = $administration->id;
    $inscription->save();

    // ✅ COPIER EXACTEMENT LA LOGIQUE EMAIL DES PAIEMENTS
    $inscription->load('etudiant.personne.user'); // Charger les relations comme dans paiements
    $etudiantUser = $inscription->etudiant?->personne?->user;

    $emailSent = false;
    try {
        if ($etudiantUser && $etudiantUser->email) {
            Mail::to($etudiantUser->email)->send(new InscriptionValideeMail($inscription));
            $emailSent = true;
        }
    } catch (Exception $emailException) {
        // Log l'erreur mais continue (comme pour les paiements)
        Log::error('Erreur envoi email validation inscription: ' . $emailException->getMessage());
        $emailSent = false;
    }

    return redirect()->route('admin.inscriptions.index')
        ->with('success', 'Inscription validée avec succès.' .
               ($emailSent ? ' Email envoyé à l\'étudiant.' :
                ' (Erreur envoi email - vérifier configuration SMTP)'));
}
    public function rejectInscription(Inscription $inscription)
    {
        if ($inscription->statut !== 'en_attente') {
            return redirect()->route('admin.inscriptions.index')
                ->with('error', 'Cette inscription ne peut pas être rejetée.');
        }

        $inscription->update([
            'statut' => 'rejeté'
        ]);

        return redirect()->route('admin.inscriptions.index')
            ->with('success', 'Inscription rejetée avec succès.');
    }


 // ✅ Affiche le formulaire d’inscription multiple
   public function inscrireMultiple()
{
    try {
        $classes = Classe::orderBy('libelle')->get();
        $etudiants = Etudiant::with('personne')
            ->whereHas('personne')
            ->orderBy('matricule')
            ->get();

        return view('administration.inscriptions.inscrire-multiple', compact('classes', 'etudiants'));
    } catch (\Exception $e) {
        return redirect()->route('admin.inscriptions.index')
            ->with('error', 'Erreur lors du chargement du formulaire : ' . $e->getMessage());
    }
}

    // ✅ Traite l’inscription multiple
    public function storeMultiple(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'etudiants' => 'required|array|min:1',
            'etudiants.*' => 'exists:etudiants,id',
            'annee_academique' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'date_inscription' => 'required|date|before_or_equal:today',
        ], [
            'etudiants.required' => 'Veuillez sélectionner au moins un étudiant.',
            'etudiants.min' => 'Veuillez sélectionner au moins un étudiant.',
            'annee_academique.regex' => 'Le format de l\'année académique doit être AAAA-AAAA.',
            'date_inscription.before_or_equal' => 'La date d\'inscription ne peut pas être dans le futur.'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $administration = $user->administration;

            if (!$administration) {
                return redirect()->back()
                    ->with('error', 'Impossible de récupérer les informations d\'administration.')
                    ->withInput();
            }

            $createdCount = 0;
            $warnings = [];
            $existingInscriptions = [];

            // Vérification des inscriptions existantes
            foreach ($request->etudiants as $etudiant_id) {
                $exists = Inscription::where('etudiant_id', $etudiant_id)
                    ->where('classe_id', $request->classe_id)
                    ->where('annee_academique', $request->annee_academique)
                    ->exists();

                if ($exists) {
                    $etudiant = Etudiant::with('personne')->find($etudiant_id);
                    $existingInscriptions[] = $etudiant->personne->nom . ' ' . $etudiant->personne->prenom;
                    continue;
                }

                // Création de l'inscription
                Inscription::create([
                    'etudiant_id' => $etudiant_id,
                    'classe_id' => $request->classe_id,
                    'administration_id' => $administration->id,
                    'annee_academique' => $request->annee_academique,
                    'date_inscription' => $request->date_inscription,
                    'statut' => 'en_attente',
                ]);

                $createdCount++;
            }

            // Préparation des messages de retour
            $messages = [];

            if ($createdCount > 0) {
                $messages['success'] = "$createdCount inscription(s) créée(s) avec succès.";
            }

            if (!empty($existingInscriptions)) {
                $warnings[] = count($existingInscriptions) . ' étudiant(s) déjà inscrit(s) : ' .
                    implode(', ', $existingInscriptions);
            }

            if (!empty($warnings)) {
                $messages['warnings'] = $warnings;
            }

            if ($createdCount === 0) {
                return redirect()->back()
                    ->with('warnings', $warnings)
                    ->with('error', 'Aucune nouvelle inscription n\'a été créée.')
                    ->withInput();
            }

            DB::commit();

            return redirect()->route('admin.inscriptions.index')
                ->with($messages);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'inscription multiple : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création des inscriptions : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Exporte la liste des inscriptions au format Excel
     *
     * @return \Illuminate\Http\Response
     */
   /**  public function export()
   * {
   *     return Excel::download(new \App\Exports\InscriptionsExport, 'inscriptions-' . now()->format('Y-m-d') . '.xlsx');
   * }
*/
    /**
     * Afficher le formulaire de modification d'une inscription
     *
     * @param  \App\Models\Inscription  $inscription
     * @return \Illuminate\Http\Response
     */
    public function edit(Inscription $inscription)
    {
        try {
            // Vérifier les autorisations si nécessaire
            // $this->authorize('update', $inscription);

            // Récupérer les données nécessaires pour le formulaire
            $etudiants = Etudiant::with('personne')->get();
            $classes = Classe::orderBy('libelle')->get();
            $administrations = Administration::with('personne')->get();

            return view('administration.inscriptions.edit', compact('inscription', 'etudiants', 'classes', 'administrations'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du formulaire de modification : ' . $e->getMessage());
            return redirect()->route('administration.inscriptions.index')
                ->with('error', 'Impossible d\'afficher le formulaire de modification : ' . $e->getMessage());
        }
    }
}
