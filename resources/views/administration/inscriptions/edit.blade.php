@extends('layouts.app')

@section('title', 'Modifier une inscription')

@section('content')
<div class="page-header">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Modifier l'inscription</h1>
    <div class="breadcrumb text-sm md:text-base">
        <a href="{{ route('administration.dashboard') }}">Tableau de bord</a> &raquo;
        <a href="{{ route('administration.inscriptions.index') }}">Inscriptions</a> &raquo;
        <span class="text-gray-600">Modifier</span>
    </div>
</div>

<!-- Alerts -->
@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
        <div class="flex-1">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800 mb-2">Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('administration.inscriptions.update', $inscription) }}" 
      method="POST" 
      id="editInscriptionForm"
      class="max-w-4xl mx-auto">
    @csrf
    @method('PUT')

    <!-- Informations de l'étudiant (lecture seule) -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 md:p-6 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                @if($inscription->etudiant->personne->photo)
                    <img src="{{ asset('storage/' . $inscription->etudiant->personne->photo) }}"
                         alt="Photo"
                         class="w-16 h-16 rounded-full object-cover">
                @else
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        {{ substr($inscription->etudiant->personne->prenom, 0, 1) }}{{ substr($inscription->etudiant->personne->nom, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-blue-900">
                    {{ $inscription->etudiant->personne->nom }} {{ $inscription->etudiant->personne->prenom }}
                </h3>
                <p class="text-sm text-blue-700">Matricule : {{ $inscription->etudiant->matricule }}</p>
                <p class="text-sm text-blue-700">Email : {{ $inscription->etudiant->personne->email }}</p>
            </div>
        </div>
    </div>

    <!-- Formulaire principal -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-4 md:px-6 py-4">
            <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                <i class="fas fa-edit mr-2"></i>
                <span>Informations de l'inscription</span>
            </h3>
        </div>

        <div class="p-4 md:p-6 lg:p-8 space-y-6">
            <!-- Étudiant (désactivé car ne doit pas changer) -->
            <div>
                <label for="etudiant_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Étudiant <span class="text-red-500">*</span>
                </label>
                <select name="etudiant_id" 
                        id="etudiant_id" 
                        disabled
                        class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed text-base">
                    <option value="{{ $inscription->etudiant_id }}" selected>
                        {{ $inscription->etudiant->personne->nom }} {{ $inscription->etudiant->personne->prenom }} ({{ $inscription->etudiant->matricule }})
                    </option>
                </select>
                <input type="hidden" name="etudiant_id" value="{{ $inscription->etudiant_id }}">
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    L'étudiant ne peut pas être modifié. Créez une nouvelle inscription si nécessaire.
                </p>
            </div>

            <!-- Grille responsive pour Classe et Année -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Classe -->
                <div>
                    <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Classe <span class="text-red-500">*</span>
                    </label>
                    <select name="classe_id" 
                            id="classe_id" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all text-base @error('classe_id') border-red-500 @enderror">
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id', $inscription->classe_id) == $classe->id ? 'selected' : '' }}>
                                {{ $classe->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('classe_id')
                        <small class="text-red-500 text-sm mt-1 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <!-- Année académique -->
                <div>
                    <label for="annee_academique" class="block text-sm font-medium text-gray-700 mb-2">
                        Année académique <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="annee_academique" 
                           id="annee_academique" 
                           required
                           pattern="\d{4}-\d{4}"
                           placeholder="2024-2025"
                           value="{{ old('annee_academique', $inscription->annee_academique) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all text-base @error('annee_academique') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format : AAAA-AAAA (ex: 2024-2025)</p>
                    @error('annee_academique')
                        <small class="text-red-500 text-sm mt-1 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>

            <!-- Grille responsive pour Date et Statut -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date d'inscription -->
                <div>
                    <label for="date_inscription" class="block text-sm font-medium text-gray-700 mb-2">
                        Date d'inscription <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="date_inscription"
                           id="date_inscription"
                           required
                           value="{{ old('date_inscription', is_string($inscription->date_inscription) ? $inscription->date_inscription : $inscription->date_inscription->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all text-base @error('date_inscription') border-red-500 @enderror">
                    @error('date_inscription')
                        <small class="text-red-500 text-sm mt-1 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select name="statut" 
                            id="statut" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all text-base @error('statut') border-red-500 @enderror">
                        <option value="en_attente" {{ old('statut', $inscription->statut) == 'en_attente' ? 'selected' : '' }}>
                            <i class="fas fa-clock"></i> En attente
                        </option>
                        <option value="valide" {{ old('statut', $inscription->statut) == 'valide' ? 'selected' : '' }}>
                            <i class="fas fa-check-circle"></i> Validée
                        </option>
                        <option value="rejete" {{ old('statut', $inscription->statut) == 'rejete' ? 'selected' : '' }}>
                            <i class="fas fa-times-circle"></i> Rejetée
                        </option>
                    </select>
                    @error('statut')
                        <small class="text-red-500 text-sm mt-1 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>

            <!-- Raison de rejet (si statut = rejeté) -->
            <div id="raison_rejet_container" class="hidden">
                <label for="raison_rejet" class="block text-sm font-medium text-gray-700 mb-2">
                    Raison du rejet
                </label>
                <textarea name="raison_rejet" 
                          id="raison_rejet" 
                          rows="3"
                          placeholder="Expliquez pourquoi cette inscription est rejetée..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all text-base resize-y">{{ old('raison_rejet', $inscription->raison_rejet) }}</textarea>
            </div>

            <!-- Informations de validation -->
            @if($inscription->administration_id && $inscription->administration)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-user-shield text-gray-500 mr-3 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">Validée par</p>
                            <p class="text-sm text-gray-600">
                                {{ $inscription->administration->personne->nom }} {{ $inscription->administration->personne->prenom }}
                            </p>
                            @if($inscription->date_validation)
                                <p class="text-xs text-gray-500 mt-1">
                                    Le {{ \Carbon\Carbon::parse($inscription->date_validation)->format('d/m/Y à H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col-reverse sm:flex-row gap-3 justify-between">
        <a href="{{ route('administration.inscriptions.index') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors text-center">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Retour à la liste</span>
        </a>
        <button type="submit" 
                id="submitBtn"
                class="inline-flex items-center justify-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-save mr-2"></i>
            <span>Enregistrer les modifications</span>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statutSelect = document.getElementById('statut');
    const raisonContainer = document.getElementById('raison_rejet_container');
    const raisonTextarea = document.getElementById('raison_rejet');

    // Fonction pour afficher/masquer le champ raison de rejet
    function toggleRaisonRejet() {
        if (statutSelect.value === 'rejete') {
            raisonContainer.classList.remove('hidden');
            raisonTextarea.setAttribute('required', 'required');
        } else {
            raisonContainer.classList.add('hidden');
            raisonTextarea.removeAttribute('required');
        }
    }

    // Vérifier au chargement
    toggleRaisonRejet();

    // Vérifier au changement
    statutSelect.addEventListener('change', toggleRaisonRejet);

    // Confirmation avant soumission
    const form = document.getElementById('editInscriptionForm');
    form.addEventListener('submit', function(e) {
        const etudiant = '{{ $inscription->etudiant->personne->nom }} {{ $inscription->etudiant->personne->prenom }}';
        const classe = document.getElementById('classe_id').options[document.getElementById('classe_id').selectedIndex].text;
        const statut = document.getElementById('statut').options[document.getElementById('statut').selectedIndex].text;
        
        const message = `Confirmer la modification de l'inscription ?\n\nÉtudiant : ${etudiant}\nClasse : ${classe}\nStatut : ${statut}`;
        
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
        
        // Désactiver le bouton pour éviter double soumission
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Enregistrement...';
        
        return true;
    });
});
</script>
@endsection