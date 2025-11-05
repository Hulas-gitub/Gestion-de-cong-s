<?php

use Illuminate\Support\Facades\Route;


//Route pour les authentifications

Route::get('/', function () {
    return view('auth.index');
});

// Activation du compte
Route::get('/activation du compte', function () {
    return view('auth.activation du compte');
});

// Nouveau mot de passe
Route::get('/nouveau-mot-de-passe', function () {
    return view('auth.nouveau mot depasse');
});

//Routes pour les employes
Route::get('/employes/tableau de bord-employers',function(){
    return view('employes.tableau de bord-employes');
});
Route::get('/employes/calendrier-employes',function(){
    return view('employes.tableau de bord-employes');
});
Route::get('/employes/conges-employes', function () {
    return view('employes.conges-employes');
});
//Route pour le chef-de-departement
Route::get('chef-de-departement/tableau de bord-manager',function(){
    return view('chef-de-departement.tableau de bord-manager');
});
Route::get('/chef-de-departement/information',function(){
    return view('chef-de-departement.informations');
});
Route::get('/chef-de-departement/demande-equipe',function(){
    return view('chef-de-departement.demande-equipe');
});
Route::get('/chef-de-departement/calendrier-manager',function(){
    return view('chef-de-departement.calendrier-manager');
});

//Route pour l'administrateur
Route::get('/admin/administration',function(){
    return view('admin.administration');
});
Route::get('/admin/calendrier-admin.blade.php',function(){
    return view('admin.calendrier-admin');
});
Route::get('/admin/dashboard-admin',function(){
    return view('admin.dashboard-admin');
});

//Route pour le comon
Route::get('/comon/sidebar',function(){
    return view('comon.sidebar');
});
Route::get('/comon/footer',function(){
    return view('comon.footer');
});
Route::get('/comon/header',function(){
    return view('comon.header');
});

//Route pour le dossier profile
Route::get('/profile/profile',function(){
    return view('profile.profile');
});
