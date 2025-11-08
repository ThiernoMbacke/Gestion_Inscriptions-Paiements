@extends('layouts.app')

@section('title', 'Mes Inscriptions')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Mes Inscriptions</h1>
        <div class="text-sm md:text-base text-gray-600 mt-2">
            <a href="{{ route('etudiant.dashboard') }}" class="text-blue-600 hover:text-blue-800">Tableau de bord</a>
            <span class="mx-2">›</span>
            <span>Inscriptions</span>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total inscriptions</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $inscriptions->count() }}</p>
                </div>
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-3xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Validées</p>
                    <p class="text-3xl font-bold text-green-600">{{ $inscriptions->where('statut', 'validée')->count() }}</p>
                </div>
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-3xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">En attente</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $inscriptions->where('statut', 'en_attente')->count() }}</p>
                </div>
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-3xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des inscriptions -->
    @if($inscriptions->count() > 0)
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                    <i class="fas fa-list mr-2"></i>
                    <span>Historique de mes inscriptions</span>
                </h3>
            </div>

            <div class="p-6">
                <!-- Version Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full" aria-label="Liste des inscriptions">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Classe</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date d'inscription</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($inscriptions as $inscription)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-book text-blue-600"></i>
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $inscription->classe->libelle ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    {{ Str::limit($inscription->classe->description ?? 'Non disponible', 50) }}
                                </td>
                                <td class="px-4 py-4">
                                    @switch($inscription->statut)
                                        @case('validée')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Validée
                                            </span>
                                            @break
                                        @case('en_attente')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i> En attente
                                            </span>
                                            @break
                                        @case('rejetée')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i> Rejetée
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ ucfirst($inscription->statut) }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <i class="far fa-calendar-alt text-gray-400 mr-1"></i>
                                    {{ $inscription->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4">
                                    @if($inscription->statut === 'validée')
                                        <a href="{{ route('etudiant.paiements.index', ['motif' => 'inscription', 'inscription_id' => $inscription->id]) }}"
                                           class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium"
                                           title="Effectuer le paiement de cette inscription">
                                            <i class="fas fa-credit-card mr-2"></i> Payer
                                        </a>
                                    @else
                                        <span class="text-sm text-gray-500 italic">Aucune action</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Version Mobile -->
                <div class="md:hidden space-y-4">
                    @foreach($inscriptions as $inscription)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $inscription->classe->libelle ?? 'N/A' }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($inscription->classe->description ?? 'Non disponible', 60) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 mb-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Statut:</span>
                                @switch($inscription->statut)
                                    @case('validée')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Validée
                                        </span>
                                        @break
                                    @case('en_attente')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i> En attente
                                        </span>
                                        @break
                                    @case('rejetée')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Rejetée
                                        </span>
                                        @break
                                @endswitch
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-medium"><i class="far fa-calendar-alt text-gray-400 mr-1"></i>{{ $inscription->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        @if($inscription->statut === 'validée')
                            <a href="{{ route('etudiant.paiements.index', ['motif' => 'inscription', 'inscription_id' => $inscription->id]) }}"
                               class="block w-full text-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium">
                                <i class="fas fa-credit-card mr-2"></i> Effectuer le paiement
                            </a>
                        @else
                            <div class="text-center text-sm text-gray-500 italic py-2">
                                Aucune action disponible
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-md p-12 text-center mb-8">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-graduation-cap text-5xl text-gray-300"></i>
            </div>
            <h4 class="text-xl font-semibold text-gray-700 mb-2">Aucune inscription trouvée</h4>
            <p class="text-gray-600 mb-1">Vous n'êtes inscrit(e) à aucune classe pour le moment.</p>
            <p class="text-gray-500 text-sm">Contactez l'administration pour plus d'informations.</p>
        </div>
    @endif

    <!-- Formulaire de nouvelle inscription -->
    @if($autoriseNouvelleInscription)
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i>
                    <span>Nouvelle inscription</span>
                </h3>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('etudiant.inscriptions.store') }}">
                    @csrf

                    <div class="mb-6">
                        <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Choisissez la classe <span class="text-red-500">*</span>
                        </label>
                        <select id="classe_id"
                                name="classe_id"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                            <option value="">-- Sélectionnez une classe --</option>
                            @foreach($classesDisponibles as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->libelle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                                class="flex-1 sm:flex-none px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Envoyer la demande d'inscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-1 text-xl"></i>
                <div>
                    <p class="text-blue-900 font-medium">Information importante</p>
                    <p class="text-blue-700 text-sm mt-1">
                        Vous ne pouvez pas faire de nouvelle inscription pour le moment. Veuillez attendre la validation de l'administration.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Contact Administration -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
            <h4 class="text-white text-lg font-semibold flex items-center">
                <i class="fas fa-phone mr-2"></i>
                Contact Administration
            </h4>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-phone-alt text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Bureau des inscriptions</p>
                        <p class="text-gray-900 font-semibold">+221 33 XXX XX XX</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-envelope text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Email</p>
                        <p class="text-gray-900 font-semibold">inscriptions@etablissement.sn</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Horaires</p>
                        <p class="text-gray-900 font-semibold">Lun - Ven : 8h - 17h</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Retour au dashboard -->
    <div class="flex justify-center">
        <a href="{{ route('etudiant.dashboard') }}"
           class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour au tableau de bord
        </a>
    </div>
</div>

<style>
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.container > div {
    animation: slideUp 0.5s ease-out;
}

.container > div:nth-child(2) {
    animation-delay: 0.1s;
}

.container > div:nth-child(3) {
    animation-delay: 0.2s;
}
</style>
@endsection
