@extends('layouts.app')

@section('title', 'Gestion des Inscriptions')

@section('content')
<div class="page-header">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Gestion des Inscriptions</h1>
            <div class="breadcrumb text-sm md:text-base mt-1">
                <a href="{{ route('administration.dashboard') }}">Tableau de bord</a> &raquo;
                <span class="text-gray-600">Inscriptions</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            <a href="{{ route('administration.inscriptions.create') }}" 
               class="flex-1 md:flex-initial inline-flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors text-sm">
                <i class="fas fa-user-plus mr-2"></i>
                <span>Inscrire</span>
            </a>
            <a href="{{ route('administration.inscriptions.inscrireMultiple') }}" 
               class="flex-1 md:flex-initial inline-flex items-center justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors text-sm">
                <i class="fas fa-users mr-2"></i>
                <span>Groupée</span>
            </a>
            <a href="{{ route('administration.inscriptions.export') }}" 
               class="flex-1 md:flex-initial inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors text-sm">
                <i class="fas fa-file-export mr-2"></i>
                <span>Exporter</span>
            </a>
        </div>
    </div>
</div>

<!-- Cartes de statistiques -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-white text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-blue-600">{{ $statistiques['total'] ?? 0 }}</span>
            </div>
            <h3 class="text-sm font-semibold text-gray-700">Total</h3>
            <p class="text-xs text-gray-500">Toutes les inscriptions</p>
        </div>
    </div>

    <!-- Validées -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-green-600">{{ $statistiques['valide'] ?? 0 }}</span>
            </div>
            <h3 class="text-sm font-semibold text-gray-700">Validées</h3>
            <p class="text-xs text-gray-500">Inscriptions approuvées</p>
        </div>
    </div>

    <!-- En attente -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-yellow-600">{{ $statistiques['en_attente'] ?? 0 }}</span>
            </div>
            <h3 class="text-sm font-semibold text-gray-700">En attente</h3>
            <p class="text-xs text-gray-500">À valider</p>
        </div>
    </div>

    <!-- Rejetées -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-white text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-red-600">{{ $statistiques['rejete'] ?? 0 }}</span>
            </div>
            <h3 class="text-sm font-semibold text-gray-700">Rejetées</h3>
            <p class="text-xs text-gray-500">Inscriptions rejetées</p>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" action="{{ route('administration.inscriptions.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Statut -->
            <div>
                <label for="statut" class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" id="statut" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                    <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>

            <!-- Classe -->
            <div>
                <label for="classe_id" class="block text-xs font-medium text-gray-700 mb-1">Classe</label>
                <select name="classe_id" id="classe_id" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Année académique -->
            <div>
                <label for="annee_academique" class="block text-xs font-medium text-gray-700 mb-1">Année</label>
                <select name="annee_academique" id="annee_academique" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Toutes les années</option>
                    @foreach($anneesAcademiques as $annee)
                        <option value="{{ $annee }}" {{ request('annee_academique') == $annee ? 'selected' : '' }}>
                            {{ $annee }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Recherche -->
            <div>
                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" id="search" 
                       placeholder="Nom, prénom..." 
                       value="{{ request('search') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Boutons -->
            <div class="flex flex-col gap-2">
                <label class="block text-xs font-medium text-gray-700 mb-1 invisible">Actions</label>
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors text-sm">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'classe_id', 'annee_academique', 'statut']))
                    <a href="{{ route('administration.inscriptions.index') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors text-sm">
                        <i class="fas fa-undo mr-2"></i>
                        Réinitialiser
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Tableau Desktop -->
<div class="hidden lg:block bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Étudiant</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Classe</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Année</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Statut</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($inscriptions as $inscription)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($inscription->etudiant->personne->photo)
                                    <img src="{{ asset('storage/' . $inscription->etudiant->personne->photo) }}"
                                         alt="Photo"
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($inscription->etudiant->personne->prenom, 0, 1) }}{{ substr($inscription->etudiant->personne->nom, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">
                                        {{ $inscription->etudiant->personne->nom }} {{ $inscription->etudiant->personne->prenom }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $inscription->etudiant->matricule }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $inscription->classe->libelle }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $inscription->annee_academique }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($inscription->date_inscription)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if($inscription->statut == 'valide')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Validée
                                </span>
                            @elseif($inscription->statut == 'rejete')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Rejetée
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> En attente
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('administration.inscriptions.show', $inscription) }}"
                                   class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors"
                                   title="Voir">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('administration.inscriptions.edit', $inscription) }}"
                                   class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-gray-500 hover:bg-gray-600 text-white transition-colors"
                                   title="Modifier">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @if($inscription->statut == 'en_attente')
                                    <form action="{{ route('administration.inscriptions.validate', $inscription) }}"
                                          method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors"
                                                title="Valider">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <button type="button"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-orange-500 hover:bg-orange-600 text-white transition-colors"
                                            onclick="openRejectModal({{ $inscription->id }})"
                                            title="Rejeter">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                @endif
                                <form action="{{ route('administration.inscriptions.destroy', $inscription) }}"
                                      method="POST" class="inline-block"
                                      onsubmit="return confirm('Supprimer cette inscription ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors"
                                            title="Supprimer">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-alt text-2xl text-gray-400"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-700 mb-2">Aucune inscription trouvée</h4>
                                <p class="text-sm text-gray-500 mb-4">Aucune inscription ne correspond à vos critères</p>
                                @if(request()->hasAny(['search', 'classe_id', 'annee_academique', 'statut']))
                                    <a href="{{ route('administration.inscriptions.index') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors text-sm">
                                        <i class="fas fa-undo mr-2"></i> Réinitialiser
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($inscriptions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $inscriptions->withQueryString()->links() }}
        </div>
    @endif
</div>

<!-- Cards Mobile/Tablet -->
<div class="lg:hidden space-y-4">
    @forelse($inscriptions as $inscription)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4">
                <!-- Header -->
                <div class="flex items-start gap-3 mb-4">
                    @if($inscription->etudiant->personne->photo)
                        <img src="{{ asset('storage/' . $inscription->etudiant->personne->photo) }}"
                             alt="Photo"
                             class="w-14 h-14 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-14 h-14 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                            {{ substr($inscription->etudiant->personne->prenom, 0, 1) }}{{ substr($inscription->etudiant->personne->nom, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 truncate">
                            {{ $inscription->etudiant->personne->nom }} {{ $inscription->etudiant->personne->prenom }}
                        </h3>
                        <p class="text-sm text-gray-500">{{ $inscription->etudiant->matricule }}</p>
                        <div class="mt-2">
                            @if($inscription->statut == 'valide')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Validée
                                </span>
                            @elseif($inscription->statut == 'rejete')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Rejetée
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> En attente
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs">Classe</p>
                        <p class="font-medium text-gray-900">{{ $inscription->classe->libelle }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Année</p>
                        <p class="font-medium text-gray-900">{{ $inscription->annee_academique }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-500 text-xs">Date d'inscription</p>
                        <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($inscription->date_inscription)->format('d/m/Y') }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('administration.inscriptions.show', $inscription) }}"
                       class="inline-flex items-center justify-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-eye mr-2"></i> Voir
                    </a>
                    <a href="{{ route('administration.inscriptions.edit', $inscription) }}"
                       class="inline-flex items-center justify-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i> Modifier
                    </a>
                    @if($inscription->statut == 'en_attente')
                        <form action="{{ route('administration.inscriptions.validate', $inscription) }}"
                              method="POST" class="col-span-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-check mr-2"></i> Valider
                            </button>
                        </form>
                        <button type="button"
                                class="inline-flex items-center justify-center px-3 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors"
                                onclick="openRejectModal({{ $inscription->id }})">
                            <i class="fas fa-times mr-2"></i> Rejeter
                        </button>
                    @endif
                    <form action="{{ route('administration.inscriptions.destroy', $inscription) }}"
                          method="POST" 
                          class="col-span-2"
                          onsubmit="return confirm('Supprimer cette inscription ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-trash mr-2"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-file-alt text-2xl text-gray-400"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-700 mb-2">Aucune inscription trouvée</h4>
                <p class="text-sm text-gray-500 mb-4 text-center">Aucune inscription ne correspond à vos critères</p>
                @if(request()->hasAny(['search', 'classe_id', 'annee_academique', 'statut']))
                    <a href="{{ route('administration.inscriptions.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors text-sm">
                        <i class="fas fa-undo mr-2"></i> Réinitialiser
                    </a>
                @endif
            </div>
        </div>
    @endforelse

    <!-- Pagination Mobile -->
    @if($inscriptions->hasPages())
        <div class="bg-white rounded-lg shadow-md p-4">
            {{ $inscriptions->withQueryString()->links() }}
        </div>
    @endif
</div>

<!-- Modal de rejet -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Rejeter l'inscription</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="rejectForm" onsubmit="submitReject(event)">
            <div class="p-4">
                <label for="raison_rejet" class="block text-sm font-medium text-gray-700 mb-2">
                    Motif du rejet <span class="text-red-500">*</span>
                </label>
                <textarea id="raison_rejet" 
                          name="raison_rejet" 
                          rows="4" 
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                          placeholder="Expliquez la raison du rejet..."></textarea>
            </div>
            <div class="flex gap-3 p-4 border-t border-gray-200">
                <button type="button" 
                        onclick="closeRejectModal()"
                        class="flex-1 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors">
                    Rejeter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentInscriptionId = null;
let rejectUrl = null;

function openRejectModal(inscriptionId) {
    currentInscriptionId = inscriptionId;
    rejectUrl = "{{ route('administration.inscriptions.reject', ':id') }}".replace(':id', inscriptionId);
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('raison_rejet').value = '';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    currentInscriptionId = null;
    rejectUrl = null;
}

function submitReject(event) {
    event.preventDefault();
    
    const raison = document.getElementById('raison_rejet').value;
    
    fetch(rejectUrl, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ raison_rejet: raison })
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            alert('Erreur lors du rejet de l\'inscription');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors du rejet de l\'inscription');
    });
}

// Fermer le modal en cliquant en dehors
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection