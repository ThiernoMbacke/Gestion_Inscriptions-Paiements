@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Gestion des Utilisateurs</h1>
    <div class="breadcrumb text-sm md:text-base">
        <a href="{{ route('administration.dashboard') }}">Tableau de bord</a> &raquo;
        <span class="text-gray-600">Utilisateurs</span>
    </div>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
    <!-- Total -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <div class="p-4 md:p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <p class="text-3xl md:text-4xl font-bold">{{ $users->count() }}</p>
            </div>
            <h3 class="text-base md:text-lg font-semibold">Total</h3>
            <p class="text-blue-100 text-xs md:text-sm">Tous les utilisateurs</p>
        </div>
    </div>

    <!-- Étudiants -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <div class="p-4 md:p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
                <p class="text-3xl md:text-4xl font-bold">{{ $users->where('role', 'etudiant')->count() }}</p>
            </div>
            <h3 class="text-base md:text-lg font-semibold">Étudiants</h3>
            <p class="text-green-100 text-xs md:text-sm">Comptes étudiants</p>
        </div>
    </div>

    <!-- Admins -->
    <div class="bg-gradient-to-br from-blue-400 to-cyan-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <div class="p-4 md:p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-user-shield text-2xl"></i>
                </div>
                <p class="text-3xl md:text-4xl font-bold">{{ $users->where('role', 'admin')->count() }}</p>
            </div>
            <h3 class="text-base md:text-lg font-semibold">Admins</h3>
            <p class="text-blue-100 text-xs md:text-sm">Administrateurs</p>
        </div>
    </div>

    <!-- Comptables -->
    <div class="bg-gradient-to-br from-teal-500 to-green-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <div class="p-4 md:p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-calculator text-2xl"></i>
                </div>
                <p class="text-3xl md:text-4xl font-bold">{{ $users->where('role', 'comptable')->count() }}</p>
            </div>
            <h3 class="text-base md:text-lg font-semibold">Comptables</h3>
            <p class="text-teal-100 text-xs md:text-sm">Gestionnaires</p>
        </div>
    </div>
</div>

<!-- Actions et filtres -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-4 md:px-6 py-4">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <h2 class="text-white text-xl md:text-2xl font-bold flex items-center">
                <i class="fas fa-list mr-2"></i>
                Liste des Utilisateurs
            </h2>

            <!-- Boutons d'action -->
            <div class="flex flex-wrap gap-2 w-full lg:w-auto">
                <button onclick="downloadTemplate()"
                        class="flex-1 lg:flex-initial inline-flex items-center justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors text-sm">
                    <i class="fas fa-download mr-2"></i>
                    <span class="hidden sm:inline">Modèle</span>
                    <span class="sm:hidden">Excel</span>
                </button>
                <label class="flex-1 lg:flex-initial inline-flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors cursor-pointer text-sm">
                    <i class="fas fa-upload mr-2"></i>
                    <span class="hidden sm:inline">Importer</span>
                    <span class="sm:hidden">Import</span>
                    <input type="file" id="excel-import" accept=".xlsx,.xls" class="hidden" onchange="handleFileUpload(event)">
                </label>
                <a href="{{ route('administration.utilisateurs.create') }}"
                   class="flex-1 lg:flex-initial inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-gray-100 text-blue-600 font-medium rounded-lg transition-colors text-sm">
                    <i class="fas fa-plus mr-2"></i>
                    <span class="hidden sm:inline">Nouveau</span>
                    <span class="sm:hidden">+</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-wrap gap-2">
            <button onclick="filterUsers('all')"
                    class="filter-btn active px-4 py-2 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-users mr-1"></i> Tous
            </button>
            <button onclick="filterUsers('etudiant')"
                    class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-graduation-cap mr-1"></i> Étudiants
            </button>
            <button onclick="filterUsers('admin')"
                    class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-user-shield mr-1"></i> Admins
            </button>
            <button onclick="filterUsers('comptable')"
                    class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-calculator mr-1"></i> Comptables
            </button>
        </div>
    </div>

    @if($users && $users->count() > 0)
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Utilisateur</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Rôle</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="user-row hover:bg-gray-50 transition-colors" data-role="{{ $user->role }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                        @if($user->role === 'etudiant' && $user->etudiant)
                                            <p class="text-xs text-gray-500">{{ $user->etudiant->matricule }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $roleConfig = [
                                        'etudiant' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-graduation-cap'],
                                        'admin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-user-shield'],
                                        'comptable' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'icon' => 'fa-calculator'],
                                    ];
                                    $config = $roleConfig[$user->role] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-user'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                    <i class="fas {{ $config['icon'] }} mr-1"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Vérifié
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <i class="fas fa-clock mr-1"></i> En attente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('administration.utilisateurs.show', $user->id) }}"
                                       class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors"
                                       title="Voir">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('administration.utilisateurs.edit', $user->id) }}"
                                       class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors"
                                       title="Modifier">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('administration.utilisateurs.destroy', $user->id) }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors"
                                                    onclick="return confirm('Supprimer cet utilisateur ?')"
                                                    title="Supprimer">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden divide-y divide-gray-200">
            @foreach($users as $user)
                <div class="user-row p-4 hover:bg-gray-50 transition-colors" data-role="{{ $user->role }}">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-600 truncate">{{ $user->email }}</p>
                            @if($user->role === 'etudiant' && $user->etudiant)
                                <p class="text-xs text-gray-500">{{ $user->etudiant->matricule }}</p>
                            @endif
                        </div>
                        @php
                            $roleConfig = [
                                'etudiant' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-graduation-cap'],
                                'admin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-user-shield'],
                                'comptable' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'icon' => 'fa-calculator'],
                            ];
                            $config = $roleConfig[$user->role] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-user'];
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }} flex-shrink-0">
                            <i class="fas {{ $config['icon'] }}"></i>
                        </span>
                    </div>

                    <div class="flex items-center gap-4 mb-3 text-sm">
                        <span class="text-gray-500">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $user->created_at->format('d/m/Y') }}
                        </span>
                        @if($user->email_verified_at)
                            <span class="text-green-600">
                                <i class="fas fa-check-circle mr-1"></i> Vérifié
                            </span>
                        @else
                            <span class="text-blue-600">
                                <i class="fas fa-clock mr-1"></i> En attente
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <a href="{{ route('administration.utilisateurs.show', $user->id) }}"
                           class="inline-flex items-center justify-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-eye mr-2"></i> Voir
                        </a>
                        <a href="{{ route('administration.utilisateurs.edit', $user->id) }}"
                           class="inline-flex items-center justify-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-edit mr-2"></i> Modifier
                        </a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('administration.utilisateurs.destroy', $user->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors"
                                        onclick="return confirm('Supprimer ?')">
                                    <i class="fas fa-trash mr-2"></i> Suppr.
                                </button>
                            </form>
                        @else
                            <div class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                                <i class="fas fa-ban mr-2"></i> Vous
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-4xl text-gray-400"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-700 mb-2">Aucun utilisateur trouvé</h4>
            <p class="text-gray-500 mb-4">Commencez par créer des comptes utilisateurs</p>
            <a href="{{ route('administration.utilisateurs.create') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i> Créer un utilisateur
            </a>
        </div>
    @endif
</div>

<!-- Inclusion SheetJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// Variables globales
let importData = [];

// Filtrage des utilisateurs
function filterUsers(role) {
    const rows = document.querySelectorAll('.user-row');
    const buttons = document.querySelectorAll('.filter-btn');

    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    rows.forEach(row => {
        if (role === 'all' || row.dataset.role === role) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Télécharger le modèle Excel
function downloadTemplate() {
    const templateData = [{
        'Nom': 'Diop',
        'Prénom': 'Aminata',
        'Email': 'aminata.diop@example.com',
        'Téléphone': '77 123 45 67',
        'Date de naissance': '2000-05-15',
        'Adresse': 'Dakar, Sénégal',
        'Rôle': 'etudiant'
    }];

    const ws = XLSX.utils.json_to_sheet(templateData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Utilisateurs');
    XLSX.writeFile(wb, 'modele_utilisateurs.xlsx');

    showToast('Modèle téléchargé !', 'success');
}

// Gérer l'upload de fichier
function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheetName = workbook.SheetNames[0];
            const sheet = workbook.Sheets[sheetName];
            const jsonData = XLSX.utils.sheet_to_json(sheet);

            if (jsonData.length > 0) {
                showToast(`${jsonData.length} utilisateurs détectés`, 'info');
                // Traiter l'importation ici
            }
        } catch (error) {
            showToast('Erreur de lecture du fichier', 'error');
        }
    };
    reader.readAsArrayBuffer(file);
}

// Toast notifications
function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };

    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => toast.classList.remove('translate-x-full'), 100);
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<style>
.filter-btn {
    @apply bg-white text-gray-700 border-2 border-transparent hover:border-blue-500;
}

.filter-btn.active {
    @apply bg-blue-500 text-white border-blue-500;
}
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
