@extends('layouts.app')

@section('title', 'Détails de l\'inscription')

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête avec breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded shadow-sm">
                <div>
                    <h1 class="h3 mb-2 text-gray-800">Détails de l'inscription</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('administration.dashboard') }}" class="text-decoration-none">
                                    <i class="fas fa-tachometer-alt me-1"></i>Tableau de bord
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('administration.inscriptions.index') }}" class="text-decoration-none">Inscriptions</a>
                            </li>
                            <li class="breadcrumb-item active">Détails</li>
                        </ol>
                    </nav>
                </div>
                <div class="text-end">
                    <!-- Badge de statut principal -->
                    @switch($inscription->statut)
                        @case('valide')
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i>Validée
                            </span>
                            @break
                        @case('en_attente')
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                <i class="fas fa-clock me-1"></i>En attente
                            </span>
                            @break
                        @case('rejete')
                            <span class="badge bg-danger fs-6 px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i>Rejetée
                            </span>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-xl-8 col-lg-7">

            <!-- Informations étudiant -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="card-header-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="mb-0 ms-2">Informations de l'étudiant</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Photo et nom -->
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="profile-avatar me-3">
                                    @if($inscription->etudiant->personne->photo)
                                        <img src="{{ asset('storage/' . $inscription->etudiant->personne->photo) }}"
                                             alt="Photo étudiant" class="rounded-circle shadow"
                                             width="80" height="80" style="object-fit: cover;">
                                    @else
                                        <div class="avatar-placeholder rounded-circle shadow d-flex align-items-center justify-content-center"
                                             style="width: 80px; height: 80px; background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);">
                                            <i class="fas fa-user text-white fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="mb-1 text-primary">
                                        {{ $inscription->etudiant->personne->nom }}
                                        {{ $inscription->etudiant->personne->prenom }}
                                    </h4>
                                    <div class="text-muted">
                                        <i class="fas fa-id-card me-2"></i>{{ $inscription->etudiant->matricule }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-group">
                                <h6 class="info-label">Contact</h6>
                                <div class="info-item">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <span>{{ $inscription->etudiant->personne->email }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <span>{{ $inscription->etudiant->personne->telephone ?? 'Non renseigné' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Détails inscription -->
                        <div class="col-md-6 mb-4">
                            <div class="info-group">
                                <h6 class="info-label">Inscription</h6>
                                <div class="info-item">
                                    <i class="fas fa-chalkboard-teacher text-success me-2"></i>
                                    <strong>{{ $inscription->classe->libelle }}</strong>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar-alt text-info me-2"></i>
                                    <span>{{ $inscription->annee_academique }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="far fa-calendar text-secondary me-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($inscription->date_inscription)->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>

                            @if($inscription->reject_reason)
                                <div class="alert alert-warning border-0 shadow-sm">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Raison du rejet
                                    </h6>
                                    <p class="mb-0 small">{{ $inscription->reject_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section paiements -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="card-header-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h5 class="mb-0 ms-2">Paiements associés</h5>
                        </div>
                        @if($inscription->paiements && $inscription->paiements->count() > 0)
                            <span class="badge bg-white text-info">
                                {{ $inscription->paiements->count() }} paiement(s)
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($inscription->paiements && $inscription->paiements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 py-3">Date</th>
                                        <th class="border-0 py-3">Montant</th>
                                        <th class="border-0 py-3">Type</th>
                                        <th class="border-0 py-3">Statut</th>
                                        <th class="border-0 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inscription->paiements as $paiement)
                                        <tr class="border-light">
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt text-muted me-2"></i>
                                                {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}
                                            </td>
                                            <td class="py-3">
                                                <strong class="text-success">
                                                    {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                                                </strong>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-light text-dark">
                                                    {{ $paiement->type_frais }}
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                @if($paiement->statut === 'valide')
                                                    <span class="badge bg-success-soft text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Validé
                                                    </span>
                                                @elseif($paiement->statut === 'en_attente')
                                                    <span class="badge bg-warning-soft text-warning">
                                                        <i class="fas fa-clock me-1"></i>En attente
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-soft text-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Rejeté
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 text-center">
                                                <a href="{{ route('administration.paiements.show', $paiement) }}"
                                                   class="btn btn-sm btn-outline-primary rounded-pill"
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-credit-card text-muted fa-3x mb-3"></i>
                                <h6 class="text-muted">Aucun paiement</h6>
                                <p class="text-muted small mb-0">Aucun paiement n'a été enregistré pour cette inscription.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="col-xl-4 col-lg-5">
            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-secondary text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="card-header-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h5 class="mb-0 ms-2">Actions rapides</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        @if($inscription->statut === 'en_attente')
                            <form method="POST" action="{{ route('administration.inscriptions.validate', $inscription) }}"
                                  class="d-grid" id="validateForm">
                                @csrf
                                @method('PATCH')
                                <button type="button" class="btn btn-success btn-action"
                                        onclick="if(confirm('Êtes-vous sûr de vouloir valider cette inscription ?')) { this.form.submit(); }">
                                    <i class="fas fa-check-circle me-2"></i>Valider l'inscription
                                </button>
                            </form>

                            <button type="button" class="btn btn-warning btn-action"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times-circle me-2"></i>Rejeter l'inscription
                            </button>
                        @endif

                        <a href="{{ route('administration.etudiants.show', $inscription->etudiant) }}"
                           class="btn btn-outline-primary btn-action">
                            <i class="fas fa-user-graduate me-2"></i>Voir le profil étudiant
                        </a>

                        <a href="{{ route('administration.inscriptions.edit', $inscription) }}"
                           class="btn btn-outline-secondary btn-action">
                            <i class="fas fa-edit me-2"></i>Modifier l'inscription
                        </a>

                        <button type="button" class="btn btn-outline-danger btn-action"
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt me-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Historique amélioré -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <div class="d-flex align-items-center">
                        <div class="card-header-icon text-muted">
                            <i class="fas fa-history"></i>
                        </div>
                        <h5 class="mb-0 ms-2 text-dark">Historique</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="timeline-modern">
                        <div class="timeline-item-modern">
                            <div class="timeline-marker-modern bg-primary">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <div class="timeline-content-modern">
                                <div class="timeline-header">
                                    <h6 class="mb-1 text-primary">Inscription créée</h6>
                                    <span class="timeline-date">
                                        {{ $inscription->created_at->translatedFormat('d F Y \à H:i') }}
                                    </span>
                                </div>
                                <p class="timeline-text">
                                    Par: {{ $inscription->createdBy->name ?? 'Système' }}
                                </p>
                            </div>
                        </div>

                        @if($inscription->statut === 'valide' && $inscription->validated_at)
                            <div class="timeline-item-modern">
                                <div class="timeline-marker-modern bg-success">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <div class="timeline-content-modern">
                                    <div class="timeline-header">
                                        <h6 class="mb-1 text-success">Inscription validée</h6>
                                        <span class="timeline-date">
                                            {{ $inscription->validated_at->translatedFormat('d F Y \à H:i') }}
                                        </span>
                                    </div>
                                    <p class="timeline-text">
                                        Par: {{ $inscription->validatedBy->name ?? 'Système' }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($inscription->statut === 'rejete' && $inscription->rejected_at)
                            <div class="timeline-item-modern">
                                <div class="timeline-marker-modern bg-danger">
                                    <i class="fas fa-times text-white"></i>
                                </div>
                                <div class="timeline-content-modern">
                                    <div class="timeline-header">
                                        <h6 class="mb-1 text-danger">Inscription rejetée</h6>
                                        <span class="timeline-date">
                                            {{ $inscription->rejected_at->translatedFormat('d F Y \à H:i') }}
                                        </span>
                                    </div>
                                    <p class="timeline-text">
                                        Par: {{ $inscription->rejectedBy->name ?? 'Système' }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('administration.inscriptions.destroy', $inscription) }}" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="warning-icon mb-3">
                            <i class="fas fa-exclamation-triangle text-danger fa-3x"></i>
                        </div>
                        <h5>Supprimer définitivement cette inscription ?</h5>
                        <p class="text-muted">Cette action est irréversible et supprimera également tous les paiements associés.</p>
                    </div>

                    <div class="form-check form-check-danger d-flex align-items-center justify-content-center">
                        <input class="form-check-input me-2" type="checkbox" id="confirmDelete" required>
                        <label class="form-check-label" for="confirmDelete">
                            Je confirme vouloir supprimer cette inscription
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <i class="fas fa-trash-alt me-1"></i>Supprimer définitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
@if($inscription->statut !== 'rejete')
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Rejeter l'inscription
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('administration.inscriptions.reject', $inscription) }}" id="rejectForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="reject_reason" class="form-label fw-bold">
                                Raison du rejet <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="reject_reason" name="reject_reason"
                                     rows="4" placeholder="Veuillez indiquer la raison du rejet..." required></textarea>
                            <div class="form-text">
                                Cette raison sera visible par l'étudiant concerné.
                            </div>
                        </div>

                        <div class="alert alert-warning border-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Cette action ne peut pas être annulée. L'étudiant sera notifié par email.
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-warning text-dark">
                            <i class="fas fa-check me-1"></i>Confirmer le rejet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push('styles')
<style>
/* Styles généraux améliorés */
.bg-gradient-primary {
    background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #a29bfe 0%, #6c5ce7 100%);
}

.card {
    border-radius: 15px;
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.card-header-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

/* Info groups */
.info-group {
    margin-bottom: 1.5rem;
}

.info-label {
    color: #6c757d;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.25rem;
}

.info-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

/* Badges personnalisés */
.bg-success-soft {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

.bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

/* Boutons d'action */
.btn-action {
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Timeline moderne */
.timeline-modern {
    position: relative;
}

.timeline-item-modern {
    position: relative;
    padding-left: 60px;
    padding-bottom: 30px;
    border-left: 2px solid #e9ecef;
    margin-left: 20px;
}

.timeline-item-modern:last-child {
    border-left-color: transparent;
    padding-bottom: 0;
}

.timeline-marker-modern {
    position: absolute;
    left: -21px;
    top: 5px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.timeline-content-modern {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    position: relative;
}

.timeline-content-modern::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 15px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid #f8f9fa;
}

.timeline-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 8px;
}

.timeline-date {
    font-size: 0.75rem;
    color: #6c757d;
    background: white;
    padding: 2px 8px;
    border-radius: 15px;
    margin-left: auto;
}

.timeline-text {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
}

/* Empty state */
.empty-state {
    padding: 2rem;
}

/* Modal amélioré */
.modal-content {
    border-radius: 15px;
}

.warning-icon {
    width: 80px;
    height: 80px;
    background: rgba(220, 53, 69, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

/* Responsive */
@media (max-width: 768px) {
    .timeline-item-modern {
        padding-left: 40px;
        margin-left: 10px;
    }

    .timeline-marker-modern {
        width: 30px;
        height: 30px;
        left: -16px;
        font-size: 12px;
    }

    .timeline-content-modern::before {
        left: -6px;
        border-right-width: 6px;
    }

    .card-header-icon {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activer/désactiver le bouton de suppression
    const confirmCheckbox = document.getElementById('confirmDelete');
    const confirmButton = document.getElementById('confirmDeleteBtn');

    if (confirmCheckbox && confirmButton) {
        confirmCheckbox.addEventListener('change', function() {
            confirmButton.disabled = !this.checked;
        });
    }

    // Validation du formulaire de rejet
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            const reason = document.getElementById('reject_reason').value.trim();
            if (!reason) {
                e.preventDefault();
                alert('Veuillez indiquer la raison du rejet.');
                return false;
            }

            if (!confirm('Êtes-vous sûr de vouloir rejeter cette inscription ?')) {
                e.preventDefault();
                return false;
            }

            return true;
        });
    }

    // Animation douce pour les alertes
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-success')) {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            }
        });
    }, 100);
});
</script>
@endpush

@endsection
