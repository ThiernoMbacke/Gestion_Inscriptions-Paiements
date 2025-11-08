<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\ComptableController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PersonneController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaiementOrangeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


// Routes pour l'administration (backend)
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Gestion des inscriptions
    Route::resource('inscriptions', InscriptionController::class)->except(['create', 'edit']);

    // Inscription groupée
    Route::get('inscriptions/inscrire-multiple', [InscriptionController::class, 'inscrireMultiple'])
         ->name('inscriptions.inscrireMultiple');
    Route::post('inscriptions/inscrire-multiple', [InscriptionController::class, 'storeMultiple'])
         ->name('inscriptions.storeMultiple');

    // Validation des inscriptions
    // Changez les noms de routes pour qu'ils correspondent à votre vue
Route::patch('inscriptions/{inscription}/validate', [InscriptionController::class, 'validateInscription'])
     ->name('administration.inscriptions.validate'); // ← Ajoutez "administration."

Route::patch('inscriptions/{inscription}/reject', [InscriptionController::class, 'rejectInscription'])
     ->name('administration.inscriptions.reject'); // ← Ajoutez "administration."

Route::get('inscriptions/export', [InscriptionController::class, 'export'])
     ->name('administration.inscriptions.export'); // ← Ajoutez "administration."

    // Détails pour formulaire
    Route::get('inscriptions/getformdetails', [InscriptionController::class, 'getformdetails'])
         ->name('inscriptions.getformdetails');

    // Gestion des classes
    Route::resource('classes', ClasseController::class)->except(['create', 'edit']);
    Route::get('classes/getformdetails', [ClasseController::class, 'getformdetails'])
         ->name('classes.getformdetails');

    // Gestion des utilisateurs
    Route::resource('users', UserController::class)->except(['create', 'edit']);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
         ->name('users.reset-password');

    // Gestion des personnes
    Route::resource('personnes', PersonneController::class)->except(['create', 'edit']);
    Route::post('personnes/import', [PersonneController::class, 'import'])->name('personnes.import');
    Route::get('personnes/export', [PersonneController::class, 'export'])->name('personnes.export');
    Route::get('personnes/getformdetails', [PersonneController::class, 'getformdetails'])
         ->name('personnes.getformdetails');
});

// Routes pour le tableau de bord d'administration
Route::prefix('administration')->name('administration.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdministrationController::class, 'dashboard'])->name('dashboard');
    Route::get('/utilisateurs', [AdministrationController::class, 'utilisateurs'])->name('utilisateurs.index');

    // Gestion des inscriptions
    Route::get('/inscriptions', [AdministrationController::class, 'inscriptions'])->name('inscriptions.index');
    Route::get('/inscriptions/create', [InscriptionController::class, 'create'])->name('inscriptions.create');
    Route::post('/inscriptions', [InscriptionController::class, 'store'])->name('inscriptions.store');
    Route::get('/inscriptions/{inscription}/edit', [InscriptionController::class, 'edit'])->name('inscriptions.edit');
    Route::put('/inscriptions/{inscription}', [InscriptionController::class, 'update'])->name('inscriptions.update');
    Route::get('/inscriptions/inscrire-multiple', [InscriptionController::class, 'inscrireMultiple'])
         ->name('inscriptions.inscrireMultiple');
    Route::post('/inscriptions/inscrire-multiple', [InscriptionController::class, 'storeMultiple'])
         ->name('inscriptions.storeMultiple');
    Route::get('/inscriptions/{inscription}', [InscriptionController::class, 'show'])->name('inscriptions.show');
    Route::patch('/inscriptions/{inscription}/validate', [InscriptionController::class, 'validateInscription'])
         ->name('inscriptions.validate');
    Route::patch('/inscriptions/{inscription}/reject', [InscriptionController::class, 'rejectInscription'])
         ->name('inscriptions.reject');
    Route::get('/inscriptions/export', [InscriptionController::class, 'export'])
         ->name('inscriptions.export');
    Route::delete('/inscriptions/{inscription}', [InscriptionController::class, 'destroy'])->name('inscriptions.destroy');

    // Gestion des classes
    Route::get('/classes', [AdministrationController::class, 'classes'])->name('classes.index');
    Route::get('classes/create', [AdministrationController::class, 'createClasse'])->name('classes.create');

    // Création d'utilisateur
    Route::get('utilisateurs/create', [AdministrationController::class, 'createUtilisateur'])->name('utilisateurs.create');
});

// Routes pour les paiements
Route::prefix('paiement')->name('paiement.')->group(function() {
    // Paiement Stripe
    Route::post('/stripe/process', [PaiementController::class, 'processStripePayment'])->name('stripe.process');
    Route::get('/stripe/success', [PaiementController::class, 'stripeSuccess'])->name('stripe.success');
    Route::get('/stripe/cancel', [PaiementController::class, 'stripeCancel'])->name('stripe.cancel');

    // Paiement Orange Money
    Route::post('/orange/initier', [PaiementController::class, 'initierOrangeMoney'])->name('orange.initier');
    Route::post('/orange/initiate', [PaiementOrangeController::class, 'initiate'])
         ->name('orange.initiate')
         ->middleware('auth');
    Route::get('/orange/callback', [PaiementOrangeController::class, 'callback'])
         ->name('callback.orange');
    Route::get('/orange/cancel', [PaiementOrangeController::class, 'cancel'])
         ->name('cancel.orange');

    // Détails et reçus
    Route::get('/{paiement}/details', [PaiementController::class, 'details'])->name('details');
    Route::get('/{paiement}/recu', [PaiementController::class, 'recu'])->name('recu');
    Route::post('/{paiement}/email', [PaiementController::class, 'sendEmail'])->name('email');
    Route::post('/{paiement}/{action}', [PaiementController::class, 'validerRejeter'])->name('validerRejeter');
});
/**
* Détails et reçus (pluriel)
*Route::prefix('paiements')->name('paiements.')->group(function () {
    *Route::get('/{paiement}/details', [PaiementController::class, 'details'])->name('details');
   * Route::get('/{paiement}/recu', [PaiementController::class, 'recu'])->name('recu');
  *  Route::post('/{paiement}/email', [PaiementController::class, 'sendEmail'])->name('email');
 *   Route::post('/{paiement}/{action}', [PaiementController::class, 'validerRejeter'])->name('validerRejeter');
*});
*/


// Routes pour l'espace étudiant
Route::middleware(['auth', 'verified', 'role:etudiant'])->prefix('etudiant')->name('etudiant.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [EtudiantController::class, 'dashboard'])->name('dashboard');

    // Gestion des inscriptions
    Route::get('/inscriptions', [EtudiantController::class, 'inscriptions'])->name('inscriptions.index');
    Route::get('/mes-inscriptions', [InscriptionController::class, 'mesInscriptions'])
         ->name('inscriptions.mes_inscriptions');

    // Gestion des paiements
    Route::get('/paiements', [EtudiantController::class, 'paiements'])->name('paiements.index');
    Route::get('/mes-paiements', [PaiementController::class, 'studentPayments'])
         ->name('paiements.historique');
    Route::get('/paiements/{paiement}/details', [PaiementController::class, 'etudiantPaiementDetails'])
         ->name('paiements.details');
    Route::get('/paiements/{paiement}/recu', [PaiementController::class, 'etudiantPaiementRecu'])
         ->name('paiements.recu');

    // Profil étudiant
    Route::get('/profil', [EtudiantController::class, 'editProfil'])->name('profil.edit');
    Route::patch('/profil', [EtudiantController::class, 'updateProfil'])->name('profil.update');

    // Vérification du statut de paiement
    Route::get('/paiements/{paiementId}/status', [PaiementOrangeController::class, 'checkStatus'])
     ->name('paiements.check.status');

});

// Routes pour l'espace comptable
Route::middleware(['auth', 'verified', 'role:comptable'])->prefix('comptable')->name('comptable.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [ComptableController::class, 'dashboard'])->name('dashboard');

    // Paiements en attente - AVANT le resource !
    Route::get('/paiements/en-attente', [ComptableController::class, 'paiements'])
         ->name('paiements.en_attente');

    // Gestion des paiements
    Route::resource('paiements', PaiementController::class)->except(['create', 'edit']);

    // Historique des paiements
    Route::get('/historique', [PaiementController::class, 'historique'])
         ->name('historique.index');
    Route::get('/historique/export/excel', [PaiementController::class, 'exportExcel'])
         ->name('historique.export.excel');

    // Rapports financiers
    Route::get('/rapports', [PaiementController::class, 'rapportsFinanciers'])
         ->name('rapports.index');
    Route::get('/rapports/export-pdf', [PaiementController::class, 'exportRapportPDF'])
         ->name('rapports.export-pdf');

    // Validation des paiements
    Route::patch('/paiements/{paiement}/validate', [PaiementController::class, 'validatePayment'])
         ->name('paiements.validate');
    Route::patch('/paiements/{paiement}/reject', [PaiementController::class, 'rejectPayment'])
         ->name('paiements.reject');

    // Détails des paiements
    Route::get('/paiements/{id}/details', [PaiementController::class, 'showDetails'])
         ->name('paiements.details');

    // API pour les actions AJAX
    Route::prefix('api')->group(function () {
        Route::post('/paiements/{paiement}/valider', [ComptableController::class, 'validerPaiement'])
               ->name('api.paiements.valider');
        Route::post('/paiements/{paiement}/rejeter', [ComptableController::class, 'rejeterPaiement'])
               ->name('api.paiements.rejeter');
    });
});

// Routes pour le profil utilisateur
Route::middleware(['auth', 'verified'])->group(function () {
    // Édition du profil
    Route::get('/profile', [ProfileController::class, 'edit'])
         ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
         ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
         ->name('profile.destroy');

    // Photo de profil
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])
         ->name('profile.photo.update');

    // Mon profil (vue simplifiée)
    Route::get('/mon-profil', [PersonneController::class, 'showProfile'])
         ->name('profile.show');
    Route::post('/mon-profil/photo', [PersonneController::class, 'updatePhoto'])
         ->name('profile.photo');
});

// Routes d'authentification
require __DIR__.'/auth.php';

// Route racine
Route::get('/', function () {
    return view('welcome');
});

// Déconnexion personnalisée
Route::post('/logout-web', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout-web');


Route::prefix('administration')->name('administration.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/classes', [AdministrationController::class, 'classes'])->name('classes.index');
    Route::get('/classes/create', [AdministrationController::class, 'createClass'])->name('classes.create');
    Route::get('/classes/{classe}', [AdministrationController::class, 'showClass'])->name('classes.show');
    Route::get('/classes/{classe}/edit', [AdministrationController::class, 'editClass'])->name('classes.edit');
    Route::post('/classes', [AdministrationController::class, 'storeClass'])->name('classes.store');
    Route::put('/classes/{classe}', [AdministrationController::class, 'updateClass'])->name('classes.update');
    Route::delete('/classes/{classe}', [AdministrationController::class, 'destroyClass'])->name('classes.destroy');
});

// Routes pour la gestion des utilisateurs
// Routes pour la gestion des utilisateurs
Route::prefix('administration/utilisateurs')->name('administration.utilisateurs.')->group(function () {

    // Liste des utilisateurs - GET
    Route::get('/', [UserController::class, 'index'])->name('index');

    // Formulaire de création - GET
    Route::get('/create', [UserController::class, 'create'])->name('create');

    // Création d'utilisateur - POST
    Route::post('/', [UserController::class, 'store'])->name('store');

    // Détails utilisateur - GET
    Route::get('/{id}', [UserController::class, 'show'])->name('show');

    // Formulaire modification - GET
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');

    // Mise à jour utilisateur - PUT
    Route::put('/{id}', [UserController::class, 'update'])->name('update');

    // Suppression utilisateur - DELETE
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');

    // Données formulaire (API) - GET
    Route::get('/getformdetails', [UserController::class, 'getformdetails'])->name('getformdetails');
});

// Routes d'authentification
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Routes pour le comptable
Route::prefix('comptable')->name('comptable.')->group(function () {

    // Route pour les paiements en attente
    //Route::get('/paiements/en-attente', [ComptableController::class, 'paiementsEnAttente'])
     //   ->name('paiements.en_attente');

    // Route pour l'historique des paiements
    Route::get('/paiements/historique', [ComptableController::class, 'historiquePaiements'])
        ->name('paiements.historique');
       // nouvelles routes pour nouveau type de tri
    Route::get('/rapports/export-etudiants-pdf', [ComptableController::class, 'exportEtudiantsPDF'])->name('rapports.export-etudiants-pdf');


    // ✅ AJOUTER LA ROUTE POUR LE REÇU
    Route::get('/paiements/{paiement}/recu', [PaiementController::class, 'recu'])
        ->name('paiements.recu');

    // Route pour les rapports
    //Route::get('/rapports', [ComptableController::class, 'rapports'])
      //  ->name('rapports.index');

    // ✅ CORRIGER LA ROUTE API
    Route::post('/api/paiements/{paiement}/valider', [ComptableController::class, 'validerPaiement'])
        ->name('api.paiements.valider');
});

// Dans routes/web.php
Route::prefix('administration')->name('administration.')->group(function () {
    Route::resource('etudiants', EtudiantController::class);
    // Ou spécifiquement :
    Route::get('etudiants/{etudiant}', [EtudiantController::class, 'show'])
         ->name('etudiants.show');
});

// paiement page dans le mail de validation inscription
//Route::get('/paiement', [PaiementController::class, 'page'])->name('paiement.page');
//Route::get('/comptable/rapports', [ComptableController::class, 'rapports'])
//    ->name('comptable.rapports.index');
