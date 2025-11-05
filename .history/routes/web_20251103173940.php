<?php

use Illuminate\Support\Facades\Route;


//Route pour les authentifications
Route::get('/',function (){
    return view('auth.index');
});
Route::get('/',function(){
    return view('auth.activation du compte');
});

Route::get('',function(){
    return view('auth.nouveau mot de passe');
});

//Routes pour les employés
Route::get('/employés/tableau de bord-employers',function(){
    return view('employés.tableau de bord-employes');
});
Route::get('/employés/calendrier-employes',function(){
    return view('employés.tableau de bord-employes');
});
Route::get('/emplpoyés/conges-employes', function () {
    return view('employés.conges-employes');
});
//Route pour le chef de departement
Route::get('chef de departement/tableau de bord-manager',function(){
    return view('chef de departement.tableau de bord-manager');
});
Route::get('/chef de departement/information',function(){
    return view('chef de departement.informations');
});
Route::get('/chef de departement/demande-equipe',function(){
    return view('chef de departement.demande-equipe');
});
Route::get('/chef de departement/calendrier-manager',function(){
    return view('chef de departement.calendrier-manager');
});

//Route pour l'administrateur
Route::get('/admin/admnistration',function(){
    return view('admin.administration');
});
Route::get('/admin/calendrier-admin.blade.php',function(){
    return view('admin.calendrier-manager');
});
Route::get('/admin/dashboard-admin',function(){
    return view('admin.dashboard-manager');
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
