<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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

// Route de déconnexion forcée (TEMPORAIRE - À SUPPRIMER EN PRODUCTION)
Route::get('/force-logout', function() {
    Auth::logout();
    Session::flush();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});

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

        // Calendrier
        Route::get('/calendrier-admin', function () {
            return view('admin.calendrier-admin');
        })->name('calendrier-admin');

        // Profil
        Route::get('/profile', function () {
            return view('admin.profile');
        })->name('profile');

        /*
        |--------------------------------------------------------------------------
        | API Routes pour les utilisateurs
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | API Routes pour les départements
        |--------------------------------------------------------------------------
        */
        Route::prefix('departements')->name('departements.')->group(function () {
            Route::get('/', [AdministrationControllers::class, 'getDepartements'])->name('index');
            Route::post('/', [AdministrationControllers::class, 'storeDepartement'])->name('store');
            Route::get('/{id}', [AdministrationControllers::class, 'showDepartement'])->name('show');
            Route::put('/{id}', [AdministrationControllers::class, 'updateDepartement'])->name('update');
            Route::delete('/{id}', [AdministrationControllers::class, 'destroyDepartement'])->name('destroy');
        });
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
