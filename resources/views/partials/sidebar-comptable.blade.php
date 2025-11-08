<nav class="flex-1 overflow-y-auto py-4">
    <div class="space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('comptable.dashboard') }}"
           class="menu-item {{ request()->routeIs('comptable.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt w-5 mr-3"></i>
            <span>Dashboard</span>
        </a>

        <!-- Paiements en Attente -->
        <a href="{{ route('comptable.paiements.index') }}"
           class="menu-item {{ request()->routeIs('comptable.paiements*') ? 'active' : '' }}">
            <i class="fas fa-credit-card w-5 mr-3"></i>
            <span>Paiements en Attente</span>
        </a>

        <!-- Historique -->
        <a href="{{ route('comptable.historique.index') }}"
           class="menu-item {{ request()->routeIs('comptable.historique*') ? 'active' : '' }}">
            <i class="fas fa-history w-5 mr-3"></i>
            <span>Historique</span>
        </a>
    </div>
</nav>

<!-- Footer User Info -->
<div class="sidebar-footer">
    <div class="flex items-center">
        <i class="fas fa-calculator text-3xl mr-3 text-white"></i>
        <div class="sidebar-user-info">
            <p class="text-sm font-medium text-white">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</p>
            <p class="text-xs text-gray-300">Comptable</p>
        </div>
    </div>
</div>
