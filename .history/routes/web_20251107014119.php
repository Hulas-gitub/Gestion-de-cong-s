<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ==================== ROUTES PUBLIQUES (NON AUTHENTIFIÉES) ====================

// Page d'accueil - Formulaire de connexion
Route::get('/', [AuthController::class, 'showLoginForm'])->name('index');

// Routes d'authentification
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes de réinitialisation du mot de passe
Route::get('/auth/mot-de-passe-oublie', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/auth/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/auth/nouveau-mot-de-passe/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/auth/nouveau-mot-de-passe', [AuthController::class, 'resetPassword'])->name('password.update');

// Activation du compte
Route::get('/auth/activation-du-compte/{token}', [AuthController::class, 'activateAccount'])->name('account.activate');

// Page d'erreur de connexion réseau
Route::get('/network/connexion-perdu', function () {
    return view('network.connexion-perdu');
})->name('network.error');

// ==================== ROUTES PROTÉGÉES (AUTHENTIFIÉES) ====================
Route::middleware(['auth'])->group(function () {

    // ========== ROUTES ADMIN ==========
    Route::prefix('admin')->name('admin.')->middleware('role:Administrateur')->group(function () {

        // Tableau de bord
        Route::get('/tableau-de-bord', function () {
            return view('admin.dashboard-admin');
        })->name('dashboard');

        // Administration (gestion des utilisateurs, départements, etc.)
        Route::get('/administration', function () {
            return view('admin.administration');
        })->name('administration');

        // Calendrier
        Route::get('/calendrier', function () {
            return view('admin.calendrier-admin');
        })->name('calendrier');

        // Profil
        Route::get('/profile', function () {
            return view('admin.profile');
        })->name('profile');
    });

    // ========== ROUTES CHEF DE DÉPARTEMENT ==========
    Route::prefix('chef-de-departement')->name('chef-de-departement.')->middleware('role:chef de departement')->group(function () {

        // Tableau de bord
        Route::get('/tableau-de-bord', function () {
            return view('chef-de-departement.tableau-de-bord-manager');
        })->name('dashboard');

        // Demandes de l'équipe
        Route::get('/demandes-equipe', function () {
            return view('chef-de-departement.demandes-equipe');
        })->name('demandes-equipe');

        // Calendrier
        Route::get('/calendrier', function () {
            return view('chef-de-departement.calendrier-manager');
        })->name('calendrier');

        // Informations
        Route::get('/informations', function () {
            return view('chef-de-departement.informations');
        })->name('informations');

        // Profil
        Route::get('/profile', function () {
            return view('chef-de-departement.profile');
        })->name('profile');
    });

    // ========== ROUTES EMPLOYÉS ==========
    Route::prefix('employes')->name('employes.')->middleware('role:Employes')->group(function () {

        // Tableau de bord
        Route::get('/tableau-de-bord', function () {
            return view('employes.tableau-de-bord-employers');
        })->name('dashboard');

        // Mes congés
        Route::get('/conges', function () {
            return view('employes.conges-employers');
        })->name('conges');

        // Calendrier
        Route::get('/calendrier', function () {
            return view('employes.calendrier-employers');
        })->name('calendrier');

        // Profil
        Route::get('/profile', function () {
            return view('employes.profile');
        })->name('profile');
    });

});

// ==================== ROUTE DE TEST EMAIL (À SUPPRIMER EN PRODUCTION) ====================
Route::get('/test-email', function () {
    \Illuminate\Support\Facades\Mail::raw('Test email depuis Laravel', function($message) {
        $message->to(config('mail.from.address'))
                ->subject('Test Email Laravel - Gestion Congés');
    });

    return 'Email de test envoyé ! Vérifiez votre boîte mail.';
})->name('test.email');
