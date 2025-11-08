@extends('layouts.app')

@section('title', 'Tableau de bord Comptable')

@section('content')
<!-- Header Dashboard -->
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">Tableau de bord Comptable</h1>
    <div class="bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
        <p class="text-green-900 flex items-center">
            <i class="fas fa-calculator mr-2 text-green-600"></i>
            Bonjour <strong class="mx-1">{{ Auth::user()->name }}</strong>, bienvenue sur votre espace comptable.
        </p>
    </div>
</div>

<!-- Alert Success -->
@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
        <div class="flex-1">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            @if(session('recu_url'))
                <a href="{{ session('recu_url') }}" target="_blank"
                   class="inline-flex items-center mt-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Télécharger le reçu
                </a>
            @endif
        </div>
    </div>
@endif

<!-- Statistiques des paiements -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Paiements en attente -->
    <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <div class="p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-clock text-3xl"></i>
                </div>
                <div class="text-right">
                    <p class="text-yellow-100 text-sm font-medium">À traiter</p>
                    <p class="text-4xl font-bold">{{ $paiementsEnAttente ?? 0 }}</p>
                </div>
            </div>
            <h3 class="text-lg font-semibold mb-1">En attente</h3>
            <a href="{{ route('comptable.paiements.en_attente') }}"
               class="text-yellow-100 text-sm hover:text-white inline-flex items-center mt-2">
                Traiter
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Paiements validés -->
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <div class="p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
                <div class="text-right">
                    <p class="text-green-100 text-sm font-medium">Ce mois</p>
                    <p class="text-4xl font-bold">{{ $paiementsValides ?? 0 }}</p>
                </div>
            </div>
            <h3 class="text-lg font-semibold mb-1">Validés</h3>
            <a href="{{ route('comptable.paiements.historique') }}"
               class="text-green-100 text-sm hover:text-white inline-flex items-center mt-2">
                Voir
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Montant total -->
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <div class="p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-money-bill-wave text-3xl"></i>
                </div>
            </div>
            <h3 class="text-lg font-semibold mb-1">Montant total</h3>
            <p class="text-3xl font-bold mb-1">{{ number_format($montantTotal ?? 0, 0, ',', ' ') }}</p>
            <p class="text-blue-100 text-sm">FCFA - Revenus du mois</p>
        </div>
    </div>

    <!-- Paiements rejetés -->
    <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <div class="p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <div class="text-right">
                    <p class="text-red-100 text-sm font-medium">Attention</p>
                    <p class="text-4xl font-bold">{{ $paiementsRejetes ?? 0 }}</p>
                </div>
            </div>
            <h3 class="text-lg font-semibold mb-1">Rejetés</h3>
            <p class="text-red-100 text-sm">Nécessitent attention</p>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-5 flex items-center">
        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-bolt text-white"></i>
        </div>
        Actions rapides
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Valider paiements -->
        <a href="{{ route('comptable.paiements.en_attente') }}"
           class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border-2 border-transparent hover:border-yellow-500">
            <div class="p-6">
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-tasks text-3xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-yellow-600 transition-colors">
                    Valider paiements
                </h3>
                <p class="text-sm text-gray-600">Traiter les paiements en attente</p>
            </div>
        </a>

        <!-- Historique -->
        <a href="{{ route('comptable.paiements.historique') }}"
           class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border-2 border-transparent hover:border-purple-500">
            <div class="p-6">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-history text-3xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-purple-600 transition-colors">
                    Historique
                </h3>
                <p class="text-sm text-gray-600">Consulter les paiements passés</p>
            </div>
        </a>

        <!-- Rapports -->
        <a href="{{ route('comptable.rapports.index') }}"
           class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border-2 border-transparent hover:border-blue-500">
            <div class="p-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-chart-bar text-3xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                    Rapports
                </h3>
                <p class="text-sm text-gray-600">Générer des rapports financiers</p>
            </div>
        </a>

        <!-- Export -->
        <a href="#"
           class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border-2 border-transparent hover:border-green-500">
            <div class="p-6">
                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-download text-3xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-green-600 transition-colors">
                    Export
                </h3>
                <p class="text-sm text-gray-600">Exporter les données comptables</p>
            </div>
        </a>
    </div>
</div>

<!-- Paiements récents -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
        <h3 class="text-white text-xl font-bold flex items-center">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-clock"></i>
            </div>
            Paiements récents nécessitant validation
        </h3>
    </div>

    @if(isset($paiementsRecents) && $paiementsRecents->count() > 0)
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Étudiant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Montant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Mode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($paiementsRecents as $paiement)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $paiement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $paiement->etudiant->personne->nom ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-green-600">
                                {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> En attente
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <form id="valider-form-{{ $paiement->id }}"
                                      action="{{ route('comptable.api.paiements.valider', $paiement) }}"
                                      method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors">
                                        <i class="fas fa-check mr-2"></i>
                                        Valider
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden divide-y divide-gray-200">
            @foreach($paiementsRecents as $paiement)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $paiement->etudiant->personne->nom ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $paiement->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i> En attente
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
                        <div>
                            <span class="text-gray-500">Montant:</span>
                            <p class="font-bold text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Mode:</span>
                            <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</p>
                        </div>
                    </div>

                    <form id="valider-form-{{ $paiement->id }}"
                          action="{{ route('comptable.api.paiements.valider', $paiement) }}"
                          method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-check mr-2"></i>
                            Valider le paiement
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-4xl text-green-500"></i>
            </div>
            <p class="text-gray-500 font-medium">Aucun paiement en attente</p>
            <p class="text-gray-400 text-sm mt-1">Tous les paiements sont à jour !</p>
        </div>
    @endif
</div>

<!-- Script de validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer tous les formulaires de validation
    document.querySelectorAll('[id^="valider-form-"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const button = this.querySelector('button[type="submit"]');
            const originalContent = button.innerHTML;

            // Désactiver le bouton et afficher le chargement
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Validation...';

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher un message de succès
                    const successMessage = document.createElement('div');
                    successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in';
                    successMessage.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span>${data.message}</span>
                        </div>
                    `;
                    document.body.appendChild(successMessage);

                    // Rediriger après 2 secondes
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 2000);
                } else {
                    alert('Erreur: ' + data.message);
                    button.disabled = false;
                    button.innerHTML = originalContent;
                }
            })
            .catch(error => {
                alert('Erreur de communication avec le serveur');
                button.disabled = false;
                button.innerHTML = originalContent;
            });
        });
    });
});
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection
