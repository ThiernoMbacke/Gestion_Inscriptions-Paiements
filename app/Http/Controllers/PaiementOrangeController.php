<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; // Ajouté
use Illuminate\Http\Request;
use App\Services\OrangeMoneyService;
use App\Models\Paiement;
use App\Models\Inscription;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaiementOrangeController extends Controller
{
    protected $orangeMoneyService;

    public function __construct(OrangeMoneyService $orangeMoneyService)
    {
        $this->orangeMoneyService = $orangeMoneyService;
    }

    /**
     * Initier un paiement Orange Money
     */
    public function initiate(Request $request)
{
    // Vérifier la configuration Orange Money
    if (!config('services.orange_money.api_key') || !config('services.orange_money.merchant_id')) {
        return response()->json([
            'success' => false,
            'message' => 'Orange Money n\'est pas encore configuré. Veuillez contacter l\'administration.',
            'error' => 'Configuration manquante'
        ], 503);
    }

    $request->validate([
        'inscription_id' => 'required|exists:inscriptions,id',
        'phone_number' => 'required|string|min:9',
        'amount' => 'required|numeric|min:1'
    ]);

    try {
        DB::beginTransaction();

        // Récupérer l'inscription
        $inscription = Inscription::with(['etudiant.personne', 'classe'])
            ->findOrFail($request->inscription_id);

        // Récupérer l'étudiant connecté via la relation hasOneThrough
        /** @var User $user */
             $user = Auth::user();
             $etudiantConnecte = $user->etudiant;

        if (!$etudiantConnecte || $inscription->etudiant_id !== $etudiantConnecte->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé à cette inscription'
            ], 403);
        }

        // Générer un ID de commande unique
        $orderId = 'INS_' . $inscription->id . '_' . time();

        // Créer l'enregistrement de paiement
        $paiement = Paiement::create([
            'inscription_id' => $inscription->id,
            'etudiant_id' => $inscription->etudiant_id,
            'montant' => $request->amount,
            'mode_paiement' => 'orange_money',
            'statut' => 'en_attente',
            'reference_transaction' => $orderId,
            'numero_telephone' => $request->phone_number,
            'date_paiement' => now()
        ]);

        // Description pour Orange Money
        $description = "Inscription " . $inscription->classe->libelle . " - " . $inscription->etudiant->personne->nom;

        // Appel API Orange Money
        $result = $this->orangeMoneyService->initiatePayment(
            $request->amount,
            $request->phone_number,
            $orderId,
            $description
        );

        if ($result['success']) {
            // Mettre à jour le paiement avec les infos API
            $paiement->update([
                'transaction_id_externe' => $result['transaction_id'],
                'payment_token' => $result['payment_token'] ?? null,
                'payment_url' => $result['payment_url'] ?? null
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paiement initié avec succès',
                'payment_url' => $result['payment_url'],
                'payment_id' => $paiement->id,
                'order_id' => $orderId
            ]);
        } else {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], 400);
        }

    } catch (Exception $e) {
        DB::rollback();
        Log::error('Orange Money Payment Initiation Error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'initiation du paiement',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Callback de retour après paiement
     */
    public function callback(Request $request)
    {
        try {
            $orderId = $request->get('order_id');
            $status = $request->get('status');
            $transactionId = $request->get('txnid');

            if (!$orderId) {
                return redirect()->route('etudiant.paiements.index')
                    ->with('error', 'Référence de commande manquante');
            }

            // Trouver le paiement
            $paiement = Paiement::where('reference_transaction', $orderId)->first();

            if (!$paiement) {
                return redirect()->route('etudiant.paiements.index')
                    ->with('error', 'Paiement introuvable');
            }

            // Vérifier le statut auprès d'Orange Money
            if ($transactionId) {
                $statusCheck = $this->orangeMoneyService->checkPaymentStatus($transactionId);

                if ($statusCheck['success']) {
                    $status = $statusCheck['status'];
                }
            }

            // Mettre à jour le statut du paiement
            switch (strtolower($status)) {
                case 'success':
                case 'completed':
                case 'paid':
                    $paiement->update([
                        'statut' => 'en_attente', // En attente de validation comptable
                        'transaction_id_externe' => $transactionId,
                        'date_paiement' => now()
                    ]);

                    return redirect()->route('etudiant.paiements.index')
                        ->with('success', 'Paiement effectué avec succès ! En attente de validation.');

                case 'failed':
                case 'cancelled':
                case 'error':
                    $paiement->update([
                        'statut' => 'échoué',
                        'transaction_id_externe' => $transactionId
                    ]);

                    return redirect()->route('etudiant.paiements.index')
                        ->with('error', 'Le paiement a échoué. Veuillez réessayer.');

                default:
                    return redirect()->route('etudiant.paiements.index')
                        ->with('info', 'Statut du paiement en cours de vérification.');
            }

        } catch (Exception $e) {
            Log::error('Orange Money Callback Error: ' . $e->getMessage());

            return redirect()->route('etudiant.paiements.index')
                ->with('error', 'Erreur lors du traitement du callback');
        }
    }

    /**
     * Webhook pour les notifications Orange Money
     */
    public function webhook(Request $request)
    {
        try {
            // Valider la signature du webhook
            $signature = $request->header('X-Orange-Signature');
            $payload = $request->getContent();

            if (!$this->orangeMoneyService->validateWebhookSignature($payload, $signature)) {
                Log::warning('Invalid Orange Money webhook signature');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $data = $request->json()->all();

            $orderId = $data['order_id'] ?? null;
            $status = $data['status'] ?? null;
            $transactionId = $data['txnid'] ?? null;

            if (!$orderId) {
                return response()->json(['error' => 'Missing order_id'], 400);
            }

            // Trouver et mettre à jour le paiement
            $paiement = Paiement::where('reference_transaction', $orderId)->first();

            if ($paiement) {
                switch (strtolower($status)) {
                    case 'success':
                    case 'completed':
                    case 'paid':
                        $paiement->update([
                            'statut' => 'en_attente', // En attente de validation comptable
                            'transaction_id_externe' => $transactionId,
                            'date_paiement' => now()
                        ]);
                        break;

                    case 'failed':
                    case 'cancelled':
                    case 'error':
                        $paiement->update([
                            'statut' => 'échoué',
                            'transaction_id_externe' => $transactionId
                        ]);
                        break;
                }

                Log::info('Orange Money webhook processed successfully', [
                    'order_id' => $orderId,
                    'status' => $status,
                    'transaction_id' => $transactionId
                ]);
            }

            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            Log::error('Orange Money Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Page d'annulation
     */
    public function cancel(Request $request)
    {
        $orderId = $request->get('order_id');

        if ($orderId) {
            $paiement = Paiement::where('reference_transaction', $orderId)->first();

            if ($paiement) {
                $paiement->update(['statut' => 'annulé']);
            }
        }

        return redirect()->route('etudiant.paiements.index')
            ->with('info', 'Paiement annulé par l\'utilisateur');
    }

    /**
     * Vérifier le statut d'un paiement
     */

public function checkStatus(Request $request, $paiementId)
    {
        try {
            $user = Auth::user();
            $paiement = null;

            // 🔹 Cas 1 : recherche par ID
            if (is_numeric($paiementId)) {
                $paiement = Paiement::findOrFail($paiementId);

                // Vérifier que l'utilisateur est propriétaire
                if ($user->etudiant && $paiement->etudiant_id !== $user->etudiant->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Accès non autorisé'
                    ], 403);
                }
            }
            // 🔹 Cas 2 : recherche par statut (ex: en_attente, valide)
            else {
                $paiement = Paiement::where('statut', $paiementId)->first();

                if (!$paiement) {
                    return response()->json([
                        'success' => false,
                        'message' => "Aucun paiement trouvé avec le statut {$paiementId}"
                    ], 404);
                }
            }

            $wasUpdated = false;
            $externalStatus = null;

            // Vérifier le statut externe si nécessaire
            if ($paiement->transaction_id_externe) {
                $result = $this->checkExternalPaymentStatus($paiement);

                if ($result['success']) {
                    $externalStatus = $result['status'];
                    $newStatus = $this->mapExternalStatusToLocal($result['status'], $paiement->mode_paiement);

                    $oldStatus = $paiement->statut;

                    if ($newStatus !== $oldStatus) {
                        $paiement->update(['statut' => $newStatus]);
                        $wasUpdated = true;
                        $paiement->refresh();
                    }

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'id' => $paiement->id,
                            'status' => $paiement->statut,
                            'external_status' => $externalStatus,
                            'updated' => $wasUpdated,
                            'previous_status' => $wasUpdated ? $oldStatus : null,
                            'transaction_id' => $paiement->transaction_id_externe,
                            'mode_paiement' => $paiement->mode_paiement,
                            'montant' => $paiement->montant,
                            'date_paiement' => $paiement->date_paiement
                        ],
                        'message' => $wasUpdated
                            ? 'Statut vérifié et mis à jour'
                            : 'Statut vérifié, aucune mise à jour nécessaire'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'data' => [
                            'id' => $paiement->id,
                            'status' => $paiement->statut,
                            'external_error' => $result['message'] ?? 'Erreur inconnue'
                        ],
                        'message' => 'Impossible de vérifier le statut externe'
                    ], 502);
                }
            }

            // 🔹 Pas de transaction externe : retourner le statut actuel
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $paiement->id,
                    'status' => $paiement->statut,
                    'updated' => false,
                    'mode_paiement' => $paiement->mode_paiement,
                    'montant' => $paiement->montant,
                    'date_paiement' => $paiement->date_paiement,
                    'has_external_transaction' => false
                ],
                'message' => 'Statut actuel récupéré'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement non trouvé'
            ], 404);

        } catch (Exception $e) {
            Log::error('Erreur lors de la vérification du statut du paiement : ' . $e->getMessage(), [
                'paiement_id' => $paiementId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut',
                'error_code' => 'CHECK_STATUS_ERROR'
            ], 500);
        }
    }


    /**
     * Mapper les statuts Orange Money vers les statuts locaux
     */
    private function mapOrangeStatusToLocal($orangeStatus)
    {
        switch (strtolower($orangeStatus)) {
            case 'success':
            case 'completed':
            case 'paid':
                return 'en_attente'; // En attente de validation comptable
            case 'failed':
            case 'cancelled':
            case 'error':
                return 'échoué';
            case 'pending':
            case 'processing':
                return 'en_cours';
            default:
                return 'en_attente';
        }
    }
}
