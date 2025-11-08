@extends('layouts.app')

@section('title', 'Tableau de bord Admin')

@section('content')
<!-- Header Dashboard -->
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Tableau de bord Administrateur</h1>
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
        <p class="text-blue-800">
            <i class="fas fa-user-shield mr-2"></i>
            Bonjour <strong>{{ Auth::user()->name }}</strong>, voici les statistiques de gestion.
        </p>
    </div>
</div>

<!-- Cartes de statistiques -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Utilisateurs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
        <div class="flex">
            <div class="bg-blue-500 w-20 md:w-24 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-white text-3xl"></i>
            </div>
            <div class="p-5 flex-1">
                <h3 class="text-lg font-semibold text-gray-700 mb-1">Utilisateurs</h3>
                <p class="text-sm text-gray-500 mb-2">Total des comptes</p>
                <span class="text-4xl font-bold text-blue-600">{{ $usersCount ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Étudiants -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
        <div class="flex">
            <div class="bg-green-500 w-20 md:w-24 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-graduate text-white text-3xl"></i>
            </div>
            <div class="p-5 flex-1">
                <h3 class="text-lg font-semibold text-gray-700 mb-1">Étudiants</h3>
                <p class="text-sm text-gray-500 mb-2">Total enregistrés</p>
                <span class="text-4xl font-bold text-green-600">{{ $etudiantsCount ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Inscriptions en attente -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
        <div class="flex">
            <div class="bg-yellow-500 w-20 md:w-24 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-file-signature text-white text-3xl"></i>
            </div>
            <div class="p-5 flex-1">
                <h3 class="text-lg font-semibold text-gray-700 mb-1">En attente</h3>
                <p class="text-sm text-gray-500 mb-2">À valider</p>
                <span class="text-4xl font-bold text-yellow-600">{{ $inscriptionsCount ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Cartes d'actions rapides -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
        Actions rapides
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Ajouter utilisateur -->
        <a href="{{ route('administration.utilisateurs.index') }}"
           class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all hover:-translate-y-1 group">
            <div class="p-6">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-purple-500 transition-colors">
                    <i class="fas fa-user-plus text-3xl text-purple-500 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Ajouter utilisateur</h3>
                <p class="text-sm text-gray-600 mb-4">Créer un nouveau compte</p>
                <span class="text-purple-600 font-medium group-hover:text-purple-700 inline-flex items-center">
                    Accéder
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </span>
            </div>
        </a>

        <!-- Créer une classe -->
        <a href="{{ route('administration.classes.index') }}"
           class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all hover:-translate-y-1 group">
            <div class="p-6">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-indigo-500 transition-colors">
                    <i class="fas fa-book text-3xl text-indigo-500 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Gérer les classes</h3>
                <p class="text-sm text-gray-600 mb-4">Ajouter et modifier les classes</p>
                <span class="text-indigo-600 font-medium group-hover:text-indigo-700 inline-flex items-center">
                    Accéder
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </span>
            </div>
        </a>

        <!-- Valider inscriptions -->
        <a href="{{ route('administration.inscriptions.index') }}"
           class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all hover:-translate-y-1 group">
            <div class="p-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-green-500 transition-colors">
                    <i class="fas fa-check-circle text-3xl text-green-500 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Valider inscriptions</h3>
                <p class="text-sm text-gray-600 mb-4">Gérer les inscriptions</p>
                <span class="text-green-600 font-medium group-hover:text-green-700 inline-flex items-center">
                    Accéder
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </span>
            </div>
        </a>
    </div>
</div>

<!-- Activité récente -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
        <h3 class="text-white text-xl font-semibold flex items-center">
            <i class="fas fa-history mr-2"></i>
            Activité récente
        </h3>
    </div>

    @php
        $activities = collect($recentActivities ?? []);
    @endphp

    <div class="p-6">
        @if($activities->isEmpty())
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-500">Aucune activité récente</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($activities as $activity)
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-4 flex-shrink-0">
                            <i class="fas fa-bell text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">
                                {{ $activity->description ?? 'Pas de description' }}
                            </p>
                            @if(isset($activity->created_at) && method_exists($activity->created_at, 'diffForHumans'))
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Widgets supplémentaires (optionnel) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
    <!-- Statistiques rapides -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-line text-blue-500 mr-2"></i>
            Statistiques du mois
        </h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Nouvelles inscriptions</span>
                <span class="text-lg font-bold text-green-600">
                    {{ $inscriptionsCount ?? 0 }}
                </span>
            </div>
            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Nouveaux utilisateurs</span>
                <span class="text-lg font-bold text-blue-600">
                    {{ $usersCount ?? 0 }}
                </span>
            </div>
            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Classes actives</span>
                <span class="text-lg font-bold text-purple-600">
                    {{ $classesCount ?? 0 }}
                </span>
            </div>
        </div>
    </div>

    <!-- Liens rapides -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-link text-purple-500 mr-2"></i>
            Liens rapides
        </h3>
        <div class="space-y-2">
            <a href="{{ route('administration.utilisateurs.index') }}"
               class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors group">
                <i class="fas fa-users text-blue-500 mr-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-gray-700 group-hover:text-blue-600 transition-colors">Gestion des utilisateurs</span>
                <i class="fas fa-chevron-right ml-auto text-gray-400 text-sm"></i>
            </a>
            <a href="{{ route('administration.classes.index') }}"
               class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors group">
                <i class="fas fa-chalkboard text-indigo-500 mr-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-gray-700 group-hover:text-indigo-600 transition-colors">Gestion des classes</span>
                <i class="fas fa-chevron-right ml-auto text-gray-400 text-sm"></i>
            </a>
            <a href="{{ route('administration.inscriptions.index') }}"
               class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors group">
                <i class="fas fa-file-signature text-green-500 mr-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-gray-700 group-hover:text-green-600 transition-colors">Gestion des inscriptions</span>
                <i class="fas fa-chevron-right ml-auto text-gray-400 text-sm"></i>
            </a>
        </div>
    </div>
</div>
@endsection
