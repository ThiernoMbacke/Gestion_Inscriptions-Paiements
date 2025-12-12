@extends('layouts.app')

@section('title', 'Historique des Paiements')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Historique des Paiements</h1>
        <div class="text-sm md:text-base text-gray-600 mt-2">
            <a href="{{ route('comptable.dashboard') }}" class="text-blue-600 hover:text-blue-800">Tableau de bord</a>
            <span class="mx-2">›</span>
            <span>Historique</span>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Validés -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Paiements validés</p>
                        <p class="text-3xl font-bold text-green-600">{{ $paiements->where('statut', 'validé')->count() }}</p>
                        <small class="text-gray-500">Total confirmés</small>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-3xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Revenus totaux</p>
                        <p class="text-xl font-bold text-blue-600">{{ number_format($paiements->where('statut', 'validé')->sum('montant'), 0, ',', ' ') }}</p>
                        <small class="text-gray-500">FCFA encaissés</small>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-3xl text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ce mois -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Ce mois</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $paiements->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                        <small class="text-gray-500">Paiements traités</small>
                    </div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-month text-3xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejetés -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Rejetés</p>
                        <p class="text-3xl font-bold text-red-600">{{ $paiements->where('statut', 'rejeté')->count() }}</p>
                        <small class="text-gray-500">Paiements refusés</small>
                    </div>
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-3xl text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h3 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-filter mr-2"></i>
                    Filtres et recherche
                </h3>
                <button type="button"
                        onclick="resetFilters()"
                        class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-undo mr-1"></i> Réinitialiser
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Période -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                    <select onchange="filterByPeriod(this.value)"
                            id="periodFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Toutes les périodes</option>
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                        <option value="quarter">Ce trimestre</option>
                        <option value="year">Cette année</option>
                    </select>
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select onchange="filterByStatus(this.value)"
                            id="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="valide">Validés</option>
                        <option value="rejete">Rejetés</option>
                        <option value="en_attente">En attente</option>
                    </select>
                </div>

                <!-- Mode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode de paiement</label>
                    <select onchange="filterByMode(this.value)"
                            id="modeFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Tous les modes</option>
                        <option value="carte_bancaire">Carte bancaire</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="orange_money">Orange Money</option>
                        <option value="virement">Virement</option>
                        <option value="especes">Espèces</option>
                    </select>
                </div>

                <!-- Recherche -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input type="text"
                           placeholder="Nom, référence..."
                           onkeyup="searchPayments(this.value)"
                           id="searchInput"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>
        </div>
    </div>

    <!-- Historique -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h3 class="text-white text-lg font-semibold flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Historique Complet
                </h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('comptable.historique.export.excel') }}"
                       class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-sm font-medium">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                    <a href=""
                       class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors text-sm font-medium">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($paiements && $paiements->count() > 0)
                <!-- Version Desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full" id="paymentsTable">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th onclick="sortTable(0)" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer hover:bg-gray-100">
                                    Date <i class="fas fa-sort ml-1 opacity-50"></i>
                                </th>
                                <th onclick="sortTable(1)" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer hover:bg-gray-100">
                                    Étudiant <i class="fas fa-sort ml-1 opacity-50"></i>
                                </th>
                                <th onclick="sortTable(2)" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer hover:bg-gray-100">
                                    Classe <i class="fas fa-sort ml-1 opacity-50"></i>
                                </th>
                                <th onclick="sortTable(3)" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer hover:bg-gray-100">
                                    Montant <i class="fas fa-sort ml-1 opacity-50"></i>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Référence</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Validé par</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($paiements as $paiement)
                            <tr data-status="{{ $paiement->statut }}"
                                data-mode="{{ $paiement->mode_paiement }}"
                                data-date="{{ $paiement->created_at->format('Y-m-d') }}"
                                data-search="{{ strtolower($paiement->inscription->etudiant->personne->nom ?? '') }} {{ strtolower($paiement->inscription->etudiant->personne->prenom ?? '') }} {{ strtolower($paiement->reference_transaction) }}"
                                class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-900">{{ $paiement->created_at->format('d/m/Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $paiement->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-900">
                                            {{ $paiement->inscription->etudiant->personne->nom ?? 'N/A' }}
                                            {{ $paiement->inscription->etudiant->personne->prenom ?? '' }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $paiement->inscription->etudiant->matricule ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $paiement->inscription->classe->libelle ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }}</span>
                                    <span class="text-xs text-gray-500">FCFA</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
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
                                    @switch($paiement->statut)
                                        @case('valide')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Validé
                                            </span>
                                            @break
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
                                    @if($paiement->comptable)
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900">{{ $paiement->comptable->personne->nom ?? 'N/A' }}</span>
                                            <span class="text-xs text-gray-500">{{ $paiement->updated_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                onclick="viewDetails({{ $paiement->id }})"
                                                class="p-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                                                title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button"
                                                onclick="printReceipt({{ $paiement->id }})"
                                                class="p-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-colors"
                                                title="Reçu">
                                            <i class="fas fa-print"></i>
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
                         data-status="{{ $paiement->statut }}"
                         data-mode="{{ $paiement->mode_paiement }}"
                         data-date="{{ $paiement->created_at->format('Y-m-d') }}"
                         data-search="{{ strtolower($paiement->inscription->etudiant->personne->nom ?? '') }} {{ strtolower($paiement->inscription->etudiant->personne->prenom ?? '') }} {{ strtolower($paiement->reference_transaction) }}">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">
                                    {{ $paiement->inscription->etudiant->personne->nom ?? 'N/A' }}
                                    {{ $paiement->inscription->etudiant->personne->prenom ?? '' }}
                                </h4>
                                <p class="text-sm text-gray-600">{{ $paiement->inscription->etudiant->matricule ?? 'N/A' }}</p>
                            </div>
                            @switch($paiement->statut)
                                @case('valide')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Validé
                                    </span>
                                    @break
                                @case('rejete')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Rejeté
                                    </span>
                                    @break
                            @endswitch
                        </div>

                        <div class="space-y-2 mb-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Classe:</span>
                                <span class="font-medium">{{ $paiement->inscription->classe->libelle ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant:</span>
                                <span class="font-bold text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span>{{ $paiement->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Référence:</span>
                                <code class="text-xs bg-gray-200 px-2 py-1 rounded">{{ $paiement->reference_transaction }}</code>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="viewDetails({{ $paiement->id }})"
                                    class="flex-1 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i> Détails
                            </button>
                            <button onclick="printReceipt({{ $paiement->id }})"
                                    class="px-3 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg text-sm">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination Info -->
                <div class="mt-6 text-center text-sm text-gray-600 bg-gray-50 rounded-lg py-3">
                    Affichage de {{ $paiements->count() }} paiement(s)
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-6xl text-gray-300"></i>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-700 mb-2">Aucun paiement dans l'historique</h4>
                    <p class="text-gray-600">L'historique des paiements apparaîtra ici une fois que des paiements auront été traités.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex justify-between items-center sticky top-0">
            <h3 class="text-white text-xl font-semibold flex items-center">
                <i class="fas fa-eye mr-2"></i>
                Détails du Paiement
            </h3>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modalBody" class="p-6">
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-blue-500 text-4xl mb-3"></i>
                <p class="text-gray-600">Chargement...</p>
            </div>
        </div>
    </div>
</div>

<script>
let sortDirection = {};

function filterByPeriod(period) {
    const rows = document.querySelectorAll('[data-date]');
    const today = new Date();

    rows.forEach(row => {
        const rowDate = new Date(row.dataset.date);
        let show = true;

        switch(period) {
            case 'today':
                show = rowDate.toDateString() === today.toDateString();
                break;
            case 'week':
                const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                show = rowDate >= weekAgo;
                break;
            case 'month':
                show = rowDate.getMonth() === today.getMonth() && rowDate.getFullYear() === today.getFullYear();
                break;
            case 'quarter':
                const quarter = Math.floor(today.getMonth() / 3);
                const rowQuarter = Math.floor(rowDate.getMonth() / 3);
                show = rowQuarter === quarter && rowDate.getFullYear() === today.getFullYear();
                break;
            case 'year':
                show = rowDate.getFullYear() === today.getFullYear();
                break;
            default:
                show = true;
        }

        row.style.display = show ? '' : 'none';
    });
}

function filterByStatus(status) {
    const rows = document.querySelectorAll('[data-status]');
    rows.forEach(row => {
        row.style.display = (status === '' || row.dataset.status === status) ? '' : 'none';
    });
}

function filterByMode(mode) {
    const rows = document.querySelectorAll('[data-mode]');
    rows.forEach(row => {
        row.style.display = (mode === '' || row.dataset.mode === mode) ? '' : 'none';
    });
}

function searchPayments(query) {
    const rows = document.querySelectorAll('[data-search]');
    const searchTerm = query.toLowerCase();
    rows.forEach(row => {
        row.style.display = (searchTerm === '' || row.dataset.search.includes(searchTerm)) ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('periodFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('modeFilter').value = '';
    document.getElementById('searchInput').value = '';
    document.querySelectorAll('tbody tr, [data-status]').forEach(row => row.style.display = '');
}

function sortTable(columnIndex) {
    const table = document.getElementById('paymentsTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const isAscending = sortDirection[columnIndex] !== 'asc';

    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
    });

    const tbody = table.querySelector('tbody');
    rows.forEach(row => tbody.appendChild(row));
    sortDirection[columnIndex] = isAscending ? 'asc' : 'desc';
}

function viewDetails(id) {
    document.getElementById('detailsModal').classList.remove('hidden');
    fetch(`/comptable/paiements/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) displayPaiementDetails(data.paiement);
        });
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

function displayPaiementDetails(paiement) {
    document.getElementById('modalBody').innerHTML = `
        <div class="space-y-6">
            <div class="bg-blue-50 rounded-lg p-6">
                <h4 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-money-bill-wave text-blue-500 mr-2"></i>
                    Informations Paiement
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Montant</p>
                        <p class="font-bold text-green-600 text-2xl">${new Intl.NumberFormat('fr-FR').format(paiement.montant)} FCFA</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Référence</p>
                        <code class="bg-gray-200 px-2 py-1 rounded text-sm">${paiement.reference_transaction}</code>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button onclick="printReceipt(${paiement.id})" class="px-6 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg">
                    <i class="fas fa-print mr-2"></i> Imprimer
                </button>
                <button onclick="closeModal()" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                    Fermer
                </button>
            </div>
        </div>
    `;
}

function printReceipt(id) {
    window.open(`/paiements/${id}/recu`, '_blank');
}

window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>
@endsection
