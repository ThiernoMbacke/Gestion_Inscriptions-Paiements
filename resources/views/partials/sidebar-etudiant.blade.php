<nav class="flex-1 overflow-y-auto py-4">
    <div class="space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('etudiant.dashboard') }}"
           class="menu-item {{ request()->routeIs('etudiant.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt w-5 mr-3"></i>
            <span>Tableau de bord</span>
        </a>

        <!-- Mes Inscriptions -->
        <a href="{{ route('etudiant.inscriptions.index') }}"
           class="menu-item {{ request()->routeIs('etudiant.inscriptions*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list w-5 mr-3"></i>
            <span>Mes Inscriptions</span>
        </a>

        <!-- Mes Paiements -->
        <a href="{{ route('etudiant.paiements.index') }}"
           class="menu-item {{ request()->routeIs('etudiant.paiements*') ? 'active' : '' }}">
            <i class="fas fa-credit-card w-5 mr-3"></i>
            <span>Mes Paiements</span>
        </a>
    </div>
</nav>

<!-- Footer User Info -->
<div class="sidebar-footer">
    <div class="flex items-center">
        <i class="fas fa-user-circle text-3xl mr-3 text-white"></i>
        <div class="sidebar-user-info">
            <p class="text-sm font-medium text-white">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</p>
            <p class="text-xs text-gray-300">Étudiant</p>
        </div>
    </div>
</div>
