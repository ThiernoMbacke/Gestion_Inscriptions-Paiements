@extends('layouts.app')

@section('title', 'Créer un nouvel utilisateur')

@section('content')
<div class="max-w-4xl mx-auto mt-6">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Créer un nouvel utilisateur</h1>
        <div class="breadcrumb text-sm md:text-base">
            <a href="{{ route('administration.dashboard') }}" class="text-blue-600 hover:underline">Tableau de bord</a> &raquo;
            <a href="{{ route('administration.utilisateurs.index') }}" class="text-blue-600 hover:underline">Utilisateurs</a> &raquo;
            <span class="text-gray-600">Création</span>
        </div>
    </div>

    <!-- Alerts -->
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

    <form action="{{ route('administration.utilisateurs.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf

        <!-- Informations personnelles -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 md:px-6 py-4">
                <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                    <i class="fas fa-user mr-2"></i> Informations personnelles
                </h3>
            </div>
            <div class="p-4 md:p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required maxlength="100" placeholder="Nom de famille"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('nom') border-red-500 @enderror">
                        @error('nom') <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small> @enderror
                    </div>
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required maxlength="100" placeholder="Prénom"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('prenom') border-red-500 @enderror">
                        @error('prenom') <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nom_d_utilisateur" class="block text-sm font-medium text-gray-700 mb-2">Nom d'utilisateur <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_d_utilisateur" id="nom_d_utilisateur" value="{{ old('nom_d_utilisateur') }}" required maxlength="50" placeholder="username"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('nom_d_utilisateur') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Utilisé pour la connexion</p>
                        @error('nom_d_utilisateur') <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required maxlength="150" placeholder="exemple@mail.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email') <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                        <input type="tel" name="telephone" id="telephone" value="{{ old('telephone') }}" maxlength="20" placeholder="77 123 45 67"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('telephone') border-red-500 @enderror">
                        @error('telephone') <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small> @enderror
                    </div>
                    <div>
                        <label for="date_de_naissance" class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                        <input type="date" name="date_de_naissance" id="date_de_naissance" value="{{ old('date_de_naissance') }}" max="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('date_de_naissance') border-red-500 @enderror">
                        @error('date_de_naissance') <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div>
                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                    <textarea name="adresse" id="adresse" rows="3" placeholder="Adresse complète"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('adresse') border-red-500 @enderror">{{ old('adresse') }}</textarea>
                    @error('adresse') <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small> @enderror
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                    <div class="flex items-center gap-4">
                        <label class="flex-1 flex flex-col items-center px-4 py-6 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                            <span class="text-sm text-gray-600">Cliquez pour choisir une image</span>
                            <span class="text-xs text-gray-500 mt-1">PNG, JPG jusqu'à 2MB</span>
                            <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewImage(event)">
                        </label>
                        <div id="preview-container" class="hidden">
                            <img id="preview" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200" alt="Aperçu">
                        </div>
                    </div>
                    @error('photo') <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <!-- Sécurité et Rôle -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 md:px-6 py-4">
                <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                    <i class="fas fa-shield-alt mr-2"></i> Sécurité et Rôle
                </h3>
            </div>
            <div class="p-4 md:p-6 space-y-6">
                <!-- Mots de passe -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required minlength="6" placeholder="••••••••"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 @error('password') border-red-500 @enderror">
                            <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimum 6 caractères</p>
                        @error('password') <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6" placeholder="••••••••"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password_confirmation-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Rôles -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rôle de l'utilisateur <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @php
                            $roles = [
                                'etudiant' => ['icon' => 'graduation-cap', 'color' => 'green', 'label' => 'Étudiant', 'desc' => 'Accès étudiant'],
                                'admin' => ['icon' => 'user-shield', 'color' => 'blue', 'label' => 'Administrateur', 'desc' => 'Accès complet'],
                                'comptable' => ['icon' => 'calculator', 'color' => 'teal', 'label' => 'Comptable', 'desc' => 'Gestion financière'],
                            ];
                        @endphp

                        @foreach($roles as $key => $role)
                            <label class="role-card cursor-pointer">
                                <input type="radio" name="role" value="{{ $key }}" class="hidden" {{ old('role') == $key ? 'checked' : '' }} required>
                                <div class="p-4 border-2 rounded-lg flex flex-col items-center transition-all">
                                    <div class="w-12 h-12 bg-{{ $role['color'] }}-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-{{ $role['icon'] }} text-2xl text-{{ $role['color'] }}-600"></i>
                                    </div>
                                    <h4 class="text-center font-semibold text-gray-800 mb-1">{{ $role['label'] }}</h4>
                                    <p class="text-center text-xs text-gray-600">{{ $role['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('role') <small class="text-red-500 text-sm mt-2 block">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col-reverse sm:flex-row gap-3 justify-between">
            <a href="{{ route('administration.utilisateurs.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-times mr-2"></i> Annuler
            </a>
            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-save mr-2"></i> Créer l'utilisateur
            </button>
        </div>
    </form>
</div>

<!-- Scripts -->
<script>
// Image preview
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
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
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Role selection
document.addEventListener('DOMContentLoaded', function() {
    const roleRadios = document.querySelectorAll('.role-card input[type="radio"]');
    roleRadios.forEach(radio => {
        const card = radio.parentElement.querySelector('div');
        // Initial active state
        if (radio.checked) card.classList.add(`border-${radio.value === 'etudiant' ? 'green' : radio.value === 'admin' ? 'blue' : 'teal'}-500`, `bg-${radio.value === 'etudiant' ? 'green' : radio.value === 'admin' ? 'blue' : 'teal'}-50`);

        radio.addEventListener('change', function() {
            roleRadios.forEach(r => r.parentElement.querySelector('div').classList.remove('border-green-500','bg-green-50','border-blue-500','bg-blue-50','border-teal-500','bg-teal-50'));
            const color = this.value === 'etudiant' ? 'green' : this.value === 'admin' ? 'blue' : 'teal';
            card.classList.add(`border-${color}-500`,`bg-${color}-50`);
        });
    });
});
</script>

<style>
.role-card div {
    border-color: #d1d5db; /* gray-300 */
}
.role-card:hover div {
    border-color: #3b82f6; /* blue-500 */
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
</style>
@endsection
