<?php

namespace App\Http\Controllers;
use App\Models\Etudiant;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaiementValideMail;
use Illuminate\Http\Request;
use App\Traits\GenerateApiResponse;
use App\Models\Paiement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ⭐ Ajoutez cette ligne
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\PaymentIntent;

class PaiementController extends Controller
{
    use GenerateApiResponse;

    // =============================
    // 1️⃣ Actions Comptable / Admin
    // =============================

    public function index()
    {
        try {
            $paiements = Paiement::with([
                'inscription.etudiant.personne', // infos étudiant
                'inscription.classe',            // infos classe
                'comptable.personne'             // infos comptable
            ])->get();

            return view('comptable.paiements.index', compact('paiements'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Récupération échouée : ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $paiement = Paiement::with([
                'inscription.etudiant.personne.user',
                'inscription.classe',
                'comptable.personne'
            ])->findOrFail($id);

            return $this->successResponse($paiement, 'Ressource trouvée');
        } catch (Exception $e) {
            return $this->errorResponse('Ressource non trouvée', 404, $e->getMessage());
        }
    }

    public function enAttente()
    {
        $paiements = Paiement::with([
            'inscription.etudiant.personne.user',
            'inscription.classe',
            'comptable.personne'
        ])
        ->where('statut', 'en_attente')
        ->get();

        return view('comptable.paiements.valider', compact('paiements'));
    }

    public function historique()
    {
        $paiements = Paiement::with([
            'inscription.etudiant.personne',
            'inscription.classe',
            'comptable.personne'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('comptable.historique.index', compact('paiements'));
    }

    public function details(Paiement $paiement)
    {
        $paiement->load([
            'inscription.etudiant.personne.user',
            'inscription.classe',
            'comptable.personne'
        ]);

        return view('comptable.paiements.details', compact('paiement'));
    }

    public function recu(Paiement $paiement)
    {
        $paiement->load([
            'inscription.etudiant.personne',
            'inscription.classe',
            'comptable.personne'
        ]);

        return view('comptable.paiements.recu', compact('paiement'));
    }

    public function sendEmail(Paiement $paiement)
    {
        try {
            $paiement->load('inscription.etudiant.personne.user');

            $user = $paiement->inscription?->etudiant?->personne?->user;

            if (!$user) {
                throw new Exception('Aucun utilisateur associé à cet étudiant');
            }

            Mail::to($user->email)->send(new PaiementValideMail($paiement));

            return response()->json(['message' => 'Email envoyé avec succès']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'envoi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function valider(Paiement $paiement)
    {
        try {
            $paiement->load('inscription.etudiant.personne.user');

            $paiement->update(['statut' => 'valide']);

            return response()->json([
                'message' => 'Paiement validé avec succès',
                'paiement' => $paiement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la validation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rejeter(Paiement $paiement)
    {
        $paiement->update(['statut' => 'rejeté']);
        return response()->json(['message' => 'Paiement rejeté', 'paiement' => $paiement]);
    }

   // use Barryvdh\DomPDF\Facade\Pdf;

public function exportPDF()
{
    $paiements = Paiement::with([
            'inscription.etudiant.personne',
            'inscription.classe'
        ])->orderBy('created_at', 'desc')->get();

    $html = view('comptable.historique.export-pdf', compact('paiements'))->render();

    $filename = 'historique_paiements_'.now()->format('Y-m-d').'.pdf';

    return response()->make(
        PDF::loadHTML($html)->output(),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]
    );
}

    public function exportExcel()
{
    $paiements = Paiement::with([
        'inscription.etudiant.personne',
        'inscription.classe',
        'comptable.personne'
    ])->get();

    $filename = 'historique_paiements.csv';

    $handle = fopen($filename, 'w+');
    fputcsv($handle, ['Nom', 'Prénom', 'Matricule', 'Classe', 'Montant', 'Date', 'Statut']);

    foreach ($paiements as $paiement) {
        $nom = $paiement->inscription->etudiant->personne->nom ?? '';
        $prenom = $paiement->inscription->etudiant->personne->prenom ?? '';
        $matricule = $paiement->inscription->etudiant->matricule ?? '';
        $classe = $paiement->inscription->classe->nom ?? '';
        $montant = $paiement->montant;
        $date = $paiement->date_paiement;
        $statut = $paiement->statut;

        fputcsv($handle, [$nom, $prenom, $matricule, $classe, $montant, $date, $statut]);
    }

    fclose($handle);

    return response()->download($filename)->deleteFileAfterSend(true);
}

public function showDetails($id)
{
    try {
        $paiement = Paiement::with([
            'inscription.etudiant.personne.user',
            'inscription.classe',
            'comptable.personne'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'paiement' => $paiement
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Paiement non trouvé'
        ], 404);
    }
}


    // =============================
    // 2️⃣ Actions Étudiant
    // =============================

    public function studentPayments(Request $request)
{
    try {
        /** @var User $user */
        $user = Auth::user();

        // Chargement des relations avec la méthode with()
        $user->load([
            'personne.etudiant' => function($query) {
                $query->with([
                    'paiements' => function($query) {
                        $query->with('inscription.classe')
                              ->orderBy('created_at', 'desc');
                    }
                ]);
            }
        ]);

        // Vérifications en une seule fois
        if (!$user->personne?->etudiant) {
            throw new \Exception('Profil étudiant non trouvé');
        }

        $paiements = $user->personne->etudiant->paiements;

        return view('etudiant.paiements', compact('paiements'));

    } catch (\Exception $e) {
        return back()->with('error', 'Erreur : ' . $e->getMessage());
    }
}

    // =============================
    // 5️⃣ Actions Étudiant - Détails et PDF
    // =============================

    public function etudiantPaiementDetails($id)
    {
        try {
            $user = Auth::user();

            $paiement = Paiement::with([
                'inscription.etudiant.personne.user',
                'inscription.classe',
                'comptable.personne'
            ])
            ->whereHas('inscription.etudiant.personne.user', function($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->findOrFail($id);

            return response()->json([
                'success' => true,
                'paiement' => $paiement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement non trouvé ou accès non autorisé'
            ], 404);
        }
    }

    public function etudiantPaiementRecu($id)
    {
        try {
            $user = Auth::user();

            $paiement = Paiement::with([
                'inscription.etudiant.personne.user',
                'inscription.classe',
                'comptable.personne'
            ])
            ->whereHas('inscription.etudiant.personne.user', function($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->where('statut', 'validé')
            ->findOrFail($id);

            $html = view('etudiant.paiements.recu-pdf', compact('paiement'))->render();

            $filename = 'recu_paiement_' . $paiement->reference_transaction . '.pdf';

            return response()->make(
                PDF::loadHTML($html)->output(),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]
            );
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Reçu non disponible ou accès non autorisé']);
        }
    }

    // =============================
    // 3️⃣ Paiement via Orange Money
    // =============================

    public function processPayment(Request $request)
{
    $validated = $request->validate([
        'montant' => 'required|numeric|min:1000|max:1000000',
        'type_frais' => 'required|in:inscription,mensualite,soutenance',
        'inscription_id' => 'required|exists:inscriptions,id',
        'telephone' => 'required|regex:/^6[0-9]{8}$/'
    ]);

    try {
        $reference = 'OM-'.Str::upper(Str::random(8)).'-'.time();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getOrangeToken(),
            'Content-Type' => 'application/json'
        ])->post(config('services.orange.payment_url'), [
            'merchant_key' => config('services.orange.merchant_key'),
            'amount' => $validated['montant'],
            'phone_number' => $validated['telephone'],
            'reference' => $reference,
            'callback_url' => route('paiement.orange.callback')
        ]);

        if ($response->successful()) {
            // 🔒 Empêche doublons
            $paiement = Paiement::firstOrCreate(
                [
                    'inscription_id' => $validated['inscription_id'],
                    'type_frais'     => $validated['type_frais'],
                    'montant'        => $validated['montant'],
                    'mode_paiement'  => 'orange_money',
                    'statut'         => 'en_attente',
                ],
                [
                    'reference_transaction' => $reference,
                    'telephone'             => $validated['telephone'],
                    'date_paiement'         => now()->format('Y-m-d'),
                ]
            );

            return response()->json([
                'status' => 'success',
                'payment_url' => $response->json()['payment_url'] ?? null,
                'reference' => $paiement->reference_transaction
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $response->json()['message'] ?? 'Erreur Orange Money'
        ], 400);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}


    public function handleOrangeCallback(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required',
            'status' => 'required|in:SUCCESS,FAILED'
        ]);

        $paiement = Paiement::where('reference_transaction', $validated['reference'])->firstOrFail();

        $paiement->update([
            'statut' => $validated['status'] === 'SUCCESS' ? 'valide' : 'echec',
            'date_validation' => now()
        ]);

        // Envoyer une notification ici si nécessaire

        return response()->json(['status' => 'ok']);
    }

    private function getOrangeToken()
    {
        $response = Http::withBasicAuth(
            config('services.orange.client_id'),
            config('services.orange.client_secret')
        )->asForm()->post(config('services.orange.token_url'), [
            'grant_type' => 'client_credentials'
        ]);

        return $response->json()['access_token'] ?? null;
    }

    //Methode orange monaie direct
    public function initierOrangeMoney(Request $request)
{
    // 1. Validation des données d'entrée
    $validated = $request->validate([
        'telephone' => 'required|regex:/^6[0-9]{8}$/',
        'etudiant_id' => 'required|exists:etudiants,id',
        'inscription_id' => 'required|exists:inscriptions,id',
        'type_frais' => 'required|in:inscription,mensualite,soutenance'
    ]);

    // 2. Récupération des frais depuis la classe de l'étudiant
    $etudiant = Etudiant::with('classe')->findOrFail($validated['etudiant_id']);

    $montant = match($validated['type_frais']) {
        'inscription' => $etudiant->classe->frais_inscription,
        'mensualite' => $etudiant->classe->frais_mensualite,
        'soutenance' => $etudiant->classe->frais_soutenance,
        default => throw new \Exception('Type de frais non supporté')
    };

    // 3. Initiation du paiement Orange Money
    $reference = 'OM_'.time().'_'.strtoupper(Str::random(6));

    $response = Http::withHeaders([
        'Authorization' => 'Bearer '.env('ORANGE_MONEY_API_KEY'),
        'Content-Type' => 'application/json'
    ])->post('https://api.orange.com/orange-money-api/v1/payments', [
        'merchant_key' => env('ORANGE_MERCHANT_KEY'),
        'amount' => $montant,
        'phone_number' => $validated['telephone'],
        'reference' => $reference,
        'callback_url' => route('paiement.orange.callback')
    ]);

    // 4. Enregistrement du paiement
    if ($response->successful()) {
        $paiement = Paiement::create([
            'inscription_id' => $validated['inscription_id'],
            'comptable_id' => null, // Ou mettre l'ID du système si requis
            'date_paiement' => now()->format('Y-m-d'),
            'montant' => $montant,
            'motif' => $validated['type_frais'],
            'reference_transaction' => $reference,
            'mode_paiement' => 'orange_money',
            'statut' => 'en_attente',
            'validation_email_envoye' => 0
        ]);

        // 5. Envoi du mail de validation (à implémenter)
        // $this->envoyerEmailValidation($paiement);

        return response()->json([
            'status' => 'success',
            'payment_url' => $response->json()['payment_url'],
            'reference' => $reference,
            'montant' => $montant
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'Échec de l\'initiation du paiement',
        'error_detail' => $response->json()
    ], 400);
}
public function callbackOrangeMoney(Request $request)
{
    $paiement = Paiement::where('reference_transaction', $request->reference)->firstOrFail();

    if ($request->status === 'SUCCESS') {
        $paiement->update([
            'statut' => 'valide',
            'date_validation' => now()
        ]);
    } else {
        $paiement->update(['statut' => 'echec']);
    }

    return response()->json(['status' => 'ok']);
}

    // =============================
    // 4️⃣ Rapports Financiers
    // =============================

    public function rapportsFinanciers(Request $request)
    {
        try {
            $periode = $request->get('periode', 'mois'); // mois, trimestre, annee
            $annee = $request->get('annee', now()->year);
            $mois = $request->get('mois', now()->month);

            // 1. Statistiques générales
            $statistiques = $this->getStatistiquesGenerales($periode, $annee, $mois);

            // 2. Analyse par type de frais
            $analyseTypeFrais = $this->getAnalyseTypeFrais($periode, $annee, $mois);

            // 3. Analyse par classe
            $analyseClasses = $this->getAnalyseParClasse($periode, $annee, $mois);

            // 4. Analyse par mode de paiement
            $analyseModePaiement = $this->getAnalyseModePaiement($periode, $annee, $mois);

            // 5. Évolution temporelle (12 derniers mois)
            $evolutionTemporelle = $this->getEvolutionTemporelle();

            return view('comptable.rapports.index', compact(
                'statistiques',
                'analyseTypeFrais',
                'analyseClasses',
                'analyseModePaiement',
                'evolutionTemporelle',
                'periode',
                'annee',
                'mois'
            ));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la génération du rapport : ' . $e->getMessage()]);
        }
    }

    private function getStatistiquesGenerales($periode, $annee, $mois)
    {
        $query = Paiement::query();

        // Filtrage par période
        switch ($periode) {
            case 'mois':
                $query->whereYear('date_paiement', $annee)
                      ->whereMonth('date_paiement', $mois);
                break;
            case 'trimestre':
                $trimestre = ceil($mois / 3);
                $moisDebut = ($trimestre - 1) * 3 + 1;
                $moisFin = $trimestre * 3;
                $query->whereYear('date_paiement', $annee)
                      ->whereBetween(DB::raw('MONTH(date_paiement)')
, [$moisDebut, $moisFin]);
                break;
            case 'annee':
                $query->whereYear('date_paiement', $annee);
                break;
        }

        $paiements = $query->get();

        return [
            'revenus_totaux' => $paiements->where('statut', 'validé')->sum('montant'),
            'nombre_paiements_valides' => $paiements->where('statut', 'valide')->count(),
            'nombre_paiements_attente' => $paiements->where('statut', 'en_attente')->count(),
            'nombre_paiements_rejetes' => $paiements->where('statut', 'rejete')->count(),
            'taux_validation' => $paiements->count() > 0 ?
                round(($paiements->where('statut', 'valide')->count() / $paiements->count()) * 100, 2) : 0,
            'revenus_moyens_etudiant' => $paiements->where('statut', 'valide')->count() > 0 ?
                round($paiements->where('statut', 'valide')->sum('montant') / $paiements->where('statut', 'valide')->unique('inscription.etudiant_id')->count()) : 0
        ];
    }

    private function getAnalyseTypeFrais($periode, $annee, $mois)
    {
        $query = Paiement::with('inscription.classe')
                         ->where('statut', 'valide');

        // Même filtrage par période
        switch ($periode) {
            case 'mois':
                $query->whereYear('date_paiement', $annee)
                      ->whereMonth('date_paiement', $mois);
                break;
            case 'trimestre':
                $trimestre = ceil($mois / 3);
                $moisDebut = ($trimestre - 1) * 3 + 1;
                $moisFin = $trimestre * 3;
                $query->whereYear('date_paiement', $annee)
                      ->whereBetween(DB::raw('MONTH(date_paiement)')
, [$moisDebut, $moisFin]);
                break;
            case 'annee':
                $query->whereYear('date_paiement', $annee);
                break;
        }

        $paiements = $query->get();
        $totalRevenus = $paiements->sum('montant');

        // Estimation des types de frais basée sur les montants
        $fraisInscription = $paiements->filter(function($p) {
            return $p->montant == $p->inscription->classe->frais_inscription;
        });

        $fraisMensualite = $paiements->filter(function($p) {
            return $p->montant == $p->inscription->classe->frais_mensualite;
        });

        $fraisSoutenance = $paiements->filter(function($p) {
            return $p->montant == $p->inscription->classe->frais_soutenance;
        });

        return [
            'inscription' => [
                'montant' => $fraisInscription->sum('montant'),
                'nombre' => $fraisInscription->count(),
                'pourcentage' => $totalRevenus > 0 ? round(($fraisInscription->sum('montant') / $totalRevenus) * 100, 2) : 0
            ],
            'mensualite' => [
                'montant' => $fraisMensualite->sum('montant'),
                'nombre' => $fraisMensualite->count(),
                'pourcentage' => $totalRevenus > 0 ? round(($fraisMensualite->sum('montant') / $totalRevenus) * 100, 2) : 0
            ],
            'soutenance' => [
                'montant' => $fraisSoutenance->sum('montant'),
                'nombre' => $fraisSoutenance->count(),
                'pourcentage' => $totalRevenus > 0 ? round(($fraisSoutenance->sum('montant') / $totalRevenus) * 100, 2) : 0
            ]
        ];
    }

    private function getAnalyseParClasse($periode, $annee, $mois)
    {
        $query = Paiement::with(['inscription.classe', 'inscription.etudiant'])
                         ->where('statut', 'validé');

        // Même filtrage par période
        switch ($periode) {
            case 'mois':
                $query->whereYear('date_paiement', $annee)
                      ->whereMonth('date_paiement', $mois);
                break;
            case 'trimestre':
                $trimestre = ceil($mois / 3);
                $moisDebut = ($trimestre - 1) * 3 + 1;
                $moisFin = $trimestre * 3;
                $query->whereYear('date_paiement', $annee)
                      ->whereBetween(DB::raw('MONTH(date_paiement)')
, [$moisDebut, $moisFin]);
                break;
            case 'annee':
                $query->whereYear('date_paiement', $annee);
                break;
        }

        return $query->get()
                    ->groupBy('inscription.classe.libelle')
                    ->map(function($paiements, $classe) {
                        return [
                            'classe' => $classe,
                            'revenus' => $paiements->sum('montant'),
                            'nombre_paiements' => $paiements->count(),
                            'nombre_etudiants' => $paiements->unique('inscription.etudiant_id')->count(),
                            'revenu_moyen_etudiant' => $paiements->unique('inscription.etudiant_id')->count() > 0 ?
                                round($paiements->sum('montant') / $paiements->unique('inscription.etudiant_id')->count()) : 0
                        ];
                    })
                    ->sortByDesc('revenus');
    }

    private function getAnalyseModePaiement($periode, $annee, $mois)
    {
        $query = Paiement::where('statut', 'valide');

        // Même filtrage par période
        switch ($periode) {
            case 'mois':
                $query->whereYear('date_paiement', $annee)
                      ->whereMonth('date_paiement', $mois);
                break;
            case 'trimestre':
                $trimestre = ceil($mois / 3);
                $moisDebut = ($trimestre - 1) * 3 + 1;
                $moisFin = $trimestre * 3;
                $query->whereYear('date_paiement', $annee)
                      ->whereBetween(DB::raw('MONTH(date_paiement)')
, [$moisDebut, $moisFin]);
                break;
            case 'annee':
                $query->whereYear('date_paiement', $annee);
                break;
        }

        $paiements = $query->get();
        $totalRevenus = $paiements->sum('montant');

        return $paiements->groupBy('mode_paiement')
                        ->map(function($paiements, $mode) use ($totalRevenus) {
                            return [
                                'mode' => ucfirst(str_replace('_', ' ', $mode)),
                                'montant' => $paiements->sum('montant'),
                                'nombre' => $paiements->count(),
                                'pourcentage' => $totalRevenus > 0 ?
                                    round(($paiements->sum('montant') / $totalRevenus) * 100, 2) : 0
                            ];
                        })
                        ->sortByDesc('montant');
    }

    private function getEvolutionTemporelle()
    {
        $evolution = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenus = Paiement::where('statut', 'valide')
                              ->whereYear('date_paiement', $date->year)
                              ->whereMonth('date_paiement', $date->month)
                              ->sum('montant');

            $evolution[] = [
                'mois' => $date->format('M Y'),
                'revenus' => $revenus
            ];
        }

        return $evolution;
    }

    public function exportRapportPDF(Request $request)
    {
        $periode = $request->get('periode', 'mois');
        $annee = $request->get('annee', now()->year);
        $mois = $request->get('mois', now()->month);

        $statistiques = $this->getStatistiquesGenerales($periode, $annee, $mois);
        $analyseTypeFrais = $this->getAnalyseTypeFrais($periode, $annee, $mois);
        $analyseClasses = $this->getAnalyseParClasse($periode, $annee, $mois);
        $analyseModePaiement = $this->getAnalyseModePaiement($periode, $annee, $mois);

        $html = view('comptable.rapports.export-pdf', compact(
            'statistiques',
            'analyseTypeFrais',
            'analyseClasses',
            'analyseModePaiement',
            'periode',
            'annee',
            'mois'
        ))->render();

        $filename = 'rapport_financier_' . $periode . '_' . $annee . '_' . $mois . '.pdf';

        return response()->make(
            PDF::loadHTML($html)->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        );
    }


// =============================
// 4️⃣ Paiement via Stripe
// =============================

public function processStripePayment(Request $request)
{
    $validated = $request->validate([
        'montant' => 'required|numeric|min:1000|max:1000000',
        'type_frais' => 'required|in:inscription,mensualite,soutenance',
        'inscription_id' => 'required|exists:inscriptions,id',
        'stripeToken' => 'required|string'
    ]);

    try {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $reference = 'STRIPE-' . Str::upper(Str::random(8)) . '-' . time();

        // Création du paiement Stripe
        $paymentIntent = PaymentIntent::create([
            'amount' => $validated['montant'] * 100, // Stripe en centimes
            'currency' => 'xaf', // FCFA
            'payment_method' => $validated['stripeToken'],
            'description' => 'Paiement ' . $validated['type_frais'] . ' pour inscription #' . $validated['inscription_id'],
            'confirm' => true
        ]);

        // 🔒 Empêche doublons
        $paiement = Paiement::firstOrCreate(
            [
                'inscription_id' => $validated['inscription_id'],
                'type_frais'     => $validated['type_frais'],
                'montant'        => $validated['montant'],
                'mode_paiement'  => 'carte_bancaire',
                'statut'         => 'valide',
            ],
            [
                'reference_transaction' => $reference,
                'date_paiement'         => now()->format('Y-m-d'),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Paiement par carte bancaire effectué avec succès !',
            'reference' => $paiement->reference_transaction,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

public function stripeSuccess(Request $request)
{
    return redirect()->route('etudiant.paiements')->with('success', 'Paiement effectué avec succès !');
}

public function stripeCancel(Request $request)
{
    return redirect()->route('etudiant.paiements')->with('error', 'Paiement annulé.');
}

}
