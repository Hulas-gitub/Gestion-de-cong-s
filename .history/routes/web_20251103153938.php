<?php

use Illuminate\Support\Facades\Route;

// Page de connexion par défaut
Route::get('/', function () {
    return view('auth.index');
});

// Routes pour l'authentification
Route::get('/activation-compte', function () {
    return view('auth.activation du compte'); // renommer le fichier pour enlever espaces si possible
});
Route::get('/nouveau-mot-de-passe', function () {
    return view('auth.nouveau mot de passe'); // idem, enlever les espaces
});

// Routes pour les employés
Route::get('/employes/tableau-de-bord', function () {
    return view('employés.tableau de bord-employers'); // attention aux noms de fichiers
});
Route::get('/employes/calendrier', function () {
    return view('employés.calendrier-employers');
});
Route::get('/employes/conges', function () {
    return view('employés.conges-employers');
});

// Routes pour le chef de département
Route::get('/chef-de-departement/tableau-de-bord', function () {
    return view('chef de departement.tableau de bord-manager');
});
Route::get('/chef-de-departement/informations', function () {
    return view('chef de departement.informations');
});
Route::get('/chef-de-departement/demande-equipe', function () {
    return view('chef de departement.demande-equipe');
});
Route::get('/chef-de-departement/calendrier', function () {
    return view('chef de departement.calendrier-manager');
});

// Routes pour l'administrateur
Route::get('/admin/administration', function () {
    return view('admin.administration');
});
Route::get('/admin/calendrier', function () {
    return view('admin.calendrier-admin');
});
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard-admin');
});

// Routes pour les fichiers communs
Route::get('/comon/sidebar', function () {
    return view('comon.sidebar');
});
Route::get('/comon/footer', function () {
    return view('comon.footer');
});
Route::get('/comon/header', function () {
    return view('comon.header');
});

// Route pour le profil
Route::get('/profile', function () {
    return view('profile.profile');
});
