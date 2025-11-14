<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdministrationControllers;
use App\Http\Controllers\DemandesEmployesController;
use App\Http\Controllers\GestionEquipeController;
use App\Http\Controllers\DemandesEquipeController;
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

    Route::get('/informations', function () {
        return view('chef-de-departement.informations');
    })->name('informations');

    Route::get('/calendrier-manager', function () {
        return view('chef-de-departement.calendrier-manager');
    })->name('calendrier-manager');

    Route::get('/profile', function () {
        return view('chef-de-departement.profile');
    })->name('profile');

    // Routes pour la gestion d'équipe
    Route::get('/gestion-equipe', [GestionEquipeController::class, 'index'])->name('gestion-equipe');
    Route::get('/gestion-equipe/employees', [GestionEquipeController::class, 'getEmployees'])->name('gestion-equipe.employees');
    Route::get('/gestion-equipe/employee/{id}', [GestionEquipeController::class, 'getEmployeeDetails'])->name('gestion-equipe.employee-details');
    Route::post('/gestion-equipe/employee/{id}/toggle-block', [GestionEquipeController::class, 'toggleBlockEmployee'])->name('gestion-equipe.toggle-block');
    Route::get('/gestion-equipe/positions', [GestionEquipeController::class, 'getPositions'])->name('gestion-equipe.positions');

    // Routes pour la gestion des demandes de congés de l'équipe
    Route::get('/demandes-equipe', [DemandesEquipeController::class, 'index'])->name('demandes-equipe');
    Route::get('/demandes-equipe/list', [DemandesEquipeController::class, 'getDemandes'])->name('demandes-equipe.list');
    Route::get('/demandes-equipe/{id}', [DemandesEquipeController::class, 'getDemandeDetails'])->name('demandes-equipe.details');
    Route::post('/demandes-equipe/{id}/approuver', [DemandesEquipeController::class, 'approuverDemande'])->name('demandes-equipe.approuver');
    Route::post('/demandes-equipe/{id}/refuser', [DemandesEquipeController::class, 'refuserDemande'])->name('demandes-equipe.refuser');
    Route::post('/demandes-equipe/{id}/revalider', [DemandesEquipeController::class, 'revaliderDemande'])->name('demandes-equipe.revalider');
    Route::delete('/demandes-equipe/{id}', [DemandesEquipeController::class, 'supprimerDemande'])->name('demandes-equipe.supprimer');
    Route::post('/demandes-equipe/{id}/upload-attestation', [DemandesEquipeController::class, 'uploadAttestation'])->name('demandes-equipe.upload-attestation');
    Route::get('/demandes-equipe/{id}/telecharger-document', [DemandesEquipeController::class, 'telechargerDocument'])->name('demandes-equipe.telecharger-document');
    // Routes pour les documents justificatifs
Route::get('/demandes-equipe/{id}/visualiser-document', [DemandesEquipeController::class, 'visualiserDocument'])->name('demandes-equipe.visualiser-document');
Route::get('/demandes-equipe/{id}/telecharger-document', [DemandesEquipeController::class, 'telechargerDocument'])->name('demandes-equipe.telecharger-document');
Route::get('/demandes-equipe/{id}/check-document', [DemandesEquipeController::class, 'checkDocument'])->name('demandes-equipe.check-document');
Route::get('/demandes-equipe/employees', [DemandesEquipeController::class, 'getEmployees'])->name('demandes-equipe.employees');
});
    /*
    |--------------------------------------------------------------------------
    | Routes pour l'administrateur
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard-admin', function () {
            return view('admin.dashboard-admin');
        })->name('dashboard-admin');

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
        });
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
