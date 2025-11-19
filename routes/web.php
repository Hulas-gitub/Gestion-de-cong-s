<?php

use App\Http\Controllers\AdministrationControllers;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DemandesAdminController;
use App\Http\Controllers\DemandesEmployesController;
use App\Http\Controllers\DemandesEquipeController;
use App\Http\Controllers\GestionEquipeController;
use App\Http\Controllers\InformationsController;
use App\Http\Controllers\ProfileUsersController;
use App\Http\Controllers\TypeCongesController;
use App\Http\Controllers\DashboardChefController;
use App\Http\Controllers\DashboardEmployesController;
use App\Http\Controllers\CalendrierEmployesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes d'authentification (Invités uniquement)
|--------------------------------------------------------------------------
*/

Route::middleware(['guest'])->group(function () {
    // Page de connexion (page par défaut)
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('index');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
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

      Route::get('/tableau-de-bord-employers', [DashboardEmployesController::class, 'index'])
            ->name('tableau-de-bord-employers');

        // API Dashboard
        Route::prefix('api/tableau-de-bord')->name('dashboard.api.')->group(function () {

            // Statistiques générales
            Route::get('/statistiques', [DashboardEmployesController::class, 'getStatistiques'])
                ->name('statistiques');

            // Historique des demandes
            Route::get('/historique-demandes', [DashboardEmployesController::class, 'getHistoriqueDemandes'])
                ->name('historique');

            // Détails d'une demande spécifique
            Route::get('/demande/{id}', [DashboardEmployesController::class, 'getDetailsDemande'])
                ->name('detailsDemande');

            // Informations du département
            Route::get('/departement-info', [DashboardEmployesController::class, 'getDepartementInfo'])
                ->name('departementInfo');

            // Gestion des notifications
            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/', [DashboardEmployesController::class, 'getNotifications'])
                    ->name('liste');
                Route::post('/{id}/lire', [DashboardEmployesController::class, 'marquerNotificationLue'])
                    ->name('marquerLue');
            });

            // Gestion des documents
            Route::prefix('documents')->name('documents.')->group(function () {

                // Documents justificatifs des demandes
                Route::get('justificatif/{id}/telecharger', [DashboardEmployesController::class, 'telechargerDocument'])
                    ->name('justificatif.telecharger');
                Route::get('justificatif/{id}/visualiser', [DashboardEmployesController::class, 'visualiserDocument'])
                    ->name('justificatif.visualiser');

                // Attestations de validation
                Route::get('attestation/{id}/telecharger', [DashboardEmployesController::class, 'telechargerAttestation'])
                    ->name('attestation.telecharger');
                Route::get('attestation/{id}/visualiser', [DashboardEmployesController::class, 'visualiserAttestation'])
                    ->name('attestation.visualiser');

                // Documents des notifications
                Route::get('notification/{id}/telecharger', [DashboardEmployesController::class, 'telechargerDocumentNotification'])
                    ->name('notification.telecharger');
                Route::get('notification/{id}/visualiser', [DashboardEmployesController::class, 'visualiserDocumentNotification'])
                    ->name('notification.visualiser');
            });
        });

    // ========== AUTRES PAGES ==========
  Route::get('/calendrier-employers', function () {
    return view('employes.calendrier-employers');
})->name('calendrier-employers');

Route::get('/employe/calendrier', [CalendrierEmployesController::class, 'index'])
    ->name('employe.calendrier');

// API pour récupérer les données de congés
Route::get('/api/employe/conges-data', [CalendrierEmployesController::class, 'getEmployeeLeaveData'])
    ->name('api.employe.conges-data');

// API pour récupérer les détails d'un congé
Route::get('/api/employe/conges/{id}', [CalendrierEmployesController::class, 'getLeaveDetails'])
    ->name('api.employe.conges.details');

// API pour récupérer les congés d'un mois spécifique
Route::get('/api/employe/conges-mois', [CalendrierEmployesController::class, 'getLeavesByMonth'])
    ->name('api.employe.conges.mois');

    // ========== GESTION DU PROFIL EMPLOYÉ ==========
    Route::get('/profile', [ProfileUsersController::class, 'index'])->name('profile');

    Route::prefix('api/profile')->name('api.profile.')->group(function () {
        Route::get('/', [ProfileUsersController::class, 'getProfile'])->name('get');
        Route::put('/update', [ProfileUsersController::class, 'updateProfile'])->name('update');
        Route::post('/photo', [ProfileUsersController::class, 'updatePhoto'])->name('update-photo');
        Route::delete('/photo', [ProfileUsersController::class, 'deletePhoto'])->name('delete-photo');
        Route::post('/password', [ProfileUsersController::class, 'updatePassword'])->name('update-password');
    });

    // ========== GESTION DES CONGÉS ==========
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
| Routes pour le chef de département - DASHBOARD
|--------------------------------------------------------------------------
*/

Route::prefix('chef-de-departement')->name('chef-de-departement.')->group(function () {

    // ========================================
    // Routes Dashboard Chef
    // ========================================
    Route::get('/tableau-de-bord-manager', [DashboardChefController::class, 'index'])->name('tableau-de-bord-manager');

    // API Routes pour le Dashboard (à ajouter APRÈS la route principale)
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/kpis', [DashboardChefController::class, 'getKpiStats'])->name('kpis');
        Route::get('/evolution-demandes', [DashboardChefController::class, 'getEvolutionDemandesParEmploye'])->name('evolution-demandes');
        Route::get('/types-conges', [DashboardChefController::class, 'getTypesConges'])->name('types-conges');
        Route::get('/taux-approbation', [DashboardChefController::class, 'getTauxApprobation'])->name('taux-approbation');
    });

    Route::get('/calendrier-manager', function () {
        return view('chef-de-departement.calendrier-manager');
    })->name('calendrier-manager');

    // ========== GESTION DU PROFIL CHEF DE DÉPARTEMENT ==========
    Route::get('/profile', [ProfileUsersController::class, 'index'])->name('profile');

    Route::prefix('api/profile')->name('api.profile.')->group(function () {
        Route::get('/', [ProfileUsersController::class, 'getProfile'])->name('get');
        Route::put('/update', [ProfileUsersController::class, 'updateProfile'])->name('update');
        Route::post('/photo', [ProfileUsersController::class, 'updatePhoto'])->name('update-photo');
        Route::delete('/photo', [ProfileUsersController::class, 'deletePhoto'])->name('delete-photo');
        Route::post('/password', [ProfileUsersController::class, 'updatePassword'])->name('update-password');
    });

    // ========================================
    // Routes pour le module Informations
    // ========================================
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

        // ========== GESTION DU PROFIL ADMIN ==========
        Route::get('/profile', [ProfileUsersController::class, 'index'])->name('profile');

        Route::prefix('api/profile')->name('api.profile.')->group(function () {
            Route::get('/', [ProfileUsersController::class, 'getProfile'])->name('get');
            Route::put('/update', [ProfileUsersController::class, 'updateProfile'])->name('update');
            Route::post('/photo', [ProfileUsersController::class, 'updatePhoto'])->name('update-photo');
            Route::delete('/photo', [ProfileUsersController::class, 'deletePhoto'])->name('delete-photo');
            Route::post('/password', [ProfileUsersController::class, 'updatePassword'])->name('update-password');
        });

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
