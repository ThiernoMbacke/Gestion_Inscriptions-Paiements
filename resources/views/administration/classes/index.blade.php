@extends('layouts.app')

@section('title', 'Gestion des Classes')

@section('content')
<div class="page-header">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Gestion des Classes</h1>
    <div class="breadcrumb text-sm md:text-base">
        <a href="{{ route('administration.dashboard') }}">Tableau de bord</a> &raquo;
        <span class="text-gray-600">Classes</span>
    </div>
</div>

<!-- Statistiques des classes -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="flex">
            <div class="bg-blue-500 w-20 md:w-24 flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-white text-2xl md:text-3xl"></i>
            </div>
            <div class="p-4 md:p-6 flex-1">
                <h3 class="text-base md:text-lg font-semibold text-gray-700 mb-1">Total Classes</h3>
                <p class="text-3xl md:text-4xl font-bold text-blue-600 mb-1">{{ $classes->count() }}</p>
                <small class="text-xs md:text-sm text-gray-500">Classes disponibles</small>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="flex">
            <div class="bg-green-500 w-20 md:w-24 flex items-center justify-center">
                <i class="fas fa-users text-white text-2xl md:text-3xl"></i>
            </div>
            <div class="p-4 md:p-6 flex-1">
                <h3 class="text-base md:text-lg font-semibold text-gray-700 mb-1">Étudiants inscrits</h3>
                <p class="text-3xl md:text-4xl font-bold text-green-600 mb-1">{{ $classes->sum(fn($c) => $c->inscriptions->count()) }}</p>
                <small class="text-xs md:text-sm text-gray-500">Total des inscriptions</small>
            </div>
        </div>
    </div>
</div>

<!-- Liste des classes -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 md:px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
            <i class="fas fa-list mr-2"></i>
            <span>Liste des Classes</span>
        </h3>
        <a href="{{ route('administration.classes.create') }}"
           class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-100 text-blue-600 font-medium rounded-lg transition-colors shadow-sm">
            <i class="fas fa-plus mr-2"></i>
            <span>Nouvelle Classe</span>
        </a>
    </div>

    @if($classes && $classes->count() > 0)
        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Libellé</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Inscriptions</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date création</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($classes as $classe)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900">{{ $classe->libelle }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ Str::limit($classe->description ?? 'Aucune description', 50) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $classe->inscriptions->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ $classe->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('administration.classes.show', $classe) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors"
                                       title="Voir">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('administration.classes.edit', $classe) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition-colors"
                                       title="Modifier">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form method="POST" action="{{ route('administration.classes.destroy', $classe) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors"
                                                onclick="return confirm('Supprimer cette classe ?')"
                                                title="Supprimer">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards (visible only on mobile/tablet) -->
        <div class="lg:hidden divide-y divide-gray-200">
            @foreach($classes as $classe)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-base mb-1">{{ $classe->libelle }}</h4>
                            <p class="text-sm text-gray-600 line-clamp-2">
                                {{ $classe->description ?? 'Aucune description' }}
                            </p>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center gap-4 mb-3 text-sm">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-users mr-1 text-blue-500"></i>
                            <span>{{ $classe->inscriptions->count() }} inscrit(s)</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar mr-1 text-gray-400"></i>
                            <span>{{ $classe->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('administration.classes.show', $classe) }}"
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            Voir
                        </a>
                        <a href="{{ route('administration.classes.edit', $classe) }}"
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('administration.classes.destroy', $classe) }}" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors"
                                    onclick="return confirm('Supprimer cette classe ?')">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-12 px-4">
            <div class="w-20 h-20 md:w-24 md:h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-chalkboard-teacher text-3xl md:text-4xl text-gray-400"></i>
            </div>
            <h4 class="text-lg md:text-xl font-semibold text-gray-700 mb-2">Aucune classe trouvée</h4>
            <p class="text-sm md:text-base text-gray-500 mb-6 text-center">Commencez par créer votre première classe.</p>
            <a href="{{ route('administration.classes.create') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Créer une classe
            </a>
        </div>
    @endif
</div>
@endsection
