@extends('layouts.app')

@section('title', 'Paiements en Attente')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Paiements en Attente de Validation</h1>
        <div class="text-sm md:text-base text-gray-600 mt-2">
            <a href="{{ route('comptable.dashboard') }}" class="text-blue-600 hover:text-blue-800">Tableau de bord</a>
            <span class="mx-2">›</span>
            <span>Paiements en attente</span>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">En attente</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $paiements->count() }}</p>
                        <small class="text-gray-500">Paiements à traiter</small>
                    </div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-3xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Montant total</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($paiements->sum('montant'), 0, ',', ' ') }}</p>
                        <small class="text-gray-500">FCFA à valider</small>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-3xl text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Aujourd'hui</p>
                        <p class="text-3xl font-bold text-green-600">{{ $paiements->where('created_at', '>=', today())->count() }}</p>
                        <small class="text-gray-500">Nouveaux paiements</small>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-day text-3xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions en lot -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-tasks text-blue-500 mr-2"></i>
                Actions en lot
            </h3>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        onclick="selectAll()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-check-square mr-1"></i> Tout sélectionner
                </button>
                <button type="button"
                        onclick="validateSelected()"
                        disabled
                        id="validateBtn"
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-check mr-1"></i> Valider sélectionnés
                </button>
                <button type="button"
                        onclick="rejectSelected()"
                        disabled
                        id="rejectBtn"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-times mr-1"></i> Rejeter sélectionnés
                </button>
            </div>
        </div>
    </div>

    <!-- Liste des paiements -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h3 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-list mr-2"></i>
                    <span>Liste des Paiements en Attente</span>
                </h3>
                <select onchange="filterByMode(this.value)"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">Tous les modes</option>
                    <option value="carte_bancaire">Carte bancaire</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="orange_money">Orange Money</option>
                    <option value="virement">Virement</option>
                    <option value="especes">Espèces</option>
                </select>
            </div>
        </div>

        <div class="p-6">
            @if($paiements && $paiements->count() > 0)
                <!-- Version Desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox"
                                           id="selectAllCheckbox"
                                           onchange="toggleSelectAll()"
                                           class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Étudiant</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Inscription</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Référence</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($paiements as $paiement)
                            <tr data-mode="{{ $paiement->mode_paiement }}"
                                data-id="{{ $paiement->id }}"
                                data-payment='@json($paiement)'
                                class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                           class="payment-checkbox w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                                           value="{{ $paiement->id }}"
                                           onchange="updateBulkButtons()">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-900">{{ $paiement->created_at->format('d/m/Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $paiement->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        @if($paiement->inscription && $paiement->inscription->etudiant)
                                            @php
                                                $etudiant = $paiement->inscription->etudiant;
                                                $personne = $etudiant->personne ?? null;
                                                $user = $etudiant->user ?? null;
                                            @endphp

                                            @if($personne)
                                                <span class="font-semibold text-gray-900">
                                                    {{ $personne->nom }} {{ $personne->prenom }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $etudiant->matricule ?? 'N/A' }}</span>
                                            @elseif($user)
                                                <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                                                <span class="text-xs text-gray-500">{{ $etudiant->matricule ?? 'N/A' }}</span>
                                            @else
                                                <span class="text-gray-500">Informations manquantes</span>
                                            @endif
                                        @else
                                            <span class="text-gray-500">Étudiant non trouvé</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-900">{{ $paiement->inscription->classe->libelle ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $paiement->inscription->annee_academique ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-green-600 text-lg">{{ number_format($paiement->montant, 0, ',', ' ') }}</span>
                                    <span class="text-xs text-gray-500">FCFA</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        @switch($paiement->mode_paiement)
                                            @case('carte_bancaire') 💳 Carte @break
                                            @case('mobile_money') 📱 Mobile @break
                                            @case('orange_money') 🟠 Orange @break
                                            @case('virement') 🏦 Virement @break
                                            @case('especes') 💵 Espèces @break
                                            @default {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <code class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">{{ $paiement->reference_transaction }}</code>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                onclick="validatePayment({{ $paiement->id }})"
                                                class="p-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors"
                                                title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button"
                                                onclick="rejectPayment({{ $paiement->id }})"
                                                class="p-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors"
                                                title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button type="button"
                                                onclick="viewDetails({{ $paiement->id }})"
                                                class="p-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                                                title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Version Mobile -->
                <div class="lg:hidden space-y-4">
                    @foreach($paiements as $paiement)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                         data-mode="{{ $paiement->mode_paiement }}"
                         data-id="{{ $paiement->id }}"
                         data-payment='@json($paiement)'>
                        <div class="flex items-start justify-between mb-3">
                            <input type="checkbox"
                                   class="payment-checkbox w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 mt-1"
                                   value="{{ $paiement->id }}"
                                   onchange="updateBulkButtons()">
                            <div class="flex-1 mx-3">
                                @if($paiement->inscription && $paiement->inscription->etudiant)
                                    @php
                                        $etudiant = $paiement->inscription->etudiant;
                                        $personne = $etudiant->personne ?? null;
                                        $user = $etudiant->user ?? null;
                                    @endphp

                                    @if($personne)
                                        <h4 class="font-semibold text-gray-900">
                                            {{ $personne->nom }} {{ $personne->prenom }}
                                        </h4>
                                        <p class="text-sm text-gray-600">{{ $etudiant->matricule ?? 'N/A' }}</p>
                                    @elseif($user)
                                        <h4 class="font-semibold text-gray-900">{{ $user->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $etudiant->matricule ?? 'N/A' }}</p>
                                    @else
                                        <h4 class="text-gray-500">Informations manquantes</h4>
                                    @endif
                                @else
                                    <h4 class="text-gray-500">Étudiant non trouvé</h4>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">{{ $paiement->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="space-y-2 mb-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Classe:</span>
                                <span class="font-medium">{{ $paiement->inscription->classe->libelle ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Montant:</span>
                                <span class="font-bold text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Mode:</span>
                                <span class="text-xs bg-gray-200 px-2 py-1 rounded">
                                    @switch($paiement->mode_paiement)
                                        @case('carte_bancaire') 💳 Carte @break
                                        @case('mobile_money') 📱 Mobile @break
                                        @case('orange_money') 🟠 Orange @break
                                        @case('virement') 🏦 Virement @break
                                        @case('especes') 💵 Espèces @break
                                    @endswitch
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Référence:</span>
                                <code class="text-xs bg-gray-200 px-2 py-1 rounded">{{ $paiement->reference_transaction }}</code>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="button"
                                    onclick="validatePayment({{ $paiement->id }})"
                                    class="flex-1 px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-check mr-1"></i> Valider
                            </button>
                            <button type="button"
                                    onclick="rejectPayment({{ $paiement->id }})"
                                    class="flex-1 px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-times mr-1"></i> Rejeter
                            </button>
                            <button type="button"
                                    onclick="viewDetails({{ $paiement->id }})"
                                    class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-6xl text-green-500"></i>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-700 mb-2">Aucun paiement en attente</h4>
                    <p class="text-gray-600 mb-4">Tous les paiements ont été traités. Excellent travail !</p>
                    <a href="{{ route('comptable.paiements.historique') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                        <i class="fas fa-history mr-2"></i> Voir l'historique
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de détails -->
<div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto animate-modalSlideIn">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex justify-between items-center sticky top-0">
            <h3 class="text-white text-xl font-semibold flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Détails du Paiement
            </h3>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modalBody" class="p-6">
            <!-- Contenu dynamique -->
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row gap-3 justify-end">
            <button onclick="validateFromModal()"
                    class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium">
                <i class="fas fa-check mr-2"></i> Valider
            </button>
            <button onclick="rejectFromModal()"
                    class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium">
                <i class="fas fa-times mr-2"></i> Rejeter
            </button>
            <button onclick="closeModal()"
                    class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium">
                <i class="fas fa-times-circle mr-2"></i> Fermer
            </button>
        </div>
    </div>
</div>

<script>
let selectedPayments = [];
let currentPaymentId = null;

function selectAll() {
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    checkboxes.forEach(checkbox => checkbox.checked = true);
    if (selectAllCheckbox) selectAllCheckbox.checked = true;
    updateBulkButtons();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = selectAllCheckbox.checked);
    updateBulkButtons();
}

function updateBulkButtons() {
    const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
    const validateBtn = document.getElementById('validateBtn');
    const rejectBtn = document.getElementById('rejectBtn');

    const isEnabled = checkedBoxes.length > 0;
    validateBtn.disabled = !isEnabled;
    rejectBtn.disabled = !isEnabled;

    selectedPayments = Array.from(checkedBoxes).map(cb => cb.value);
}

function validateSelected() {
    if (selectedPayments.length === 0) return;
    if (confirm(`Valider ${selectedPayments.length} paiement(s) sélectionné(s) ?`)) {
        console.log('Validation des paiements:', selectedPayments);
        // TODO: Appel AJAX
    }
}

function rejectSelected() {
    if (selectedPayments.length === 0) return;
    if (confirm(`Rejeter ${selectedPayments.length} paiement(s) sélectionné(s) ?`)) {
        console.log('Rejet des paiements:', selectedPayments);
        // TODO: Appel AJAX
    }
}

function validatePayment(id) {
    if (!confirm('Confirmer la validation de ce paiement ?')) return;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/comptable/api/paiements/${id}/valider`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({}),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Paiement validé avec succès ✅');
            location.reload();
        } else {
            alert('Erreur : ' + (data.message || 'Échec de la validation'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur réseau ou serveur.');
    });
}

function rejectPayment(id) {
    if (confirm('Rejeter ce paiement ?')) {
        console.log('Rejet du paiement:', id);
        // TODO: Appel AJAX
    }
}

function viewDetails(id) {
    currentPaymentId = id;
    const row = document.querySelector(`[data-id="${id}"]`);
    const paymentData = JSON.parse(row.getAttribute('data-payment'));

    const modalBody = document.getElementById('modalBody');

    // Récupération sécurisée des données étudiant
    const etudiant = paymentData.inscription?.etudiant;
    const personne = etudiant?.personne;
    const user = personne?.user; // ✅ ici, pas etudiant.user

    // Nom complet avec fallback
    let nomComplet = 'N/A';
    if (personne && personne.nom && personne.prenom) {
        nomComplet = `${personne.nom} ${personne.prenom}`;
    } else if (user && user.name) {
        nomComplet = user.name;
    }

    const matricule = etudiant?.matricule || 'Non défini';
    const classe = paymentData.inscription?.classe?.libelle || 'N/A';
    const anneeAcademique = paymentData.inscription?.annee_academique || 'N/A';

    // Formatage du mode de paiement
    let modeLabel = paymentData.mode_paiement;
    const modesMapping = {
        'carte_bancaire': '💳 Carte bancaire',
        'mobile_money': '📱 Mobile Money',
        'orange_money': '🟠 Orange Money',
        'virement': '🏦 Virement bancaire',
        'especes': '💵 Espèces'
    };
    modeLabel = modesMapping[paymentData.mode_paiement] || paymentData.mode_paiement;

    // Formatage du motif
    const motifsMapping = {
        'inscription': 'Frais d\'inscription',
        'mensualite': 'Frais de mensualité',
        'soutenance': 'Frais de soutenance'
    };
    const motifLabel = motifsMapping[paymentData.motif] || (paymentData.motif || 'Non spécifié');

    modalBody.innerHTML = `
        <div class="space-y-6">
            <!-- Informations Étudiant -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border-l-4 border-blue-500">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    Informations Étudiant
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Nom complet</p>
                        <p class="font-semibold text-gray-900">${nomComplet}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Matricule</p>
                        <p class="font-semibold text-gray-900">${matricule}</p>
                    </div>
                    ${personne?.telephone ? `
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Téléphone</p>
                        <p class="font-semibold text-gray-900">${personne.telephone}</p>
                    </div>
                    ` : ''}
                    ${personne?.adresse ? `
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Adresse</p>
                        <p class="font-semibold text-gray-900">${personne.adresse}</p>
                    </div>
                    ` : ''}
                </div>
            </div>

            <!-- Informations Inscription -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border-l-4 border-green-500">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-graduation-cap text-white"></i>
                    </div>
                    Inscription
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Classe</p>
                        <p class="font-semibold text-gray-900">${classe}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Année académique</p>
                        <p class="font-semibold text-gray-900">${anneeAcademique}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Date d'inscription</p>
                        <p class="font-semibold text-gray-900">${paymentData.inscription?.date_inscription ? new Date(paymentData.inscription.date_inscription).toLocaleDateString('fr-FR') : 'N/A'}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Statut inscription</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold ${
                            paymentData.inscription?.statut === 'validée' ? 'bg-green-100 text-green-800' :
                            paymentData.inscription?.statut === 'en_attente' ? 'bg-yellow-100 text-yellow-800' :
                            'bg-gray-100 text-gray-800'
                        }">
                            ${paymentData.inscription?.statut || 'N/A'}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informations Paiement -->
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 border-l-4 border-yellow-500">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-money-bill-wave text-white"></i>
                    </div>
                    Détails du Paiement
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-3 md:col-span-2">
                        <p class="text-xs text-gray-600 mb-1">Montant</p>
                        <p class="font-bold text-green-600 text-3xl">${new Intl.NumberFormat('fr-FR').format(paymentData.montant)} <span class="text-lg">FCFA</span></p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Mode de paiement</p>
                        <p class="font-semibold text-gray-900">${modeLabel}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Type de frais</p>
                        <p class="font-semibold text-gray-900">${motifLabel}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3 md:col-span-2">
                        <p class="text-xs text-gray-600 mb-1">Référence transaction</p>
                        <code class="bg-gray-200 px-3 py-2 rounded text-sm font-mono block">${paymentData.reference_transaction}</code>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Date de création</p>
                        <p class="font-semibold text-gray-900">${new Date(paymentData.created_at).toLocaleString('fr-FR')}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Statut actuel</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i> En attente de validation
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informations supplémentaires si disponibles -->
            ${paymentData.commentaire ? `
            <div class="bg-gray-50 rounded-lg p-6 border-l-4 border-gray-400">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-comment text-gray-500 mr-2"></i>
                    Commentaire
                </h4>
                <p class="text-gray-700 italic">"${paymentData.commentaire}"</p>
            </div>
            ` : ''}
        </div>
    `;

    document.getElementById('detailsModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
    currentPaymentId = null;
}

function validateFromModal() {
    if (currentPaymentId) {
        validatePayment(currentPaymentId);
        closeModal();
    }
}

function rejectFromModal() {
    if (currentPaymentId) {
        rejectPayment(currentPaymentId);
        closeModal();
    }
}

function filterByMode(mode) {
    const rows = document.querySelectorAll('[data-mode]');
    rows.forEach(row => {
        if (mode === '' || row.dataset.mode === mode) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>

<style>
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-modalSlideIn {
    animation: modalSlideIn 0.3s ease-out;
}
</style>
@endsection
