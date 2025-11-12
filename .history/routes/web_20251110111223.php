<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Routes publiques (sans authentification)
|--------------------------------------------------------------------------
*/

// Page de connexion (page par défaut au démarrage)
Route::get('/', [AuthController::class, 'showLoginForm'])->name('index');

// Traitement de la connexion
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Mot de passe oublié - Afficher le formulaire
Route::get('/auth/activation-du-compte', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');

// Mot de passe oublié - Envoyer le lien de réinitialisation
Route::post('/auth/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

// Réinitialisation du mot de passe - Afficher le formulaire
Route::get('/auth/nouveau-mot-de-passe/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');

// Réinitialisation du mot de passe - Traitement
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Routes protégées (nécessitent une authentification)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Routes pour l'administrateur
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard-admin', [AdminDashboardController::class, 'index'])->name('dashboard-admin');

        Route::get('/administration', function () {
            return view('admin.administration');
        })->name('administration');

        Route::get('/calendrier-admin', function () {
            return view('admin.calendrier-admin');
        })->name('calendrier-admin');

        Route::get('/profile', [AdminDashboardController::class, 'profile'])->name('profile');

        // Routes API pour le profil
        Route::post('/profile/update', [AdminDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/upload-photo', [AdminDashboardController::class, 'uploadPhoto'])->name('profile.upload-photo');
        Route::delete('/profile/delete-photo', [AdminDashboardController::class, 'deletePhoto'])->name('profile.delete-photo');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes pour le chef de département
    |--------------------------------------------------------------------------
    */
    Route::prefix('chef-de-departement')->name('chef-de-departement.')->middleware('chef.departement')->group(function () {
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
    | Routes pour les employés
    |--------------------------------------------------------------------------
    */
    Route::prefix('employes')->name('employes.')->middleware('employe')->group(function () {
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

Route::prefix('comon')->name('comon.')->group(function () {
    Route::get('/footer', function () {
        return view('comon.footer');
    })->name('footer');

    Route::get('/header', function () {
        return view('comon.header');
    })->name('header');
});
