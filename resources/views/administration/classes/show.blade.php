@extends('layouts.app')

@section('title', 'Détails de la classe')

@section('content')
<div class="page-header mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $classe->libelle }}</h1>
            <div class="breadcrumb text-sm md:text-base mt-2">
                <a href="{{ route('administration.dashboard') }}" class="text-blue-600 hover:text-blue-800 transition-colors">Tableau de bord</a> &raquo;
                <a href="{{ route('administration.classes.index') }}" class="text-blue-600 hover:text-blue-800 transition-colors">Classes</a> &raquo;
                <span class="text-gray-600">Détails</span>
            </div>
        </div>

        <!-- Actions rapides - Couleurs conservées -->
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('administration.classes.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Retour à la liste</span>
            </a>
            <a href="{{ route('administration.classes.edit', $classe) }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm">
                <i class="fas fa-edit mr-2"></i>
                <span>Modifier</span>
            </a>
            <form method="POST" action="{{ route('administration.classes.destroy', $classe) }}" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm"
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')">
                    <i class="fas fa-trash mr-2"></i>
                    <span>Supprimer</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Informations principales -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Carte Info générale -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-lg">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h2 class="text-white text-lg md:text-xl font-semibold flex items-center">
                <i class="fas fa-info-circle mr-3"></i>
                <span>Informations générales</span>
            </h2>
        </div>
        <div class="p-6">
            <!-- Libellé -->
            <div class="mb-6 pb-4 border-b border-gray-100">
                <label class="block text-sm font-medium text-gray-500 mb-2">Libellé de la classe</label>
                <p class="text-xl font-bold text-gray-900">{{ $classe->libelle }}</p>
            </div>

            <!-- Description -->
            <div class="mb-6 pb-4 border-b border-gray-100">
                <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
                <p class="text-base text-gray-700 leading-relaxed">
                    {{ $classe->description ?? 'Aucune description disponible' }}
                </p>
            </div>

            <!-- Date de création -->
            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                <div class="flex items-center bg-blue-50 px-3 py-2 rounded-lg">
                    <i class="fas fa-calendar-plus text-blue-500 mr-2"></i>
                    <span>Créée le {{ $classe->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                @if($classe->updated_at != $classe->created_at)
                <div class="flex items-center bg-gray-50 px-3 py-2 rounded-lg">
                    <i class="fas fa-calendar-edit text-gray-500 mr-2"></i>
                    <span>Modifiée le {{ $classe->updated_at->format('d/m/Y à H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Carte Statistiques -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-lg">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h2 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Statistiques</span>
            </h2>
        </div>
        <div class="p-6">
            <div class="text-center mb-4">
                <div class="text-5xl font-bold text-blue-600 mb-2">
                    {{ $classe->inscriptions->filter(fn($i) => $i->user !== null)->count() }}
                </div>
                <div class="text-base text-gray-600 font-medium">Étudiant(s) inscrit(s)</div>
            </div>

            @if($classe->inscriptions->filter(fn($i) => $i->user !== null)->count() > 0)
            <div class="pt-4 border-t border-gray-200">
                <a href="#liste-etudiants"
                   class="inline-flex items-center justify-center w-full px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm">
                    <i class="fas fa-users mr-2"></i>
                    Voir les étudiants
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Frais de scolarité -->
<div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 mb-8 transition-all duration-300 hover:shadow-lg">
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
        <h2 class="text-white text-lg md:text-xl font-semibold flex items-center">
            <i class="fas fa-money-bill-wave mr-3"></i>
            <span>Frais de scolarité</span>
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Frais d'inscription -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-l-4 border-blue-500 transition-all duration-300 hover:shadow-md">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Frais d'inscription</p>
                        <p class="text-2xl font-bold text-blue-700">
                            {{ number_format($classe->frais_inscription, 0, ',', ' ') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">FCFA</p>
                    </div>
                    <div class="bg-blue-500 rounded-full p-3 shadow-sm">
                        <i class="fas fa-file-signature text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Frais de mensualité -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-l-4 border-blue-500 transition-all duration-300 hover:shadow-md">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Frais de mensualité</p>
                        <p class="text-2xl font-bold text-blue-700">
                            {{ number_format($classe->frais_mensualite, 0, ',', ' ') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">FCFA / mois</p>
                    </div>
                    <div class="bg-blue-500 rounded-full p-3 shadow-sm">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Frais de soutenance -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-l-4 border-blue-500 sm:col-span-2 lg:col-span-1 transition-all duration-300 hover:shadow-md">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Frais de soutenance</p>
                        <p class="text-2xl font-bold text-blue-700">
                            {{ number_format($classe->frais_soutenance, 0, ',', ' ') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">FCFA</p>
                    </div>
                    <div class="bg-blue-500 rounded-full p-3 shadow-sm">
                        <i class="fas fa-graduation-cap text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total estimé -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100 transition-all duration-300 hover:shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <p class="text-base font-semibold text-gray-700">Coût total estimé (1 an)</p>
                        <p class="text-sm text-gray-500">Inscription + 10 mensualités + Soutenance</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-blue-700">
                            {{ number_format($classe->frais_inscription + ($classe->frais_mensualite * 10) + $classe->frais_soutenance, 0, ',', ' ') }}
                        </p>
                        <p class="text-sm font-medium text-gray-600">FCFA</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des étudiants inscrits -->
@if($classe->inscriptions->filter(fn($i) => $i->user !== null)->count() > 0)
<div id="liste-etudiants" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-lg">
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
        <h2 class="text-white text-lg md:text-xl font-semibold flex items-center">
            <i class="fas fa-users mr-3"></i>
            <span>Étudiants inscrits ({{ $classe->inscriptions->filter(fn($i) => $i->user !== null)->count() }})</span>
        </h2>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Étudiant</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date inscription</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($classe->inscriptions as $inscription)
                    @if($inscription->user)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold mr-3 shadow-sm">
                                    {{ substr($inscription->user->prenom, 0, 1) }}{{ substr($inscription->user->nom, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $inscription->user->prenom }} {{ $inscription->user->nom }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inscription->user->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inscription->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if($inscription->statut === 'validee')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check-circle mr-1.5"></i> Validée
                                </span>
                            @elseif($inscription->statut === 'en_attente')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <i class="fas fa-clock mr-1.5"></i> En attente
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-times-circle mr-1.5"></i> Rejetée
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden divide-y divide-gray-200">
        @foreach($classe->inscriptions as $inscription)
            @if($inscription->user)
            <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-lg flex-shrink-0 shadow-sm">
                        {{ substr($inscription->user->prenom, 0, 1) }}{{ substr($inscription->user->nom, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $inscription->user->prenom }} {{ $inscription->user->nom }}</p>
                        <p class="text-sm text-gray-600 truncate">{{ $inscription->user->email }}</p>
                        <p class="text-xs text-gray-500 mt-1">Inscrit le {{ $inscription->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div>
                    @if($inscription->statut === 'validee')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            <i class="fas fa-check-circle mr-1.5"></i> Validée
                        </span>
                    @elseif($inscription->statut === 'en_attente')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <i class="fas fa-clock mr-1.5"></i> En attente
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                            <i class="fas fa-times-circle mr-1.5"></i> Rejetée
                        </span>
                    @endif
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endif

<!-- Aucun étudiant inscrit -->
@if($classe->inscriptions->filter(fn($i) => $i->user !== null)->count() === 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
    <div class="max-w-md mx-auto">
        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-users text-blue-400 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun étudiant inscrit</h3>
        <p class="text-gray-500 mb-4">Cette classe ne contient actuellement aucun étudiant inscrit.</p>
    </div>
</div>
@endif
@endsection
