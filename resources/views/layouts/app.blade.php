<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestion Scolaire') }} - @yield('title')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Vite Assets (Tailwind + CSS personnalisé) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    @auth
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <h2><span>{{ config('app.name', 'Gestion Scolaire') }}</span></h2>
            </div>
            <nav class="menu">
                @include('partials.menu')
            </nav>
        </div>

        <!-- Overlay pour mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <!-- Bouton hamburger pour mobile -->
                <button class="menu-toggle lg:hidden" id="menuToggle" type="button">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="user-info">
                    <span class="user-name hidden sm:inline">{{ Auth::user()->name }}</span>

                    <!-- Dropdown menu -->
                    <div class="relative inline-block">
                        <button class="user-dropdown-btn" id="userDropdownBtn">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="hidden sm:inline ml-2">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>

                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <a href="{{ route('profile.show') }}" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i> Profil
                            </a>
                            <a href="{{ route('logout') }}" class="dropdown-item"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @else
    <!-- Page de connexion -->
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        @yield('content')
    </div>
    @endauth

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar on mobile
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });

                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }

            // User dropdown toggle
            const dropdownBtn = document.getElementById('userDropdownBtn');
            const dropdownMenu = document.getElementById('userDropdownMenu');

            if (dropdownBtn) {
                dropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    dropdownMenu.classList.remove('show');
                });
            }

            // Active menu item
            const currentPath = window.location.pathname;
            document.querySelectorAll('.menu a').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
