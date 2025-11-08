@extends('layouts.app')

@section('title', 'Rapports Financiers')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header avec filtres -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Rapports Financiers</h1>
                <p class="text-gray-600 text-sm mt-1">Analyse complète des revenus et paiements</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Formulaire de filtres -->
                <form method="GET" class="flex flex-wrap gap-2">
                    <select name="periode"
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="mois" {{ $periode == 'mois' ? 'selected' : '' }}>Ce mois</option>
                        <option value="trimestre" {{ $periode == 'trimestre' ? 'selected' : '' }}>Ce trimestre</option>
                        <option value="annee" {{ $periode == 'annee' ? 'selected' : '' }}>Cette année</option>
                    </select>

                    @if($periode == 'mois' || $periode == 'trimestre')
                    <select name="mois"
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $mois == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    @endif

                    <select name="annee"
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @for($i = now()->year; $i >= now()->year - 5; $i--)
                            <option value="{{ $i }}" {{ $annee == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>

                    <input type="hidden" name="periode" value="{{ $periode }}">
                </form>

                <!-- Bouton Export PDF -->
                <a href="{{ route('comptable.rapports.export-pdf', request()->all()) }}"
                   class="inline-flex items-center justify-center px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors whitespace-nowrap">
                    <i class="fas fa-download mr-2"></i>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Section Exports Personnalisés -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
            <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
                <i class="fas fa-file-export mr-2"></i>
                <span>Exports Personnalisés</span>
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Export par Mois -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-5 border-2 border-blue-200 hover:border-blue-400 transition-all">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white mr-3">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Par Mois</h4>
                            <p class="text-xs text-gray-600">Étudiants payeurs</p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('comptable.rapports.export-etudiants-pdf') }}" class="space-y-2">
                        <input type="hidden" name="type" value="mois">
                        <select name="mois"
                                required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner mois</option>
                            @foreach(['janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'decembre'] as $moisNom)
                                <option value="{{ $moisNom }}">{{ ucfirst($moisNom) }}</option>
                            @endforeach
                        </select>
                        <select name="annee" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                            @for($i = now()->year; $i >= now()->year - 5; $i--)
                                <option value="{{ $i }}" {{ $i == now()->year ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit"
                                class="w-full px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-file-pdf mr-1"></i> Générer
                        </button>
                    </form>
                </div>

                <!-- Export par Classe -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border-2 border-green-200 hover:border-green-400 transition-all">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white mr-3">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Par Classe</h4>
                            <p class="text-xs text-gray-600">Étudiants de la classe</p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('comptable.rapports.export-etudiants-pdf') }}" class="space-y-2">
                        <input type="hidden" name="type" value="classe">
                        <select name="classe_id"
                                required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Sélectionner classe</option>
                            @foreach($classes ?? [] as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->libelle }}</option>
                            @endforeach
                        </select>
                        <select name="annee" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                            @for($i = now()->year; $i >= now()->year - 5; $i--)
                                <option value="{{ $i }}" {{ $i == now()->year ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit"
                                class="w-full px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-file-pdf mr-1"></i> Générer
                        </button>
                    </form>
                </div>

                <!-- Export par Statut -->
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-5 border-2 border-yellow-200 hover:border-yellow-400 transition-all">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center text-white mr-3">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Par Statut</h4>
                            <p class="text-xs text-gray-600">Paiements filtrés</p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('comptable.rapports.export-etudiants-pdf') }}" class="space-y-2">
                        <input type="hidden" name="type" value="statut">
                        <select name="statut"
                                required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                            <option value="">Sélectionner statut</option>
                            <option value="valide">Validés</option>
                            <option value="en_attente">En attente</option>
                            <option value="rejete">Rejetés</option>
                            <option value="annule">Annulés</option>
                        </select>
                        <select name="annee" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                            @for($i = now()->year; $i >= now()->year - 5; $i--)
                                <option value="{{ $i }}" {{ $i == now()->year ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit"
                                class="w-full px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-file-pdf mr-1"></i> Générer
                        </button>
                    </form>
                </div>

                <!-- Export par Mode -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-5 border-2 border-red-200 hover:border-red-400 transition-all">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center text-white mr-3">
                            <i class="fas fa-credit-card text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Par Mode</h4>
                            <p class="text-xs text-gray-600">Mode de paiement</p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('comptable.rapports.export-etudiants-pdf') }}" class="space-y-2">
                        <input type="hidden" name="type" value="mode">
                        <select name="mode_paiement"
                                required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            <option value="">Sélectionner mode</option>
                            <option value="espece">Espèce</option>
                            <option value="virement">Virement</option>
                            <option value="wave">Wave</option>
                            <option value="orange_money">Orange Money</option>
                        </select>
                        <select name="annee" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                            @for($i = now()->year; $i >= now()->year - 5; $i--)
                                <option value="{{ $i }}" {{ $i == now()->year ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit"
                                class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-file-pdf mr-1"></i> Générer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques Générales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenus Totaux -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Revenus Totaux</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($statistiques['revenus_totaux'], 0, ',', ' ') }}</p>
                        <small class="text-gray-500">FCFA</small>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-3xl text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paiements Validés -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Paiements Validés</p>
                        <p class="text-2xl font-bold text-green-600">{{ $statistiques['nombre_paiements_valides'] }}</p>
                        <small class="text-gray-500">Taux: {{ $statistiques['taux_validation'] }}%</small>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-3xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- En Attente -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">En Attente</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $statistiques['nombre_paiements_attente'] }}</p>
                        <small class="text-gray-500">À traiter</small>
                    </div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-3xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenu Moyen -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Revenu Moyen</p>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($statistiques['revenus_moyens_etudiant'], 0, ',', ' ') }}</p>
                        <small class="text-gray-500">Par étudiant</small>
                    </div>
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-graduate text-3xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique d'évolution -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-line text-blue-500 mr-2"></i>
                Évolution des Revenus (12 derniers mois)
            </h3>
        </div>
        <div class="p-6">
            <div class="h-80 md:h-96">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Analyse par Type de Frais -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-pie-chart text-purple-500 mr-2"></i>
                Répartition par Type de Frais
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Graphique -->
                <div class="h-80">
                    <canvas id="typeFraisChart"></canvas>
                </div>
                <!-- Stats -->
                <div class="space-y-4">
                    <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800">Frais d'Inscription</span>
                            <span class="text-sm text-gray-600">{{ $analyseTypeFrais['inscription']['pourcentage'] }}%</span>
                        </div>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($analyseTypeFrais['inscription']['montant'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800">Frais de Mensualité</span>
                            <span class="text-sm text-gray-600">{{ $analyseTypeFrais['mensualite']['pourcentage'] }}%</span>
                        </div>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($analyseTypeFrais['mensualite']['montant'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                    </div>

                    <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800">Frais de Soutenance</span>
                            <span class="text-sm text-gray-600">{{ $analyseTypeFrais['soutenance']['pourcentage'] }}%</span>
                        </div>
                        <p class="text-2xl font-bold text-yellow-600">{{ number_format($analyseTypeFrais['soutenance']['montant'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance par Classe -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-graduation-cap text-green-500 mr-2"></i>
                Performance par Classe
            </h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Classe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Revenus</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nb Paiements</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nb Étudiants</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Revenu Moyen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($analyseClasses as $classe)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $classe['classe'] }}</td>
                            <td class="px-4 py-3 font-semibold text-green-600">{{ number_format($classe['revenus'], 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-gray-700">{{ $classe['nombre_paiements'] }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $classe['nombre_etudiants'] }}</td>
                            <td class="px-4 py-3 font-semibold text-blue-600">{{ number_format($classe['revenu_moyen_etudiant'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Analyse par Mode de Paiement -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-credit-card text-red-500 mr-2"></i>
                Répartition par Mode de Paiement
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Graphique -->
                <div class="h-80">
                    <canvas id="modePaiementChart"></canvas>
                </div>
                <!-- Stats -->
                <div class="space-y-3">
                    @foreach($analyseModePaiement as $index => $mode)
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-gray-400">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800">{{ $mode['mode'] }}</span>
                            <span class="text-sm text-gray-600">{{ $mode['pourcentage'] }}%</span>
                        </div>
                        <p class="text-xl font-bold text-gray-700">{{ number_format($mode['montant'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                        <p class="text-xs text-gray-600 mt-1">{{ $mode['nombre'] }} paiements</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique d'évolution
const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
new Chart(evolutionCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($evolutionTemporelle, 'mois')) !!},
        datasets: [{
            label: 'Revenus (FCFA)',
            data: {!! json_encode(array_column($evolutionTemporelle, 'revenus')) !!},
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                    }
                }
            }
        }
    }
});

// Graphique type de frais
const typeFraisCtx = document.getElementById('typeFraisChart').getContext('2d');
new Chart(typeFraisCtx, {
    type: 'doughnut',
    data: {
        labels: ['Inscription', 'Mensualité', 'Soutenance'],
        datasets: [{
            data: [
                {{ $analyseTypeFrais['inscription']['montant'] }},
                {{ $analyseTypeFrais['mensualite']['montant'] }},
                {{ $analyseTypeFrais['soutenance']['montant'] }}
            ],
            backgroundColor: ['#EF4444', '#3B82F6', '#F59E0B']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});

// Graphique mode de paiement
const modePaiementCtx = document.getElementById('modePaiementChart').getContext('2d');
new Chart(modePaiementCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($analyseModePaiement->pluck('mode')->toArray()) !!},
        datasets: [{
            data: {!! json_encode($analyseModePaiement->pluck('montant')->toArray()) !!},
            backgroundColor: ['#EF4444', '#3B82F6', '#F59E0B', '#10B981', '#8B5CF6']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});
</script>
@endsection
