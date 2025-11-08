<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Traits\GenerateApiResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Paiement;
use App\Models\User; // Import du modèle User
use Illuminate\Support\Facades\Mail;
use App\Mail\PaiementValideMail;
use App\Models\Classe;
use Barryvdh\DomPDF\Facade\Pdf;

use Exception;

class ComptableController extends Controller
{
    use GenerateApiResponse;

    public function dashboard()
    {
        try {
            $paiementsEnAttente = Paiement::where('statut', 'en_attente')->count();
            $paiementsValides   = Paiement::where('statut', 'valide')->count();
            $paiementsRejetes   = Paiement::where('statut', 'rejete')->count();
            $montantTotal       = Paiement::where('statut', 'valide')->sum('montant');
            $montantCeMois      = Paiement::where('statut', 'valide')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('montant');

            $paiementsRecents = Paiement::with(['etudiant.personne', 'inscription.classe'])
                ->where('statut', 'en_attente')
                ->latest()
                ->limit(5)
                ->get();

            return view('comptable.dashboard', compact(
                'paiementsEnAttente',
                'paiementsValides',
                'paiementsRejetes',
                'montantTotal',
                'montantCeMois',
                'paiementsRecents'
            ));
        } catch (Exception $e) {
            return $this->errorResponse('Erreur du dashboard', 500, $e->getMessage());
        }
    }

    public function paiements()
    {
        try {
            $paiements = Paiement::with(['etudiant.personne', 'inscription.classe'])
                ->where('statut', 'en_attente')
                ->latest()
                ->get();
            return view('comptable.paiements.index', compact('paiements'));
        } catch (Exception $e) {
            return $this->errorResponse('Erreur de récupération', 500, $e->getMessage());
        }
    }

    // Correct
public function historiquePaiements()
{
    $paiements = Paiement::with([
        'inscription.etudiant.personne',
        'inscription.classe',
        'comptable.personne'
    ])
    ->latest()
    ->get();

    return view('comptable.historique.index', compact('paiements'));
}


    public function validerPaiement(Request $request, Paiement $paiement)
{
    try {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            throw new Exception("Utilisateur non authentifié");
        }

        // Vérification simple du rôle
        if ($user->role !== 'comptable') {
            throw new Exception("Accès réservé aux comptables");
        }

        // Récupérer l'ID du comptable via la relation
        $user->load(['personne.comptable']);
        $comptableId = $user->personne?->comptable?->id ?? null;

        $paiement->update([
            'statut'                 => 'valide',
            'comptable_id'           => $comptableId,
            'date_validation'        => now(),
            'commentaire_validation' => $request->input('commentaire', '')
        ]);

        // Envoyer l'email de validation à l'étudiant
        $paiement->load('inscription.etudiant.personne.user');
        $etudiantUser = $paiement->inscription?->etudiant?->personne?->user;

        $emailSent = false;
        try {
            if ($etudiantUser && $etudiantUser->email) {
                Mail::to($etudiantUser->email)->send(new PaiementValideMail($paiement));
                $emailSent = true;
            }
        } catch (Exception $emailException) {
            // Log l'erreur mais continue la validation
            Log::error('Erreur envoi email validation paiement: ' . $emailException->getMessage());
            $emailSent = false;
        }

        return response()->json([
    'success' => true,
    'message' => 'Paiement validé avec succès.' . ($emailSent ? ' Email envoyé à l\'étudiant.' : ''),
    'recu_url' => route('comptable.paiements.recu', $paiement), // ✅ Maintenant ça va fonctionner
    'redirect_url' => route('comptable.dashboard')
]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la validation: ' . $e->getMessage()
        ], 500);
    }
}


    public function rejeterPaiement(Request $request, Paiement $paiement)
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (!$user?->comptable) {
                throw new Exception("Accès réservé aux comptables");
            }

            $paiement->update([
                'statut'                => 'rejete',
                'comptable_id'          => $user->comptable->id,
                'date_validation'       => now(),
                'commentaire_validation'=> $request->commentaire ?? 'Paiement rejeté'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paiement rejeté avec succès'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet: ' . $e->getMessage()
            ], 500);
        }
    }

/**
     * Exporter la liste des étudiants selon différents critères
     */
    public function exportEtudiantsPDF(Request $request)
    {
        $type = $request->input('type');
        $annee = $request->input('annee', now()->year);

        $data = [];
        $titre = '';

        switch ($type) {
            case 'mois':
                $mois = $request->input('mois');
                $data = $this->getEtudiantsParMois($mois, $annee);
                $titre = "Étudiants ayant payé - " . ucfirst($mois) . " " . $annee;
                break;

           case 'classe':
    $classeId = $request->input('classe_id');

    // Vérifier que la classe existe d'abord
    $classe = Classe::find($classeId);

    if (!$classe) {
        return redirect()->back()->with('error', 'Classe non trouvée');
    }

    // Récupérer les données
    $data = $this->getEtudiantsParClasse($classeId, $annee);

    // Vérifier s'il y a des données
    if ($data->isEmpty()) {
        return redirect()->back()->with('warning', 'Aucun étudiant trouvé pour cette classe et cette année académique');
    }

    $titre = "Étudiants de la classe " . $classe->libelle . " - " . $annee;
    break;

            case 'statut':
                $statut = $request->input('statut');
                $data = $this->getPaiementsParStatut($statut, $annee);
                $titre = "Paiements " . $this->getStatutLabel($statut) . " - " . $annee;
                break;

            case 'mode':
                $mode = $request->input('mode_paiement');
                $data = $this->getPaiementsParMode($mode, $annee);
                $titre = "Paiements par " . $this->getModeLabel($mode) . " - " . $annee;
                break;

            default:
                return back()->with('error', 'Type d\'export invalide');
        }

        $pdf = PDF::loadView('comptable.rapports.pdf-etudiants', [
            'titre' => $titre,
            'data' => $data,
            'type' => $type,
            'date_generation' => now()->format('d/m/Y à H:i')
        ]);

        return $pdf->download('rapport-' . $type . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Récupérer les étudiants ayant payé pour un mois donné
     */
    private function getEtudiantsParMois($mois, $annee)
    {
        return Paiement::with(['inscription.etudiant.personne', 'inscription.classe'])
            ->where('mois_a_payer', $mois)
            ->whereYear('date_paiement', $annee)
            ->where('statut', 'valide')
            ->get()
            ->map(function ($paiement) {
                return [
                    'nom' => $paiement->inscription->etudiant->personne->nom,
                    'prenom' => $paiement->inscription->etudiant->personne->prenom,
                    'matricule' => $paiement->inscription->etudiant->matricule,
                    'classe' => $paiement->inscription->classe->libelle,
                    'montant' => $paiement->montant,
                    'date_paiement' => $paiement->date_paiement->format('d/m/Y'),
                    'mode_paiement' => ucfirst(str_replace('_', ' ', $paiement->mode_paiement)),
                    'reference' => $paiement->reference_transaction,
                ];
            });
    }

    /**
     * Récupérer les étudiants d'une classe ayant effectué des paiements
     */
  private function getEtudiantsParClasse($classeId, $annee)
{
    // Convertir l'année simple en année académique si nécessaire
    // Ex: 2024 devient "2024-2025"
    $anneeAcademique = is_numeric($annee) && strlen($annee) == 4
        ? $annee . '-' . ($annee + 1)
        : $annee;

    return Paiement::with(['inscription.etudiant.personne', 'inscription.classe'])
        ->whereHas('inscription', function ($query) use ($classeId, $anneeAcademique) {
            $query->where('classe_id', $classeId)
                  ->where('annee_academique', $anneeAcademique);
        })
        ->where('statut', 'valide')
        ->get()
        ->groupBy('inscription.etudiant_id')
        ->map(function ($paiements) {
            $premier = $paiements->first();
            $dernierPaiement = $paiements->sortByDesc('date_paiement')->first(); // ✅ Correction ici

            return [
                'nom' => $premier->inscription->etudiant->personne->nom,
                'prenom' => $premier->inscription->etudiant->personne->prenom,
                'matricule' => $premier->inscription->etudiant->matricule,
                'classe' => $premier->inscription->classe->libelle,
                'total_paye' => number_format($paiements->sum('montant'), 0, ',', ' ') . ' FCFA',
                'nombre_paiements' => $paiements->count(),
                'dernier_paiement' => $dernierPaiement->date_paiement->format('d/m/Y'), // ✅ Correction ici
            ];
        })
        ->sortBy('nom')
        ->values();
}

    /**
     * Récupérer les paiements par statut
     */
    private function getPaiementsParStatut($statut, $annee)
    {
        return Paiement::with(['inscription.etudiant.personne', 'inscription.classe'])
            ->where('statut', $statut)
            ->whereYear('date_paiement', $annee)
            ->get()
            ->map(function ($paiement) {
                return [
                    'nom' => $paiement->inscription->etudiant->personne->nom,
                    'prenom' => $paiement->inscription->etudiant->personne->prenom,
                    'matricule' => $paiement->inscription->etudiant->matricule,
                    'classe' => $paiement->inscription->classe->libelle,
                    'montant' => $paiement->montant,
                    'date_paiement' => $paiement->date_paiement->format('d/m/Y'),
                    'mode_paiement' => ucfirst(str_replace('_', ' ', $paiement->mode_paiement)),
                    'reference' => $paiement->reference_transaction,
                    'mois' => ucfirst($paiement->mois_a_payer),
                ];
            });
    }

    /**
     * Récupérer les paiements par mode de paiement
     */
    private function getPaiementsParMode($mode, $annee)
    {
        return Paiement::with(['inscription.etudiant.personne', 'inscription.classe'])
            ->where('mode_paiement', $mode)
            ->whereYear('date_paiement', $annee)
            ->where('statut', 'valide')
            ->get()
            ->map(function ($paiement) {
                return [
                    'nom' => $paiement->inscription->etudiant->personne->nom,
                    'prenom' => $paiement->inscription->etudiant->personne->prenom,
                    'matricule' => $paiement->inscription->etudiant->matricule,
                    'classe' => $paiement->inscription->classe->libelle,
                    'montant' => $paiement->montant,
                    'date_paiement' => $paiement->date_paiement->format('d/m/Y'),
                    'reference' => $paiement->reference_transaction,
                    'mois' => ucfirst($paiement->mois_a_payer),
                ];
            });
    }

    /**
     * Obtenir le label du statut
     */
    private function getStatutLabel($statut)
    {
        $labels = [
            'valide' => 'Validés',
            'en_attente' => 'En Attente',
            'rejete' => 'Rejetés',
            'annule' => 'Annulés',
        ];
        return $labels[$statut] ?? $statut;
    }

    /**
     * Obtenir le label du mode de paiement
     */
    private function getModeLabel($mode)
    {
        $labels = [
            'espece' => 'Espèce',
            'virement' => 'Virement',
            'wave' => 'Wave',
            'orange_money' => 'Orange Money',
        ];
        return $labels[$mode] ?? $mode;
    }





    /**
 * Afficher la page des rapports financiers
 */
public function rapports(Request $request)
{
    try {
        // Récupération des filtres
        $periode = $request->input('periode', 'mois');
        $mois = $request->input('mois', now()->month);
        $annee = $request->input('annee', now()->year);

        // Récupérer toutes les classes pour le formulaire d'export
        $classes = Classe::orderBy('libelle')->get();

        // Calcul des statistiques selon la période
        $query = Paiement::where('statut', 'valide');

        switch ($periode) {
            case 'mois':
                $query->whereMonth('date_paiement', $mois)
                      ->whereYear('date_paiement', $annee);
                break;
            case 'trimestre':
                $trimestre = ceil($mois / 3);
                $debutTrimestre = ($trimestre - 1) * 3 + 1;
                $finTrimestre = $trimestre * 3;
                $query->whereYear('date_paiement', $annee)
                      ->whereMonth('date_paiement', '>=', $debutTrimestre)
                      ->whereMonth('date_paiement', '<=', $finTrimestre);
                break;
            case 'annee':
                $query->whereYear('date_paiement', $annee);
                break;
        }

        // Statistiques générales
        $statistiques = [
            'revenus_totaux' => $query->sum('montant'),
            'nombre_paiements_valides' => $query->count(),
            'nombre_paiements_attente' => Paiement::where('statut', 'en_attente')->count(),
            'taux_validation' => $this->calculerTauxValidation($annee),
            'revenus_moyens_etudiant' => $this->calculerRevenuMoyen($query)
        ];

        // Évolution temporelle (12 derniers mois)
        $evolutionTemporelle = $this->getEvolutionTemporelle();

        // Analyse par type de frais
        $analyseTypeFrais = $this->getAnalyseTypeFrais($query);

        // Analyse par classe
        $analyseClasses = $this->getAnalyseClasses($query);

        // Analyse par mode de paiement
        $analyseModePaiement = $this->getAnalyseModePaiement($query);

        return view('comptable.rapports.index', compact(
            'classes',
            'periode',
            'mois',
            'annee',
            'statistiques',
            'evolutionTemporelle',
            'analyseTypeFrais',
            'analyseClasses',
            'analyseModePaiement'
        ));

    } catch (Exception $e) {
        return back()->with('error', 'Erreur lors du chargement des rapports: ' . $e->getMessage());
    }
}

/**
 * Calculer le taux de validation
 */
private function calculerTauxValidation($annee)
{
    $total = Paiement::whereYear('date_paiement', $annee)->count();
    $valides = Paiement::where('statut', 'valide')->whereYear('date_paiement', $annee)->count();

    return $total > 0 ? round(($valides / $total) * 100, 1) : 0;
}

/**
 * Calculer le revenu moyen par étudiant
 */
private function calculerRevenuMoyen($query)
{
    $clone = clone $query;
    $nombreEtudiants = $clone->distinct('inscription_id')->count('inscription_id');
    $revenus = $query->sum('montant');

    return $nombreEtudiants > 0 ? round($revenus / $nombreEtudiants) : 0;
}

/**
 * Obtenir l'évolution temporelle (12 derniers mois)
 */
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

/**
 * Analyse par type de frais
 */
private function getAnalyseTypeFrais($query)
{
    $total = $query->sum('montant');

    $types = ['inscription', 'mensualite', 'soutenance'];
    $analyse = [];

    foreach ($types as $type) {
        $clone = clone $query;
        $montant = $clone->where('type_frais', $type)->sum('montant');
        $analyse[$type] = [
            'montant' => $montant,
            'pourcentage' => $total > 0 ? round(($montant / $total) * 100, 1) : 0
        ];
    }

    return $analyse;
}

/**
 * Analyse par classe
 */
private function getAnalyseClasses($query)
{
    return Paiement::with('inscription.classe')
        ->whereIn('id', $query->pluck('id'))
        ->get()
        ->groupBy('inscription.classe_id')
        ->map(function ($paiements) {
            $classe = $paiements->first()->inscription->classe;
            $nombreEtudiants = $paiements->pluck('inscription.etudiant_id')->unique()->count();
            $revenus = $paiements->sum('montant');

            return [
                'classe' => $classe->libelle,
                'revenus' => $revenus,
                'nombre_paiements' => $paiements->count(),
                'nombre_etudiants' => $nombreEtudiants,
                'revenu_moyen_etudiant' => $nombreEtudiants > 0 ? round($revenus / $nombreEtudiants) : 0
            ];
        })
        ->sortByDesc('revenus')
        ->values();
}

/**
 * Analyse par mode de paiement
 */
private function getAnalyseModePaiement($query)
{
    $total = $query->sum('montant');

    return Paiement::whereIn('id', $query->pluck('id'))
        ->selectRaw('mode_paiement, SUM(montant) as montant, COUNT(*) as nombre')
        ->groupBy('mode_paiement')
        ->get()
        ->map(function ($item) use ($total) {
            return [
                'mode' => $this->getModeLabel($item->mode_paiement),
                'montant' => $item->montant,
                'nombre' => $item->nombre,
                'pourcentage' => $total > 0 ? round(($item->montant / $total) * 100, 1) : 0
            ];
        });
}

}
