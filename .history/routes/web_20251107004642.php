<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


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


// Page de connexion
Route::get('/', [AuthController::class, 'showLoginForm'])->name('index');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Traitement de la connexion
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Mot de passe oublié
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

// Afficher le formulaire de réinitialisation
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');

// Traiter la réinitialisation
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');




Route::get('/debug-password', function () {
    // Récupérer l'utilisateur
    $user = User::where('email', 'sandershulas@gmail.com')->first();

    if (!$user) {
        return response()->json([
            'error' => 'Utilisateur non trouvé'
        ]);
    }

    // Tester plusieurs mots de passe courants
    $testPasswords = [
        'password',
        'admin',
        'admin123',
        '123456',
        'password123',
    ];

    $results = [];
    foreach ($testPasswords as $testPassword) {
        $isValid = Hash::check($testPassword, $user->password);
        $results[] = [
            'password_tested' => $testPassword,
            'is_valid' => $isValid,
        ];
    }

    return response()->json([
        'user_info' => [
            'id' => $user->id_user,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'actif' => $user->actif,
            'current_hash' => $user->password,
            'hash_length' => strlen($user->password),
        ],
        'password_tests' => $results,
        'generate_new_hash' => [
            'for_password' => Hash::make('password'),
            'for_admin' => Hash::make('admin'),
        ],
    ]);
});

// Route pour mettre à jour le mot de passe (ATTENTION: à supprimer après)
Route::get('/reset-admin-password/{password}', function ($password) {
    $user = User::where('email', 'sandershulas@gmail.com')->first();

    if (!$user) {
        return response()->json(['error' => 'Utilisateur non trouvé']);
    }

    $oldHash = $user->password;
    $user->password = $password; // Le cast 'hashed' va hasher automatiquement
    $user->save();

    // Vérifier
    $verification = Hash::check($password, $user->password);

    return response()->json([
        'success' => true,
        'message' => 'Mot de passe mis à jour',
        'old_hash' => $oldHash,
        'new_hash' => $user->password,
        'password_works' => $verification,
    ]);
});
