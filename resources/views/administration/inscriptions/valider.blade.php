@extends('layouts.app')

@section('title', 'Valider une Inscription')

@section('content')
<div class="page-header">
    <h1>Validation de l'Inscription</h1>
    <div class="breadcrumb">
        <a href="{{ route('administration.dashboard') }}">Tableau de bord</a> &raquo;
        <a href="{{ route('administration.inscriptions.index') }}">Inscriptions</a> &raquo; Valider
    </div>
</div>

<table class="table" style="max-width:600px; margin: 0 auto 2rem auto;">
    <tr>
        <th>Étudiant</th>
        <td>
            <strong>{{ $inscription->etudiant->personne->nom ?? 'N/A' }} {{ $inscription->etudiant->personne->prenom ?? '' }}</strong><br>
            <small>Matricule : {{ $inscription->etudiant->matricule }}</small>
        </td>
    </tr>
    <tr>
        <th>Classe</th>
        <td>{{ $inscription->classe->libelle }}</td>
    </tr>
    <tr>
        <th>Année académique</th>
        <td>{{ $inscription->annee_academique }}</td>
    </tr>
    <tr>
        <th>Date inscription</th>
        <td>{{ $inscription->date_inscription ? \Carbon\Carbon::parse($inscription->date_inscription)->format('d/m/Y') : 'N/A' }}</td>
    </tr>
    <tr>
        <th>Statut actuel</th>
        <td>
            @switch($inscription->statut)
                @case('validee')
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Validée
                    </span>
                    @break
                @case('en_attente')
                    <span class="badge badge-warning">
                        <i class="fas fa-clock"></i> En attente
                    </span>
                    @break
                @case('rejeté')
                    <span class="badge badge-danger">
                        <i class="fas fa-times-circle"></i> Rejetée
                    </span>
                    @break
                @default
                    <span class="badge" style="background-color:#999; color:white;">
                        {{ ucfirst($inscription->statut) }}
                    </span>
            @endswitch
        </td>
    </tr>
</table>

@if($inscription->statut === 'en_attente')
<div style="text-align:center; margin-bottom: 2rem;">
    <form method="POST" action="{{ route('admin.inscriptions.validate', $inscription) }}" style="display:inline-block;">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-primary" title="Valider l'inscription">
            <i class="fas fa-check"></i> Valider
        </button>
    </form>

    <form method="POST" action="{{ route('admin.inscriptions.reject', $inscription) }}" style="display:inline-block; margin-left: 15px;">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-danger" title="Rejeter l'inscription" onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette inscription ?')">
            <i class="fas fa-times"></i> Rejeter
        </button>
    </form>
</div>
@endif

<div style="text-align:center;">
    <a href="{{ route('admin.inscriptions.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>
@endsection
