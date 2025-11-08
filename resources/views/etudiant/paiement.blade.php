@extends('layouts.app')

@section('title', 'Mes Paiements')

@push('styles')
<script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Mes Paiements</h1>
        <div class="text-sm md:text-base text-gray-600 mt-2">
            <a href="{{ route('etudiant.dashboard') }}" class="text-blue-600 hover:text-blue-800">Tableau de bord</a>
            <span class="mx-2">›</span>
            <span>Paiements</span>
        </div>
    </div>

    <!-- Statistiques Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Paiements validés -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Paiements validés</p>
                        <p class="text-3xl font-bold text-green-600">{{ $paiements->where('statut', 'valide')->count() }}</p>
                        <small class="text-gray-500">Confirmés</small>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-3xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- En attente -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">En attente</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $paiements->where('statut', 'en_attente')->count() }}</p>
                        <small class="text-gray-500">En traitement</small>
                    </div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-3xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Montant total -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Montant total</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($paiements->sum('montant'), 0, ',', ' ') }}</p>
                        <small class="text-gray-500">FCFA</small>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-3xl text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nouveau Paiement -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                <i class="fas fa-plus-circle mr-2"></i>
                <span>Effectuer un nouveau paiement</span>
            </h3>
        </div>

        <div class="p-6">
            @if($inscriptions->count() > 0)
                <div class="space-y-6">
                    @foreach($inscriptions as $inscription)
                    <div class="bg-gray-50 rounded-lg border-2 border-gray-200 p-6 hover:border-blue-500 transition-colors">
                        <h4 class="text-xl font-bold text-blue-600 mb-4 flex items-center">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            {{ $inscription->classe->libelle ?? 'Classe inconnue' }} - {{ $inscription->annee_academique ?? '' }}
                        </h4>

                        <form id="form_{{ $inscription->id }}" method="POST" action="{{ route('paiement.orange.initiate') }}" novalidate>
                            @csrf
                            <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <!-- Type de frais -->
                                <div>
                                    <label for="type_frais_{{ $inscription->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        Type de frais <span class="text-red-500">*</span>
                                    </label>
                                    <select name="type_frais"
                                            id="type_frais_{{ $inscription->id }}"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">-- Choisissez --</option>
                                        <option value="inscription">Frais d'inscription</option>
                                        <option value="mensualite">Frais de mensualité</option>
                                        <option value="soutenance">Frais de soutenance</option>
                                    </select>
                                </div>

                                <!-- Montant -->
                                <div>
                                    <label for="montant_{{ $inscription->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        Montant (FCFA)
                                    </label>
                                    <input type="number"
                                           name="amount"
                                           id="montant_{{ $inscription->id }}"
                                           readonly
                                           placeholder="Montant calculé"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>

                                <!-- Mode de paiement -->
                                <div>
                                    <label for="mode_paiement_{{ $inscription->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        Mode de paiement <span class="text-red-500">*</span>
                                    </label>
                                    <select name="mode_paiement"
                                            id="mode_paiement_{{ $inscription->id }}"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="" disabled selected>-- Choisir --</option>
                                        <option value="carte_bancaire">💳 Carte bancaire</option>
                                        <option value="orange_money">📱 Orange Money</option>
                                        <option value="virement">🏦 Virement bancaire</option>
                                        <option value="especes">💵 Espèces</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Orange Money Section -->
                            <div id="orangeMoneySection_{{ $inscription->id }}" class="hidden mt-4">
                                <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-4">
                                    <label for="phone_number_{{ $inscription->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-mobile-alt"></i> Numéro Orange Money
                                    </label>
                                    <input type="text"
                                           name="phone_number"
                                           id="phone_number_{{ $inscription->id }}"
                                           placeholder="7XXXXXXXX"
                                           pattern="7[0-9]{8}"
                                           disabled
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <small class="text-gray-600 mt-1 block">Format: 7 suivi de 8 chiffres</small>
                                </div>
                            </div>

                            <!-- Stripe Section -->
                            <div id="stripeSection_{{ $inscription->id }}" class="hidden mt-4">
                                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                                    <h5 class="text-lg font-semibold text-blue-800 mb-3">
                                        <i class="fas fa-credit-card"></i> Informations de la carte bancaire
                                    </h5>
                                    <div id="card-element-{{ $inscription->id }}" class="bg-white p-4 rounded-lg border border-gray-300"></div>
                                    <div id="card-errors-{{ $inscription->id }}" class="text-red-600 text-sm mt-2" role="alert"></div>
                                    <div class="mt-3 space-y-1 text-sm text-gray-600">
                                        <p><i class="fas fa-shield-alt text-blue-500"></i> Paiement sécurisé par Stripe</p>
                                        <p><i class="fas fa-lock text-blue-500"></i> Vos données ne sont pas stockées</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton Submit -->
                            <div class="mt-6">
                                <button type="submit"
                                        id="submitBtn_{{ $inscription->id }}"
                                        class="w-full md:w-auto px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Payer cette inscription
                                </button>
                            </div>
                        </form>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <i class="fas fa-info-circle text-yellow-500 text-4xl mb-3"></i>
                    <p class="text-yellow-800 font-medium">Votre inscription est encore <strong>en attente</strong>.</p>
                    <p class="text-yellow-700 text-sm mt-2">Vous pourrez effectuer un paiement une fois validée par l'administration.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Historique des paiements -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    <span>Historique de mes paiements</span>
                </h3>
                @if($paiements->count() > 0)
                    <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-medium text-white">
                        {{ $paiements->count() }} paiement(s)
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6">
            @if($paiements->count() > 0)
                <!-- Version Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Inscription</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Motif</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Référence</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($paiements as $paiement)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $paiement->inscription->classe->libelle ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                    {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ ucfirst($paiement->motif) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 font-mono">
                                    {{ $paiement->reference_transaction }}
                                </td>
                                <td class="px-4 py-3">
                                    @switch($paiement->statut)
                                        @case('validé')
                                        @case('valide')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Validé
                                            </span>
                                            @break
                                        @case('rejeté')
                                        @case('rejete')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i> Rejeté
                                            </span>
                                            @break
                                        @case('en_attente')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i> En attente
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewPaiementDetails({{ $paiement->id }})"
                                                class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(in_array($paiement->statut, ['validé', 'valide']))
                                            <a href="{{ route('etudiant.paiements.recu', $paiement->id) }}"
                                               class="p-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                                               title="Télécharger reçu"
                                               target="_blank">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Version Mobile -->
                <div class="md:hidden space-y-4">
                    @foreach($paiements as $paiement)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $paiement->inscription->classe->libelle ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">{{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            @switch($paiement->statut)
                                @case('validé')
                                @case('valide')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Validé
                                    </span>
                                    @break
                                @case('rejeté')
                                @case('rejete')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Rejeté
                                    </span>
                                    @break
                                @case('en_attente')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> En attente
                                    </span>
                                    @break
                            @endswitch
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant:</span>
                                <span class="font-semibold">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Mode:</span>
                                <span>{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Référence:</span>
                                <span class="font-mono text-xs">{{ $paiement->reference_transaction }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <button onclick="viewPaiementDetails({{ $paiement->id }})"
                                    class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                <i class="fas fa-eye mr-1"></i> Détails
                            </button>
                            @if(in_array($paiement->statut, ['validé', 'valide']))
                                <a href="{{ route('etudiant.paiements.recu', $paiement->id) }}"
                                   class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm text-center"
                                   target="_blank">
                                    <i class="fas fa-download mr-1"></i> Reçu
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-receipt text-gray-300 text-6xl mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-700 mb-2">Aucun paiement trouvé</h4>
                    <p class="text-gray-600">Vous n'avez encore effectué aucun paiement.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de détails (votre code existant conservé) -->
<div id="paiementDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex justify-between items-center sticky top-0">
            <h3 class="text-white text-xl font-semibold flex items-center">
                <i class="fas fa-receipt mr-2"></i>
                Détails de mon Paiement
            </h3>
            <button onclick="closePaiementModal()" class="text-white hover:text-gray-200 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="paiementModalBody" class="p-6">
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-blue-500 text-4xl mb-3"></i>
                <p class="text-gray-600">Chargement...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Votre code JavaScript existant (stripe, orange money, etc.)
const stripe = Stripe('{{ env("STRIPE_KEY") }}');
const stripeElements = {};
const cardElements = {};

document.addEventListener('DOMContentLoaded', function() {
    @foreach($inscriptions as $inscription)
    initializeInscriptionForm{{ $inscription->id }}();
    @endforeach
});

@foreach($inscriptions as $inscription)
function initializeInscriptionForm{{ $inscription->id }}() {
    const modePaiement = document.getElementById('mode_paiement_{{ $inscription->id }}');
    const orangeSection = document.getElementById('orangeMoneySection_{{ $inscription->id }}');
    const stripeSection = document.getElementById('stripeSection_{{ $inscription->id }}');
    const orangePhone = document.getElementById('phone_number_{{ $inscription->id }}');
    const form = document.getElementById('form_{{ $inscription->id }}');

    // Initialiser Stripe
    stripeElements['{{ $inscription->id }}'] = stripe.elements();
    const cardElement = stripeElements['{{ $inscription->id }}'].create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': { color: '#aab7c4' }
            }
        }
    });

    modePaiement.addEventListener('change', function() {
        orangeSection.classList.add('hidden');
        stripeSection.classList.add('hidden');
        orangePhone.disabled = true;

        if (this.value === 'orange_money') {
            orangeSection.classList.remove('hidden');
            orangePhone.disabled = false;
        } else if (this.value === 'carte_bancaire' || this.value === 'virement') {
            stripeSection.classList.remove('hidden');
            if (!cardElements['{{ $inscription->id }}']) {
                cardElement.mount('#card-element-{{ $inscription->id }}');
                cardElements['{{ $inscription->id }}'] = cardElement;
            }
        }
    });

    @if(isset($fraisParClasse[$inscription->classe->id]))
    const fraisClasse = {!! json_encode($fraisParClasse[$inscription->classe->id]) !!};
    document.getElementById('type_frais_{{ $inscription->id }}').addEventListener('change', function() {
        document.getElementById('montant_{{ $inscription->id }}').value = fraisClasse[this.value] || 0;
    });
    @endif
}
@endforeach

function viewPaiementDetails(id) {
    const modal = document.getElementById('paiementDetailsModal');
    modal.classList.remove('hidden');

    fetch(`/etudiant/paiements/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayEtudiantPaiementDetails(data.paiement);
            }
        });
}

function closePaiementModal() {
    document.getElementById('paiementDetailsModal').classList.add('hidden');
}

function displayEtudiantPaiementDetails(paiement) {
    // Votre code d'affichage existant adapté avec Tailwind
    document.getElementById('paiementModalBody').innerHTML = `
        <div class="space-y-6">
            <!-- Infos paiement -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                    Informations du Paiement
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Référence</p>
                        <p class="font-semibold">${paiement.reference_transaction}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Montant</p>
                        <p class="font-semibold text-green-600 text-xl">${new Intl.NumberFormat('fr-FR').format(paiement.montant)} FCFA</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end">
                ${(paiement.statut === 'validé' || paiement.statut === 'valide') ? `
                <a href="/etudiant/paiements/${paiement.id}/recu" target="_blank"
                   class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-download mr-2"></i> Télécharger le reçu
                </a>
                ` : ''}
                <button onclick="closePaiementModal()"
                        class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Fermer
                </button>
            </div>
        </div>
    `;
}
</script>
@endpush
@endsection
