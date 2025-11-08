{{-- resources/views/partials/sidebar-admin.blade.php --}}
<aside class="bg-gray-900 text-white w-64 min-h-screen p-4">
    <h2 class="text-lg font-bold mb-4 flex items-center">
        <i class="fas fa-user-shield mr-2 text-yellow-400"></i> Administration
    </h2>
    <nav class="space-y-2">
        <a href="{{ route('administration.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-700">Dashboard</a>
        <a href="{{ route('administration.utilisateurs.index') }}" class="block px-3 py-2 rounded hover:bg-gray-700">Utilisateurs</a>
        <a href="{{ route('administration.classes.index') }}" class="block px-3 py-2 rounded hover:bg-gray-700">Classes</a>
        <a href="{{ route('administration.inscriptions.index') }}" class="block px-3 py-2 rounded hover:bg-gray-700">Inscriptions</a>
    </nav>
</aside>
