<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdministrationControllers;
use App\Http\Controllers\DemandesEmployesController;
use App\Http\Controllers\GestionEquipeController;
use App\Http\Controllers\DemandesEquipeController;
use App\Http\Controllers\DemandesAdminController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\InformationsController;
use App\Http\Controllers\TypeCongesController;
/*
|--------------------------------------------------------------------------
| Routes d'authentification (Invités uniquement)
|--------------------------------------------------------------------------
*/

Route::middleware(['guest'])->group(function () {
    // Page de connexion (page par défaut)
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('index');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Mot de passe oublié
    Route::get('/auth/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/auth/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/auth/nouveau-mot-de-passe', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    // Réinitialisation du mot de passe
    Route::get('/auth/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

});

// Déconnexion (accessible à tous)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Routes protégées par authentification + vérification statut actif
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.status'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Routes pour les employés
    |--------------------------------------------------------------------------
    */
    Route::prefix('employes')->name('employes.')->group(function () {

        // Pages simples
        Route::get('/tableau-de-bord-employers', function () {
            return view('employes.tableau-de-bord-employers');
        })->name('tableau-de-bord-employers');

        Route::get('/calendrier-employers', function () {
            return view('employes.calendrier-employers');
        })->name('calendrier-employers');

        Route::get('/profile', function () {
            return view('employes.profile');
        })->name('profile');

        Route::get('/informations', function (){
            return view('employes.informations');
        })->name('informations');

        // Gestion des congés
        Route::get('/conges-employers', [DemandesEmployesController::class, 'index'])->name('conges-employers');
        Route::get('/conges-employers/data', [DemandesEmployesController::class, 'getData'])->name('conges-employers.data');
        Route::post('/conges/store', [DemandesEmployesController::class, 'store'])->name('conges.store');
        Route::delete('/conges/{id}/supprimer', [DemandesEmployesController::class, 'supprimer'])->name('conges.supprimer');
        Route::match(['POST', 'PUT'], '/conges/{id}/modifier', [DemandesEmployesController::class, 'modifier'])->name('conges.modifier');
        Route::post('/conges/{id}/retour-anticipe', [DemandesEmployesController::class, 'retourAnticipe'])->name('conges.retourAnticipe');

        // Documents justificatifs
        Route::get('/conges/document/{id}', [DemandesEmployesController::class, 'telechargerDocument'])->name('conges.telecharger');
        Route::get('/conges/document/{id}/visualiser', [DemandesEmployesController::class, 'visualiserDocument'])->name('conges.visualiser');
        Route::post('/conges/{id}/relancer', [DemandesEmployesController::class, 'relancer'])->name('conges.relancer');
    });

/*
|--------------------------------------------------------------------------
| Routes pour le chef de département
|--------------------------------------------------------------------------
*/
Route::prefix('chef-de-departement')->name('chef-de-departement.')->group(function () {

    // Pages simples
    Route::get('/tableau-de-bord-manager', function () {
        return view('chef-de-departement.tableau-de-bord-manager');
    })->name('tableau-de-bord-manager');

    Route::get('/calendrier-manager', function () {
        return view('chef-de-departement.calendrier-manager');
    })->name('calendrier-manager');

    Route::get('/profile', function () {
        return view('chef-de-departement.profile');
    })->name('profile');


    // ========================================
    // Routes pour le module Informations
    // ========================================
    // IMPORTANT: Les routes spécifiques AVANT les routes avec paramètres
    Route::get('/informations/get-notes', [InformationsController::class, 'getNotes'])->name('informations.get-notes');
    Route::post('/informations/store', [InformationsController::class, 'store'])->name('informations.store');
    Route::get('/informations/download/{id}', [InformationsController::class, 'download'])->name('informations.download');
    Route::get('/informations/show/{id}', [InformationsController::class, 'show'])->name('informations.show');
    Route::post('/informations/update/{id}', [InformationsController::class, 'update'])->name('informations.update');
    Route::delete('/informations/delete/{id}', [InformationsController::class, 'destroy'])->name('informations.destroy');
    Route::get('/informations', [InformationsController::class, 'index'])->name('informations');


    // ========================================
    // Routes pour la gestion d'équipe
    // ========================================
    Route::get('/gestion-equipe', [GestionEquipeController::class, 'index'])->name('gestion-equipe');
    Route::get('/gestion-equipe/employees', [GestionEquipeController::class, 'getEmployees'])->name('gestion-equipe.employees');
    Route::get('/gestion-equipe/employee/{id}', [GestionEquipeController::class, 'getEmployeeDetails'])->name('gestion-equipe.employee-details');
    Route::post('/gestion-equipe/employee/{id}/toggle-block', [GestionEquipeController::class, 'toggleBlockEmployee'])->name('gestion-equipe.toggle-block');
    Route::get('/gestion-equipe/positions', [GestionEquipeController::class, 'getPositions'])->name('gestion-equipe.positions');


    // ========================================
    // Routes pour la gestion des demandes de congés de l'équipe
    // ========================================
    Route::get('/demandes-equipe', [DemandesEquipeController::class, 'index'])->name('demandes-equipe');
    Route::get('/demandes-equipe/list', [DemandesEquipeController::class, 'getDemandes'])->name('demandes-equipe.list');
    Route::get('/demandes-equipe/employees', [DemandesEquipeController::class, 'getEmployees'])->name('demandes-equipe.employees');
    Route::get('/demandes-equipe/{id}', [DemandesEquipeController::class, 'getDemandeDetails'])->name('demandes-equipe.details');
    Route::post('/demandes-equipe/{id}/approuver', [DemandesEquipeController::class, 'approuverDemande'])->name('demandes-equipe.approuver');
    Route::post('/demandes-equipe/{id}/refuser', [DemandesEquipeController::class, 'refuserDemande'])->name('demandes-equipe.refuser');
    Route::post('/demandes-equipe/{id}/revalider', [DemandesEquipeController::class, 'revaliderDemande'])->name('demandes-equipe.revalider');
    Route::delete('/demandes-equipe/{id}', [DemandesEquipeController::class, 'supprimerDemande'])->name('demandes-equipe.supprimer');
    Route::post('/demandes-equipe/{id}/upload-attestation', [DemandesEquipeController::class, 'uploadAttestation'])->name('demandes-equipe.upload-attestation');

    // Routes pour les documents justificatifs
    Route::get('/demandes-equipe/{id}/visualiser-document', [DemandesEquipeController::class, 'visualiserDocument'])->name('demandes-equipe.visualiser-document');
    Route::get('/demandes-equipe/{id}/telecharger-document', [DemandesEquipeController::class, 'telechargerDocument'])->name('demandes-equipe.telecharger-document');
    Route::get('/demandes-equipe/{id}/check-document', [DemandesEquipeController::class, 'checkDocument'])->name('demandes-equipe.check-document');
});

/*
|--------------------------------------------------------------------------
| Routes pour l'administrateur - Gestion des demandes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

     Route::get('/dashboard-admin', [DashboardAdminController::class, 'index'])->name('dashboard-admin');

    // API Routes pour le Dashboard
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/kpi-stats', [DashboardAdminController::class, 'getKpiStats'])->name('kpi-stats');
        Route::get('/evolution-conges', [DashboardAdminController::class, 'getEvolutionConges'])->name('evolution-conges');
        Route::get('/employes-departement', [DashboardAdminController::class, 'getEmployesParDepartement'])->name('employes-departement');
        Route::get('/types-conges', [DashboardAdminController::class, 'getTypesConges'])->name('types-conges');
        Route::get('/taux-absenteisme', [DashboardAdminController::class, 'getTauxAbsenteisme'])->name('taux-absenteisme');
        Route::get('/vue-ensemble', [DashboardAdminController::class, 'getVueEnsemble'])->name('vue-ensemble');
    });

    // Administration des utilisateurs
    Route::get('/administration', [AdministrationControllers::class, 'index'])->name('administration');

    // Calendrier
    Route::get('/calendrier-admin', function () {
        return view('admin.calendrier-admin');
    })->name('calendrier-admin');

    // Profil
    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('profile');

    // ========== GESTION DES DEMANDES DE CONGÉS ==========

    // Page principale de gestion des demandes
    Route::get('/demandes-admin', [DemandesAdminController::class, 'index'])->name('demandes-admin');

    // API Routes pour les demandes
    Route::prefix('api/demandes')->name('api.demandes.')->group(function () {

        // Récupérer toutes les demandes avec filtres
        Route::get('/', [DemandesAdminController::class, 'getDemandes'])->name('index');

        // Récupérer tous les départements
        Route::get('/departements', [DemandesAdminController::class, 'getAllDepartements'])->name('departements');

        // Récupérer les employés par département
        Route::get('/departements/{id}/employees', [DemandesAdminController::class, 'getEmployeesByDepartement'])->name('employees');

        // Récupérer les détails d'une demande
        Route::get('/{id}', [DemandesAdminController::class, 'getDemandeDetails'])->name('show');

        // Actions individuelles
        Route::post('/{id}/approuver', [DemandesAdminController::class, 'approuverDemande'])->name('approve');
        Route::post('/{id}/refuser', [DemandesAdminController::class, 'refuserDemande'])->name('reject');
        Route::post('/{id}/revalider', [DemandesAdminController::class, 'revaliderDemande'])->name('revalidate');
        Route::delete('/{id}', [DemandesAdminController::class, 'supprimerDemande'])->name('destroy');

        // Actions groupées
        Route::post('/approuver-multiples', [DemandesAdminController::class, 'approuverMultiples'])->name('approve-multiple');
        Route::post('/refuser-multiples', [DemandesAdminController::class, 'refuserMultiples'])->name('reject-multiple');

        // Gestion des documents
        Route::post('/{id}/upload-attestation', [DemandesAdminController::class, 'uploadAttestation'])->name('upload-attestation');
        Route::get('/{id}/telecharger-document', [DemandesAdminController::class, 'telechargerDocument'])->name('download-document');
        Route::get('/{id}/visualiser-document', [DemandesAdminController::class, 'visualiserDocument'])->name('view-document');
        Route::get('/{id}/check-document', [DemandesAdminController::class, 'checkDocument'])->name('check-document');
    });

    // API Routes pour la gestion des utilisateurs et départements
    Route::prefix('api')->name('api.')->group(function () {

        // Routes pour les utilisateurs
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdministrationControllers::class, 'getUsers'])->name('index');
            Route::post('/', [AdministrationControllers::class, 'store'])->name('store');
            Route::get('/{id}', [AdministrationControllers::class, 'show'])->name('show');
            Route::put('/{id}', [AdministrationControllers::class, 'update'])->name('update');
            Route::delete('/{id}', [AdministrationControllers::class, 'destroy'])->name('destroy');
            Route::post('/{id}/block', [AdministrationControllers::class, 'block'])->name('block');
            Route::post('/{id}/unblock', [AdministrationControllers::class, 'unblock'])->name('unblock');
            Route::post('/{id}/resend-activation', [AdministrationControllers::class, 'resendActivation'])->name('resend-activation');
            Route::post('/generate-matricule', [AdministrationControllers::class, 'generateMatricule'])->name('generate-matricule');
        });

        // Routes pour les départements
        Route::prefix('departements')->name('departements.')->group(function () {
            Route::get('/', [AdministrationControllers::class, 'getDepartements'])->name('index');
            Route::post('/', [AdministrationControllers::class, 'storeDepartement'])->name('store');
            Route::get('/{id}', [AdministrationControllers::class, 'showDepartement'])->name('show');
            Route::put('/{id}', [AdministrationControllers::class, 'updateDepartement'])->name('update');
            Route::delete('/{id}', [AdministrationControllers::class, 'destroyDepartement'])->name('destroy');
        });

        // ========== ROUTES POUR LES TYPES DE CONGÉS ==========
        Route::prefix('types-conges')->name('types-conges.')->group(function () {
            Route::get('/', [TypeCongesController::class, 'getTypesConges'])->name('index');
            Route::post('/', [TypeCongesController::class, 'store'])->name('store');
            Route::get('/{id}', [TypeCongesController::class, 'show'])->name('show');
            Route::put('/{id}', [TypeCongesController::class, 'update'])->name('update');
            Route::delete('/{id}', [TypeCongesController::class, 'destroy'])->name('destroy');
        });
    });

    // ========== PAGE DE GESTION DES TYPES DE CONGÉS ==========
    Route::get('/types-conges', [TypeCongesController::class, 'index'])->name('types-conges.page');
});
});

/*
|--------------------------------------------------------------------------
| Routes communes (accessibles sans authentification)
|--------------------------------------------------------------------------
*/

Route::prefix('network')->name('network.')->group(function () {
    Route::get('/connexion-perdu', function () {
        return view('network.connexion-perdu');
    })->name('connexion-perdu');
});
