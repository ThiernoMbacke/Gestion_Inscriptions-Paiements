@extends('layouts.app')

@section('title', 'Modifier mon profil')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="page-header mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Modifier mon profil</h1>
        <div class="breadcrumb text-sm md:text-base text-gray-600 mt-2">
            <a href="{{ route('etudiant.dashboard') }}" class="text-blue-600 hover:text-blue-800">Tableau de bord</a>
            <span class="mx-2">›</span>
            <span>Mon profil</span>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 animate-fadeIn">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Alert Errors -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5 flex-shrink-0"></i>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-800 mb-2">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Info utilisateur -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
            <div class="flex-shrink-0">
                @if($user->personne->photo ?? false)
                    <img src="{{ asset('storage/' . $user->personne->photo) }}"
                         alt="Photo"
                         class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg">
                @else
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white font-bold text-2xl border-4 border-white shadow-lg">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                @endif
            </div>
            <div class="flex-1 text-center sm:text-left">
                <p class="text-xl font-bold mb-1">
                    {{ $user->personne->prenom ?? '' }} {{ $user->personne->nom ?? '' }}
                </p>
                <p class="text-blue-100 text-sm">
                    <i class="fas fa-envelope mr-1"></i> {{ $user->email }}
                </p>
                <div class="inline-flex items-center bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full mt-2">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    <span class="text-sm font-medium">Étudiant</span>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('etudiant.profil.update') }}"
          method="POST"
          enctype="multipart/form-data"
          class="max-w-4xl mx-auto">
        @csrf
        @method('PATCH')

        <!-- Section Informations personnelles -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 md:px-6 py-4">
                <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                    <i class="fas fa-user-edit mr-2"></i>
                    <span>Informations personnelles</span>
                </h3>
            </div>

            <div class="p-4 md:p-6 lg:p-8 space-y-6">
                <!-- Nom complet (pour users table) -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           required
                           maxlength="255"
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base @error('name') border-red-500 @enderror">
                    @error('name')
                        <small class="text-red-500 text-sm mt-1 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <!-- Nom et Prénom (pour personnes table) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom
                        </label>
                        <input type="text"
                               name="nom"
                               id="nom"
                               maxlength="100"
                               value="{{ old('nom', $user->personne?->nom) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base @error('nom') border-red-500 @enderror">
                        @error('nom')
                            <small class="text-red-500 text-sm mt-1 block flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom
                        </label>
                        <input type="text"
                               name="prenom"
                               id="prenom"
                               maxlength="100"
                               value="{{ old('prenom', $user->personne?->prenom) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base @error('prenom') border-red-500 @enderror">
                        @error('prenom')
                            <small class="text-red-500 text-sm mt-1 block flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                </div>

                <!-- Nom d'utilisateur et Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nom_d_utilisateur" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom d'utilisateur
                        </label>
                        <input type="text"
                               name="nom_d_utilisateur"
                               id="nom_d_utilisateur"
                               maxlength="50"
                               value="{{ old('nom_d_utilisateur', $user->personne?->nom_d_utilisateur) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base @error('nom_d_utilisateur') border-red-500 @enderror">
                        @error('nom_d_utilisateur')
                            <small class="text-red-500 text-sm mt-1 block flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               required
                               maxlength="150"
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base @error('email') border-red-500 @enderror">
                        @error('email')
                            <small class="text-red-500 text-sm mt-1 block flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                </div>

                <!-- Téléphone et Date de naissance -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone
                        </label>
                        <input type="tel"
                               name="telephone"
                               id="telephone"
                               maxlength="20"
                               value="{{ old('telephone', $user->personne?->telephone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base @error('telephone') border-red-500 @enderror">
                        @error('telephone')
                            <small class="text-red-500 text-sm mt-1 block flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div>
                        <label for="date_de_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de naissance
                        </label>
                        <input type="date"
                               name="date_de_naissance"
                               id="date_de_naissance"
                               value="{{ old('date_de_naissance', $user->personne?->date_de_naissance) }}"
                               max="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base @error('date_de_naissance') border-red-500 @enderror">
                        @error('date_de_naissance')
                            <small class="text-red-500 text-sm mt-1 block flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                </div>

                <!-- Adresse -->
                <div>
                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse
                    </label>
                    <textarea name="adresse"
                              id="adresse"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base resize-y @error('adresse') border-red-500 @enderror">{{ old('adresse', $user->personne?->adresse) }}</textarea>
                    @error('adresse')
                        <small class="text-red-500 text-sm mt-1 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <!-- Photo de profil -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Photo de profil
                    </label>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Photo actuelle -->
                        @if($user->personne->photo ?? false)
                            <div class="flex-shrink-0">
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $user->personne->photo) }}"
                                         id="current-photo"
                                         alt="Photo actuelle"
                                         class="w-32 h-32 rounded-lg object-cover border-2 border-gray-200">
                                    <div class="absolute bottom-0 right-0 bg-blue-500 text-white px-2 py-1 rounded-tl-lg rounded-br-lg text-xs">
                                        Actuelle
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Upload nouvelle photo -->
                        <div class="flex-1">
                            <label class="flex flex-col items-center px-4 py-6 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <span class="text-sm text-gray-600">Choisir une nouvelle photo</span>
                                <span class="text-xs text-gray-500 mt-1">PNG, JPG jusqu'à 2MB</span>
                                <input type="file"
                                       name="photo"
                                       id="photo"
                                       accept="image/*"
                                       class="hidden"
                                       onchange="previewImage(event)">
                            </label>
                            <div id="preview-container" class="hidden mt-4">
                                <p class="text-sm text-gray-700 mb-2">Nouvelle photo :</p>
                                <img id="preview" class="w-32 h-32 rounded-lg object-cover border-2 border-blue-500" alt="Aperçu">
                            </div>
                        </div>
                    </div>
                    @error('photo')
                        <small class="text-red-500 text-sm mt-2 block flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section Sécurité -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 md:px-6 py-4">
                <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                    <i class="fas fa-lock mr-2"></i>
                    <span>Sécurité</span>
                </h3>
            </div>

            <div class="p-4 md:p-6 lg:p-8 space-y-6">
                <!-- Info mot de passe -->
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm font-medium text-blue-900">Modification du mot de passe</p>
                            <p class="text-sm text-blue-700 mt-1">
                                Laissez les champs vides si vous ne souhaitez pas changer votre mot de passe.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mots de passe -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Nouveau mot de passe
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   minlength="6"
                                   placeholder="Laisser vide si inchangé"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-base @error('password') border-red-500 @enderror">
                            <button type="button"
                                    onclick="togglePassword('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimum 6 caractères</p>
                        @error('password')
                            <small class="text-red-500 text-sm mt-1 block flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmer le mot de passe
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   minlength="6"
                                   placeholder="Confirmer le nouveau mot de passe"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-base">
                            <button type="button"
                                    onclick="togglePassword('password_confirmation')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password_confirmation-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col-reverse sm:flex-row gap-3 justify-between">
            <a href="{{ route('etudiant.dashboard') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors text-center">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Retour au tableau de bord</span>
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <i class="fas fa-save mr-2"></i>
                <span>Enregistrer les modifications</span>
            </button>
        </div>
    </form>
</div>

<script>
// Prévisualisation de l'image
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('preview-container').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}
</style>
@endsection
