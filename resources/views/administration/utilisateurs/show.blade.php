@extends('layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="page-header">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Détails de l'utilisateur</h1>
    <div class="breadcrumb text-sm md:text-base">
        <a href="{{ route('administration.dashboard') }}">Tableau de bord</a> &raquo;
        <a href="{{ route('administration.utilisateurs.index') }}">Utilisateurs</a> &raquo;
        <span class="text-gray-600">{{ $user->name }}</span>
    </div>
</div>

<!-- Actions rapides -->
<div class="flex flex-wrap gap-3 mb-6">
    <a href="{{ route('administration.utilisateurs.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        Retour
    </a>
    <a href="{{ route('administration.utilisateurs.edit', $user->id) }}"
       class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
        <i class="fas fa-edit mr-2"></i>
        Modifier
    </a>
    @if($user->id !== auth()->id())
        <form method="POST" action="{{ route('administration.utilisateurs.destroy', $user->id) }}" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                <i class="fas fa-trash mr-2"></i>
                Supprimer
            </button>
        </form>
    @endif
</div>

<!-- Card principale avec photo -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Carte profil -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-24"></div>
        <div class="px-6 pb-6 -mt-12">
            <div class="flex flex-col items-center">
                @if($user->personne->photo ?? false)
                    <img src="{{ asset('storage/' . $user->personne->photo) }}"
                         alt="Photo de {{ $user->personne->prenom }}"
                         class="w-32 h-32 rounded-full border-4 border-white object-cover shadow-lg">
                @else
                    <div class="w-32 h-32 rounded-full border-4 border-white bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                        {{ substr($user->personne->prenom, 0, 1) }}{{ substr($user->personne->nom, 0, 1) }}
                    </div>
                @endif

                <h2 class="mt-4 text-2xl font-bold text-gray-800 text-center">
                    {{ $user->personne->prenom }} {{ $user->personne->nom }}
                </h2>

                <div class="mt-2">
                    @php
                        $roleConfig = [
                            'etudiant' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-graduation-cap'],
                            'admin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-user-shield'],
                            'comptable' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'icon' => 'fa-calculator'],
                        ];
                        $config = $roleConfig[$user->role] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-user'];
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                        <i class="fas {{ $config['icon'] }} mr-2"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                <div class="mt-4 text-center text-sm text-gray-500">
                    <i class="fas fa-calendar-plus mr-1"></i>
                    Créé le {{ $user->created_at->format('d/m/Y à H:i') }}
                </div>

                @if($user->email_verified_at)
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        Email vérifié
                    </div>
                @else
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        <i class="fas fa-clock mr-1"></i>
                        En attente de vérification
                    </div>
                @endif
            </div>

            <!-- Stats rapides -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-blue-600">
                            @if($user->role === 'etudiant' && $user->personne->etudiant)
                                {{ $user->personne->etudiant->inscriptions->count() ?? 0 }}
                            @else
                                -
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">Inscriptions</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">
                            @if($user->role === 'etudiant' && $user->personne->etudiant)
                                {{ $user->personne->etudiant->paiements->count() ?? 0 }}
                            @else
                                -
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">Paiements</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations détaillées -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Informations personnelles -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center">
                    <i class="fas fa-user mr-2"></i>
                    Informations personnelles
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <i class="fas fa-id-card text-blue-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 mb-1">Nom complet</p>
                            <p class="font-semibold text-gray-900 truncate">
                                {{ $user->personne->prenom }} {{ $user->personne->nom }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <i class="fas fa-at text-green-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 mb-1">Nom d'utilisateur</p>
                            <p class="font-semibold text-gray-900 truncate">
                                {{ $user->personne->nom_d_utilisateur }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <i class="fas fa-envelope text-purple-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="font-semibold text-gray-900 truncate">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <i class="fas fa-phone text-yellow-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 mb-1">Téléphone</p>
                            <p class="font-semibold text-gray-900">
                                {{ $user->personne->telephone ?? 'Non renseigné' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <i class="fas fa-birthday-cake text-pink-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 mb-1">Date de naissance</p>
                            <p class="font-semibold text-gray-900">
                                {{ $user->personne->date_de_naissance
                                    ? \Carbon\Carbon::parse($user->personne->date_de_naissance)->format('d/m/Y')
                                    : 'Non renseignée'
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <i class="fas fa-map-marker-alt text-indigo-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 mb-1">Adresse</p>
                            <p class="font-semibold text-gray-900">
                                {{ $user->personne->adresse ?? 'Non renseignée' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations spécifiques au rôle -->
        @if($user->role === 'etudiant' && $user->personne->etudiant)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        Informations étudiant
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border-l-4 border-green-500">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0 mr-4 shadow">
                                <i class="fas fa-id-badge text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Matricule</p>
                                <p class="text-xl font-bold text-gray-900">
                                    {{ $user->personne->etudiant->matricule }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border-l-4 border-blue-500">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0 mr-4 shadow">
                                <i class="fas fa-envelope-open-text text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Notifications email</p>
                                <p class="text-lg font-bold text-gray-900">
                                    @if($user->personne->etudiant->accepte_email)
                                        <span class="text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>Activées
                                        </span>
                                    @else
                                        <span class="text-gray-500">
                                            <i class="fas fa-times-circle mr-1"></i>Désactivées
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Inscriptions récentes -->
                    @if($user->personne->etudiant->inscriptions->isNotEmpty())
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-list-alt mr-2 text-green-600"></i>
                                Inscriptions récentes
                            </h4>
                            <div class="space-y-3">
                                @foreach($user->personne->etudiant->inscriptions->take(3) as $inscription)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-book text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $inscription->classe->libelle }}</p>
                                                <p class="text-xs text-gray-500">{{ $inscription->annee_academique }}</p>
                                            </div>
                                        </div>
                                        @php
                                            $statutConfig = [
                                                'valide' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle'],
                                                'en_attente' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-clock'],
                                                'rejete' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle'],
                                            ];
                                            $statut = $statutConfig[$inscription->statut] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-question'];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statut['bg'] }} {{ $statut['text'] }}">
                                            <i class="fas {{ $statut['icon'] }} mr-1"></i>
                                            {{ ucfirst($inscription->statut) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($user->role === 'comptable' && $user->personne->comptable)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-teal-500 to-green-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center">
                        <i class="fas fa-calculator mr-2"></i>
                        Informations comptable
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-center py-8">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check text-teal-600 text-2xl"></i>
                            </div>
                            <p class="text-gray-700 font-medium">Compte comptable actif</p>
                            <p class="text-sm text-gray-500 mt-1">Accès à la gestion financière</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($user->role === 'admin' && $user->personne->administration)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center">
                        <i class="fas fa-user-shield mr-2"></i>
                        Informations administration
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-center py-8">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shield-alt text-blue-600 text-2xl"></i>
                            </div>
                            <p class="text-gray-700 font-medium">Compte administrateur actif</p>
                            <p class="text-sm text-gray-500 mt-1">Accès complet au système</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
