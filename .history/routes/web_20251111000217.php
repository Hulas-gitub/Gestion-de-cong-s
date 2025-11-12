<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdministrationControllers;
use App\Http\Controllers\AccountActivationController;

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

    // Réinitialisation du mot de passe
    Route::get('/auth/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Activation de compte
    Route::get('/activation-compte/{token}', [AccountActivationController::class, 'showActivationForm'])->name('account.activation.form');
    Route::post('/activation-compte', [AccountActivationController::class, 'activate'])->name('account.activation.activate');
    Route::post('/activation-compte/verify-token', [AccountActivationController::class, 'verifyToken'])->name('account.activation.verify');
});

// Déconnexion (accessible à tous)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Routes protégées par authentification
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Routes pour les employés
    |--------------------------------------------------------------------------
    */
    Route::prefix('employes')->name('employes.')->group(function () {
        Route::get('/tableau-de-bord-employers', function () {
            return view('employes.tableau-de-bord-employers');
        })->name('tableau-de-bord-employers');

        Route::get('/calendrier-employers', function () {
            return view('employes.calendrier-employers');
        })->name('calendrier-employers');

        Route::get('/conges-employers', function () {
            return view('employes.conges-employers');
        })->name('conges-employers');

        Route::get('/profile', function () {
            return view('employes.profile');
        })->name('profile');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes pour le chef de département
    |--------------------------------------------------------------------------
    */
    Route::prefix('chef-de-departement')->name('chef-de-departement.')->group(function () {
        Route::get('/tableau-de-bord-manager', function () {
            return view('chef-de-departement.tableau-de-bord-manager');
        })->name('tableau-de-bord-manager');

        Route::get('/informations', function () {
            return view('chef-de-departement.informations');
        })->name('informations');

        Route::get('/demandes-equipe', function () {
            return view('chef-de-departement.demandes-equipe');
        })->name('demandes-equipe');

        Route::get('/calendrier-manager', function () {
            return view('chef-de-departement.calendrier-manager');
        })->name('calendrier-manager');

        Route::get('/profile', function () {
            return view('chef-de-departement.profile');
        })->name('profile');
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

        // API Routes pour la gestion des utilisateurs
        Route::prefix('api')->name('api.')->group(function () {
           // Routes pour les utilisateurs et départements
        Route::prefix('administration')->name('administration.')->group(function () {
            Route::get('/users', [AdministrationControllers::class, 'getUsers'])->name('users.index');
            Route::post('/users', [AdministrationControllers::class, 'store'])->name('users.store');
            Route::get('/users/{id}', [AdministrationControllers::class, 'show'])->name('users.show');
            Route::put('/users/{id}', [AdministrationControllers::class, 'update'])->name('users.update');
            Route::delete('/users/{id}', [AdministrationControllers::class, 'destroy'])->name('users.destroy');
            Route::post('/users/{id}/block', [AdministrationControllers::class, 'block'])->name('users.block');
            Route::post('/users/{id}/unblock', [AdministrationControllers::class, 'unblock'])->name('users.unblock');
            Route::post('/users/{id}/resend-activation', [AdministrationControllers::class, 'resendActivation'])->name('users.resend');

            // Route pour générer le matricule
            Route::get('/generate-matricule', [AdministrationControllers::class, 'generateMatricule'])->name('generate.matricule');

            // Routes pour les départements
            Route::get('/departements', [AdministrationControllers::class, 'getDepartements'])->name('departements.index');
            Route::post('/departements', [AdministrationControllers::class, 'storeDepartement'])->name('departements.store');
            Route::get('/departements/{id}', [AdministrationControllers::class, 'showDepartement'])->name('departements.show');
            Route::put('/departements/{id}', [AdministrationControllers::class, 'updateDepartement'])->name('departements.update');
            Route::delete('/departements/{id}', [AdministrationControllers::class, 'destroyDepartement'])->name('departements.destroy');
        }); });

        // Calendrier
        Route::get('/calendrier-admin', function () {
            return view('admin.calendrier-admin');
        })->name('calendrier-admin');

        // Profil
        Route::get('/profile', function () {
            return view('admin.profile');
        })->name('profile');
    });
});

/*
|--------------------------------------------------------------------------
| Routes communes
|--------------------------------------------------------------------------
*/

Route::prefix('network')->name('network.')->group(function () {
    Route::get('/connexion-perdu', function () {
        return view('network.connexion-perdu');
    })->name('connexion-perdu');
});
