@extends('layouts.app')

@section('title', 'Tableau de bord Étudiant')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="dashboard-header bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl shadow-lg p-8 mb-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Tableau de bord</h1>
                <p class="text-blue-100 text-lg">
                    Bonjour <span class="font-semibold">{{ Auth::user()->name }}</span>, bienvenue sur votre espace étudiant.
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-3">
                    <p class="text-sm text-blue-100">Année académique</p>
                    <p class="text-xl font-bold">
                        @php
                            $currentMonth = date('n');
                            $currentYear = date('Y');
                            // L'année académique commence en septembre (mois 9)
                            if ($currentMonth >= 9) {
                                $anneeDebut = $currentYear;
                                $anneeFin = $currentYear + 1;
                            } else {
                                $anneeDebut = $currentYear - 1;
                                $anneeFin = $currentYear;
                            }
                            echo $anneeDebut . '-' . $anneeFin;
                        @endphp
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Card Inscriptions -->
        <div class="card bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group">
            <div class="card-inner p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="card-icon bg-blue-100 text-blue-600 p-4 rounded-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                    <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">Actif</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Inscriptions</h3>
                <p class="text-gray-600 mb-4 text-sm">Gérez vos inscriptions aux cours et consultez votre emploi du temps</p>
                <a href="{{ route('etudiant.inscriptions.index') }}"
                   class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800 transition-colors group">
                    Accéder
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        <!-- Card Paiements -->
        <div class="card bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group">
            <div class="card-inner p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="card-icon bg-green-100 text-green-600 p-4 rounded-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                    <span class="bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">En ligne</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Paiements</h3>
                <p class="text-gray-600 mb-4 text-sm">Consultez et effectuez vos paiements de frais de scolarité</p>
                <a href="{{ route('etudiant.paiements.index') }}"
                   class="inline-flex items-center text-green-600 font-semibold hover:text-green-800 transition-colors group">
                    Accéder
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        <!-- Card Profil -->
        <div class="card bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group">
            <div class="card-inner p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="card-icon bg-purple-100 text-purple-600 p-4 rounded-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-user-cog text-2xl"></i>
                    </div>
                    <span class="bg-purple-50 text-purple-700 text-xs font-semibold px-3 py-1 rounded-full">Perso</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Mon Profil</h3>
                <p class="text-gray-600 mb-4 text-sm">Modifiez vos informations personnelles et de connexion</p>
                <a href="{{ route('etudiant.profil.edit') }}"
                   class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-800 transition-colors group">
                    Modifier
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Notifications Section -->
    <div class="notifications bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-bell text-yellow-500 mr-3"></i>
                Notifications récentes
                @php
                    $totalNotifications = 0;
                    if(isset($dernieresInscriptions)) $totalNotifications += $dernieresInscriptions->count();
                    if(isset($derniersPaiements)) $totalNotifications += $derniersPaiements->count();
                @endphp
                @if($totalNotifications > 0)
                    <span class="ml-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                        {{ $totalNotifications }}
                    </span>
                @endif
            </h3>
        </div>

        @php
            $hasNotifications = (isset($dernieresInscriptions) && $dernieresInscriptions->count() > 0) ||
                               (isset($derniersPaiements) && $derniersPaiements->count() > 0);
        @endphp

        @if($hasNotifications)
            <ul class="space-y-4">
                {{-- Notifications des inscriptions --}}
                @if(isset($dernieresInscriptions))
                    @foreach($dernieresInscriptions as $inscription)
                        <li class="notification-item flex items-start p-4
                            @if($inscription->statut === 'valide') bg-blue-50 hover:bg-blue-100
                            @elseif($inscription->statut === 'en_attente') bg-yellow-50 hover:bg-yellow-100
                            @else bg-red-50 hover:bg-red-100
                            @endif
                            rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-10 h-10
                                @if($inscription->statut === 'valide') bg-blue-500
                                @elseif($inscription->statut === 'en_attente') bg-yellow-500
                                @else bg-red-500
                                @endif
                                rounded-full flex items-center justify-center text-white mr-4">
                                @if($inscription->statut === 'valide')
                                    <i class="fas fa-check"></i>
                                @elseif($inscription->statut === 'en_attente')
                                    <i class="fas fa-clock"></i>
                                @else
                                    <i class="fas fa-times"></i>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <p class="text-gray-800 font-medium">
                                    @if($inscription->statut === 'valide')
                                        Inscription validée
                                    @elseif($inscription->statut === 'en_attente')
                                        Inscription en attente
                                    @else
                                        Inscription refusée
                                    @endif
                                </p>
                                <p class="text-gray-600 text-sm">
                                    @if($inscription->statut === 'valide')
                                        Votre inscription pour {{ $inscription->cours->nom ?? 'le cours' }} a été validée
                                    @elseif($inscription->statut === 'en_attente')
                                        Votre inscription pour {{ $inscription->cours->nom ?? 'le cours' }} est en cours de traitement
                                    @else
                                        Votre inscription pour {{ $inscription->cours->nom ?? 'le cours' }} a été refusée
                                    @endif
                                </p>
                                <span class="text-xs text-gray-500 mt-1 inline-block">
                                    {{ $inscription->updated_at->diffForHumans() }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                @endif

                {{-- Notifications des paiements --}}
                @if(isset($derniersPaiements))
                    @foreach($derniersPaiements as $paiement)
                        <li class="notification-item flex items-start p-4
                            @if($paiement->statut === 'valide') bg-green-50 hover:bg-green-100
                            @elseif($paiement->statut === 'en_attente') bg-orange-50 hover:bg-orange-100
                            @else bg-red-50 hover:bg-red-100
                            @endif
                            rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-10 h-10
                                @if($paiement->statut === 'valide') bg-green-500
                                @elseif($paiement->statut === 'en_attente') bg-orange-500
                                @else bg-red-500
                                @endif
                                rounded-full flex items-center justify-center text-white mr-4">
                                @if($paiement->statut === 'valide')
                                    <i class="fas fa-check-circle"></i>
                                @elseif($paiement->statut === 'en_attente')
                                    <i class="fas fa-hourglass-half"></i>
                                @else
                                    <i class="fas fa-exclamation-triangle"></i>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <p class="text-gray-800 font-medium">
                                    @if($paiement->statut === 'valide')
                                        Paiement confirmé
                                    @elseif($paiement->statut === 'en_attente')
                                        Paiement en attente
                                    @else
                                        Paiement échoué
                                    @endif
                                </p>
                                <p class="text-gray-600 text-sm">
                                    @if($paiement->statut === 'valide')
                                        Votre paiement de {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA a été validé
                                    @elseif($paiement->statut === 'en_attente')
                                        Votre paiement de {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA est en cours de vérification
                                    @else
                                        Votre paiement de {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA a échoué
                                    @endif
                                </p>
                                <span class="text-xs text-gray-500 mt-1 inline-block">
                                    {{ $paiement->updated_at->diffForHumans() }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                @endif
            </ul>
        @else
            <div class="text-center py-12">
                <i class="fas fa-bell-slash text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">Aucune notification pour le moment</p>
                <p class="text-gray-400 text-sm mt-2">Vous serez notifié ici de toute nouvelle information</p>
            </div>
        @endif
    </div>
</div>

<style>
    /* Animation d'entrée pour les cartes */
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

    .card {
        animation: slideUp 0.5s ease-out;
    }

    .card:nth-child(2) {
        animation-delay: 0.1s;
    }

    .card:nth-child(3) {
        animation-delay: 0.2s;
    }

    /* Effet de hover pour les cartes */
    .card:hover {
        transform: translateY(-5px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
        }

        .dashboard-header h1 {
            font-size: 1.75rem;
        }
    }
</style>
@endsection
