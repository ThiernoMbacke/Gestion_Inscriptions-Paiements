@extends('layouts.app')

@section('title', 'Inscription groupée')

@section('content')
<div class="page-header">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Inscription Groupée</h1>
    <div class="breadcrumb text-sm md:text-base">
        <a href="{{ route('administration.dashboard') }}">Tableau de bord</a> &raquo;
        <a href="{{ route('administration.inscriptions.index') }}">Inscriptions</a> &raquo;
        <span class="text-gray-600">Inscription groupée</span>
    </div>
</div>

<!-- Alerts -->
@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
        <div class="flex-1">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if(session('warnings'))
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-0.5 flex-shrink-0"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-yellow-800 mb-2">Attention</p>
                <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                    @foreach(session('warnings') as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5 flex-shrink-0"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-800 mb-2">Erreurs lors de la soumission</p>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('administration.inscriptions.storeMultiple') }}"
      method="POST"
      id="inscriptionMultipleForm"
      class="max-w-5xl mx-auto">
    @csrf

    <!-- Formulaire principal -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 md:px-6 py-4">
            <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                <i class="fas fa-users mr-2"></i>
                <span>Paramètres de l'inscription groupée</span>
            </h3>
        </div>

        <div class="p-4 md:p-6 lg:p-8 space-y-6">
            <!-- Classe -->
            <div>
                <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Classe <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="classe_id"
                            id="classe_id"
                            required
                            class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-base appearance-none @error('classe_id') border-red-500 @enderror">
                        <option value="" disabled selected>-- Sélectionner une classe --</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->libelle }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                @error('classe_id')
                    <small class="text-red-500 text-sm mt-1 block flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </small>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Tous les étudiants seront inscrits dans cette classe
                </p>
            </div>

            <!-- Grille responsive pour Année et Date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Année académique -->
                <div>
                    <label for="annee_academique" class="block text-sm font-medium text-gray-700 mb-2">
                        Année académique <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text"
                               name="annee_academique"
                               id="annee_academique"
                               required
                               pattern="\d{4}-\d{4}"
                               placeholder="2024-2025"
                               value="{{ old('annee_academique', date('Y').'-'.(date('Y')+1)) }}"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-base @error('annee_academique') border-red-500 @enderror">
                        <button type="button"
                                id="anneeSuivante"
                                class="px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors flex items-center justify-center"
                                title="Année suivante">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Format : AAAA-AAAA</p>
                    @error('annee_academique')
                        <small class="text-red-500 text-sm mt-1 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <!-- Date d'inscription -->
                <div>
                    <label for="date_inscription" class="block text-sm font-medium text-gray-700 mb-2">
                        Date d'inscription <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="date_inscription"
                           id="date_inscription"
                           required
                           value="{{ old('date_inscription', date('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-base @error('date_inscription') border-red-500 @enderror">
                    @error('date_inscription')
                        <small class="text-red-500 text-sm mt-1 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Sélection des étudiants -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 md:px-6 py-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                    <i class="fas fa-user-check mr-2"></i>
                    <span>Sélection des étudiants</span>
                </h3>
                <div class="flex items-center gap-3">
                    <span class="text-white text-sm font-medium">
                        <span id="selected-count" class="text-2xl font-bold">0</span> sélectionné(s)
                    </span>
                </div>
            </div>
        </div>

        <div class="p-4 md:p-6">
            <!-- Recherche -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text"
                           id="searchEtudiant"
                           placeholder="Rechercher par nom, prénom ou matricule..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button"
                        id="selectAll"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-check-double mr-2"></i>
                    Tout sélectionner
                </button>
                <button type="button"
                        id="deselectAll"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Tout désélectionner
                </button>
                <button type="button"
                        id="toggleSelection"
                        class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Inverser
                </button>
            </div>

            <!-- Liste des étudiants avec checkboxes -->
            <div id="etudiantsList" class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto">
                @foreach($etudiants as $etudiant)
                    <label class="etudiant-item flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                        <input type="checkbox"
                               name="etudiants[]"
                               value="{{ $etudiant->id }}"
                               {{ in_array($etudiant->id, old('etudiants', [])) ? 'checked' : '' }}
                               class="etudiant-checkbox w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 mr-3 flex-shrink-0"
                               data-matricule="{{ strtolower($etudiant->matricule) }}"
                               data-nom="{{ strtolower($etudiant->personne->nom) }}"
                               data-prenom="{{ strtolower($etudiant->personne->prenom) }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ substr($etudiant->personne->prenom, 0, 1) }}{{ substr($etudiant->personne->nom, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 truncate">
                                        {{ $etudiant->personne->nom }} {{ $etudiant->personne->prenom }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">{{ $etudiant->matricule }}</p>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('etudiants')
                <small class="text-red-500 text-sm mt-2 block flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </small>
            @enderror

            <!-- Message si aucun résultat -->
            <div id="noResults" class="hidden text-center py-8">
                <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Aucun étudiant ne correspond à votre recherche</p>
            </div>
        </div>
    </div>

    <!-- Récapitulatif -->
    <div id="recapitulatif" class="hidden bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 md:p-6 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1 flex-shrink-0"></i>
            <div class="flex-1">
                <h4 class="font-semibold text-blue-900 mb-2">Récapitulatif</h4>
                <div class="text-sm text-blue-700 space-y-1">
                    <p><strong>Classe :</strong> <span id="recap-classe">-</span></p>
                    <p><strong>Année :</strong> <span id="recap-annee">-</span></p>
                    <p><strong>Date :</strong> <span id="recap-date">-</span></p>
                    <p><strong>Étudiants :</strong> <span id="recap-etudiants">0</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col-reverse sm:flex-row gap-3 justify-between">
        <a href="{{ route('administration.inscriptions.index') }}"
           class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors text-center">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Retour</span>
        </a>
        <button type="submit"
                id="submitBtn"
                disabled
                class="inline-flex items-center justify-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="fas fa-save mr-2"></i>
            <span>Enregistrer les inscriptions</span>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.etudiant-checkbox');
    const selectedCount = document.getElementById('selected-count');
    const submitBtn = document.getElementById('submitBtn');
    const searchInput = document.getElementById('searchEtudiant');
    const etudiantsList = document.querySelectorAll('.etudiant-item');
    const noResults = document.getElementById('noResults');
    const recapitulatif = document.getElementById('recapitulatif');

    // Mise à jour du compteur
    function updateCount() {
        const count = document.querySelectorAll('.etudiant-checkbox:checked').length;
        selectedCount.textContent = count;
        submitBtn.disabled = count === 0;

        // Afficher/masquer le récapitulatif
        if (count > 0) {
            recapitulatif.classList.remove('hidden');
            updateRecap();
        } else {
            recapitulatif.classList.add('hidden');
        }
    }

    // Mise à jour du récapitulatif
    function updateRecap() {
        const classeSelect = document.getElementById('classe_id');
        const annee = document.getElementById('annee_academique').value;
        const date = document.getElementById('date_inscription').value;
        const count = document.querySelectorAll('.etudiant-checkbox:checked').length;

        document.getElementById('recap-classe').textContent =
            classeSelect.options[classeSelect.selectedIndex]?.text || '-';
        document.getElementById('recap-annee').textContent = annee || '-';
        document.getElementById('recap-date').textContent = date ? new Date(date).toLocaleDateString('fr-FR') : '-';
        document.getElementById('recap-etudiants').textContent = count;
    }

    // Écouter les changements de checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCount);
    });

    // Recherche
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;

        etudiantsList.forEach(item => {
            const checkbox = item.querySelector('.etudiant-checkbox');
            const matricule = checkbox.dataset.matricule;
            const nom = checkbox.dataset.nom;
            const prenom = checkbox.dataset.prenom;

            const matches = !searchTerm ||
                           matricule.includes(searchTerm) ||
                           nom.includes(searchTerm) ||
                           prenom.includes(searchTerm);

            item.style.display = matches ? 'flex' : 'none';
            if (matches) visibleCount++;
        });

        // Afficher message si aucun résultat
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            document.getElementById('etudiantsList').classList.add('hidden');
        } else {
            noResults.classList.add('hidden');
            document.getElementById('etudiantsList').classList.remove('hidden');
        }
    });

    // Bouton tout sélectionner
    document.getElementById('selectAll').addEventListener('click', function() {
        const visibleCheckboxes = Array.from(checkboxes).filter(cb =>
            cb.closest('.etudiant-item').style.display !== 'none'
        );
        visibleCheckboxes.forEach(cb => cb.checked = true);
        updateCount();
    });

    // Bouton tout désélectionner
    document.getElementById('deselectAll').addEventListener('click', function() {
        checkboxes.forEach(cb => cb.checked = false);
        updateCount();
    });

    // Bouton inverser sélection
    document.getElementById('toggleSelection').addEventListener('click', function() {
        const visibleCheckboxes = Array.from(checkboxes).filter(cb =>
            cb.closest('.etudiant-item').style.display !== 'none'
        );
        visibleCheckboxes.forEach(cb => cb.checked = !cb.checked);
        updateCount();
    });

    // Bouton année suivante
    document.getElementById('anneeSuivante').addEventListener('click', function() {
        const currentYear = new Date().getFullYear();
        const nextYear = currentYear + 1;
        const yearAfterNext = nextYear + 1;
        document.getElementById('annee_academique').value = `${nextYear}-${yearAfterNext}`;
        updateRecap();
    });

    // Écouter les changements pour le récap
    document.getElementById('classe_id').addEventListener('change', updateRecap);
    document.getElementById('annee_academique').addEventListener('input', updateRecap);
    document.getElementById('date_inscription').addEventListener('change', updateRecap);

    // Confirmation avant soumission
    const form = document.getElementById('inscriptionMultipleForm');
    form.addEventListener('submit', function(e) {
        const count = document.querySelectorAll('.etudiant-checkbox:checked').length;
        const classe = document.getElementById('classe_id').options[document.getElementById('classe_id').selectedIndex].text;

        if (!confirm(`Confirmer l'inscription de ${count} étudiant(s) dans "${classe}" ?`)) {
            e.preventDefault();
            return false;
        }

        // Désactiver le bouton et afficher le chargement
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Traitement en cours...';
    });

    // Initialiser le compteur
    updateCount();
});
</script>
@endsection
