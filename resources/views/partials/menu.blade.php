@php
    $role = strtolower(Auth::user()->role ?? '');
@endphp

@if ($role === 'admin')
    @include('partials.sidebar-admin')
@elseif ($role === 'comptable')
    @include('partials.sidebar-comptable')
@elseif ($role === 'etudiant')
    @include('partials.sidebar-etudiant')
@else
    <div class="p-4 text-red-500">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        Aucun menu disponible pour ce rôle: {{ $role }}
    </div>
@endif
