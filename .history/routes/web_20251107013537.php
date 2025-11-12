<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;


//Route pour les authentifications

Route::get('/', function () {
    return view('auth.index');
});

// Activation du compte
Route::get('/auth/activation-du-compte', function () {
    return view('auth.activation-du-compte');
});

// Nouveau mot de passe
Route::get('/auth/nouveau-mot-de-passe', function () {
    return view('auth.nouveau-mot-de-passe');
});

//Routes pour les employes
Route::get('/employes/tableau-de-bord-employers',function(){
    return view('employes.tableau-de-bord-employers');
});
Route::get('/employes/calendrier-employers',function(){
    return view('employes.calendrier-employers');
});
Route::get('/employes/conges-employers', function () {
    return view('employes.conges-employers');
});
Route::get('/employes/profile', function () {
    return view('employes.profile');
});
//Route pour le chef-de-departement
Route::get('chef-de-departement/tableau-de-bord-manager',function(){
    return view('chef-de-departement.tableau-de-bord-manager');
});
Route::get('/chef-de-departement/informations',function(){
    return view('chef-de-departement.informations');
});
Route::get('/chef-de-departement/demandes-equipe',function(){
    return view('chef-de-departement.demandes-equipe');
});
Route::get('/chef-de-departement/calendrier-manager',function(){
    return view('chef-de-departement.calendrier-manager');
});
Route::get('chef-de-departement/profile',function(){
    return view('chef-de-departement.profile');
});
//Route pour l'administrateur
Route::get('/admin/administration',function(){
    return view('admin.administration');
});
Route::get('/admin/calendrier-admin',function(){
    return view('admin.calendrier-admin');
});
Route::get('/admin/dashboard-admin',function(){
    return view('admin.dashboard-admin');
});
Route::get('admin/profile',function(){
    return view('admin.profile');
});

//Route pour le comon
Route::get('/network/connexion-perdu',function(){
    return view('network.connexion-perdu');
});
Route::get('/comon/footer',function(){
    return view('comon.footer');
});
Route::get('/comon/header',function(){
    return view('comon.header');
});



// Routes d'authentification
Route::get('/', [AuthController::class, 'showLoginForm'])->name('index');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes de réinitialisation du mot de passe
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// ==================== ROUTES PROTÉGÉES ====================
Route::middleware(['auth'])->group(function () {

    // ========== ROUTES ADMIN ==========
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard-admin');

        // Ajoutez vos autres routes admin ici
    });

    // ========== ROUTES CHEF DE DÉPARTEMENT ==========
    Route::prefix('chef-departement')->name('chef-de-departement.')->group(function () {
        Route::get('/tableau-de-bord', function () {
            return view('chef-departement.tableau-de-bord');
        })->name('tableau-de-bord-manager');

        // Ajoutez vos autres routes chef de département ici
    });

    // ========== ROUTES EMPLOYÉS ==========
    Route::prefix('employes')->name('employes.')->group(function () {
        Route::get('/tableau-de-bord', function () {
            return view('employes.tableau-de-bord');
        })->name('tableau-de-bord-employers');

        // Ajoutez vos autres routes employés ici
    });

});
