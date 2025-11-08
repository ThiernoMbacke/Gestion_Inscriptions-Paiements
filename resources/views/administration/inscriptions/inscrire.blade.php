@extends('layouts.app')

@section('title', 'Inscrire un étudiant')

@section('content')
<div class="page-header">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Nouvelle Inscription</h1>
    <div class="breadcrumb text-sm md:text-base">
        <a href="{{ route('administration.dashboard') }}">Tableau de bord</a> &raquo;
        <a href="{{ route('administration.inscriptions.index') }}">Inscriptions</a> &raquo;
        <span class="text-gray-600">Nouvelle</span>
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

<form action="{{ route('administration.inscriptions.store') }}"
      method="POST"
      id="inscriptionForm"
      class="max-w-4xl mx-auto">
    @csrf

    <!-- Formulaire principal -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 md:px-6 py-4">
            <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                <i class="fas fa-user-plus mr-2"></i>
                <span>Informations d'inscription</span>
            </h3>
        </div>

        <div class="p-4 md:p-6 lg:p-8 space-y-6">
            <!-- Étudiant -->
            <div>
                <label for="etudiant_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Sélectionner l'étudiant <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="etudiant_id"
                            id="etudiant_id"
                            required
                            class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base appearance-none @error('etudiant_id') border-red-500 @enderror">
                        <option value="" disabled {{ old('etudiant_id') ? '' : 'selected' }}>-- Choisissez un étudiant --</option>
                        @foreach($etudiants as $etudiant)
                            <option value="{{ $etudiant->id }}"
                                    {{ old('etudiant_id') == $etudiant->id ? 'selected' : '' }}
                                    data-matricule="{{ $etudiant->matricule }}"
                                    data-nom="{{ $etudiant->personne->nom }}"
                                    data-prenom="{{ $etudiant->personne->prenom }}">
                                {{ $etudiant->personne->nom }} {{ $etudiant->personne->prenom }} - {{ $etudiant->matricule }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                @error('etudiant_id')
                    <small class="text-red-500 text-sm mt-1 block flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </small>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Sélectionnez l'étudiant à inscrire dans une classe
                </p>
            </div>

            <!-- Aperçu étudiant sélectionné -->
            <div id="etudiantPreview" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-3">
                        <span id="etudiantInitiales"></span>
                    </div>
                    <div>
                        <p class="font-semibold text-blue-900" id="etudiantNomComplet"></p>
                        <p class="text-sm text-blue-700" id="etudiantMatricule"></p>
                    </div>
                </div>
            </div>

            <!-- Classe -->
            <div>
                <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Classe <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="classe_id"
                            id="classe_id"
                            required
                            class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base appearance-none @error('classe_id') border-red-500 @enderror">
                        <option value="" disabled {{ old('classe_id') ? '' : 'selected' }}>-- Choisissez une classe --</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}"
                                    {{ old('classe_id') == $classe->id ? 'selected' : '' }}
                                    data-frais-inscription="{{ $classe->frais_inscription }}"
                                    data-frais-mensualite="{{ $classe->frais_mensualite }}">
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
            </div>

            <!-- Aperçu frais classe -->
            <div id="fraisPreview" class="hidden bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-money-bill-wave text-green-500 text-xl mr-3 mt-1"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-green-900 mb-2">Frais de scolarité</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-600">Inscription :</span>
                                <span class="font-bold text-green-700 ml-2" id="fraisInscription">0 FCFA</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Mensualité :</span>
                                <span class="font-bold text-green-700 ml-2" id="fraisMensualite">0 FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grille responsive pour Date et Année -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                           value="{{ old('annee_academique', date('Y').'-'.(date('Y')+1)) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base @error('annee_academique') border-red-500 @enderror">
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
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base @error('date_inscription') border-red-500 @enderror">
                    @error('date_inscription')
                        <small class="text-red-500 text-sm mt-1 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>

            <!-- Information importante -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-yellow-500 mr-3 mt-1"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-yellow-800 mb-1">Information</p>
                        <p class="text-sm text-yellow-700">
                            L'inscription sera créée avec le statut "En attente". Vous pourrez la valider depuis la liste des inscriptions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col-reverse sm:flex-row gap-3 justify-between">
        <a href="{{ route('administration.inscriptions.index') }}"
           class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors text-center">
            <i class="fas fa-times mr-2"></i>
            <span>Annuler</span>
        </a>
        <button type="submit"
                id="submitBtn"
                class="inline-flex items-center justify-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-check mr-2"></i>
            <span>Créer l'inscription</span>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const etudiantSelect = document.getElementById('etudiant_id');
    const classeSelect = document.getElementById('classe_id');
    const etudiantPreview = document.getElementById('etudiantPreview');
    const fraisPreview = document.getElementById('fraisPreview');

    // Aperçu de l'étudiant sélectionné
    etudiantSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            const nom = selectedOption.dataset.nom;
            const prenom = selectedOption.dataset.prenom;
            const matricule = selectedOption.dataset.matricule;
            const initiales = prenom.charAt(0) + nom.charAt(0);

            document.getElementById('etudiantInitiales').textContent = initiales.toUpperCase();
            document.getElementById('etudiantNomComplet').textContent = `${nom} ${prenom}`;
            document.getElementById('etudiantMatricule').textContent = `Matricule : ${matricule}`;

            etudiantPreview.classList.remove('hidden');
        } else {
            etudiantPreview.classList.add('hidden');
        }
    });

    // Aperçu des frais de la classe
    classeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            const fraisInscription = parseInt(selectedOption.dataset.fraisInscription);
            const fraisMensualite = parseInt(selectedOption.dataset.fraisMensualite);

            document.getElementById('fraisInscription').textContent =
                new Intl.NumberFormat('fr-FR').format(fraisInscription) + ' FCFA';
            document.getElementById('fraisMensualite').textContent =
                new Intl.NumberFormat('fr-FR').format(fraisMensualite) + ' FCFA';

            fraisPreview.classList.remove('hidden');
        } else {
            fraisPreview.classList.add('hidden');
        }
    });

    // Afficher les aperçus si des valeurs sont déjà sélectionnées (old values)
    if (etudiantSelect.value) {
        etudiantSelect.dispatchEvent(new Event('change'));
    }
    if (classeSelect.value) {
        classeSelect.dispatchEvent(new Event('change'));
    }

    // Confirmation avant soumission
    const form = document.getElementById('inscriptionForm');
    form.addEventListener('submit', function(e) {
        const etudiantText = etudiantSelect.options[etudiantSelect.selectedIndex].text;
        const classeText = classeSelect.options[classeSelect.selectedIndex].text;
        const annee = document.getElementById('annee_academique').value;

        const message = `Confirmer la création de cette inscription ?\n\n` +
                       `Étudiant : ${etudiantText}\n` +
                       `Classe : ${classeText}\n` +
                       `Année : ${annee}`;

        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }

        // Désactiver le bouton pour éviter double soumission
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Création en cours...';

        return true;
    });
});
</script>
@endsection
