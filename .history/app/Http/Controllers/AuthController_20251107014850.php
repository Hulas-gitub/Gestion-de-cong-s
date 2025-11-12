<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route par défaut - Afficher la page de connexion
Route::get('/', [AuthController::class, 'showLoginForm'])->name('index');

// Routes d'authentification
Route::prefix('auth')->group(function () {
    // Connexion
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Mot de passe oublié
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('auth.forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('auth.send-reset-link');

    // Réinitialisation du mot de passe
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('auth.reset-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password.post');
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {

    // Routes Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard-admin');

        // Ajoutez vos autres routes admin ici
    });

    // Routes Chef de département
    Route::prefix('chef-de-departement')->name('chef-de-departement.')->group(function () {
        Route::get('/tableau-de-bord', function () {
            return view('chef-departement.dashboard');
        })->name('tableau-de-bord-manager');

        // Ajoutez vos autres routes chef de département ici
    });

    // Routes Employés
    Route::prefix('employes')->name('employes.')->group(function () {
        Route::get('/tableau-de-bord', function () {
            return view('employes.dashboard');
        })->name('tableau-de-bord-employers');

        // Ajoutez vos autres routes employés ici
    });
});

// ==========================================
// ROUTES DE TEST - À SUPPRIMER EN PRODUCTION
// ==========================================

// Test 1 : Route basique
Route::get('/test', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'Les routes fonctionnent !',
        'timestamp' => now()
    ]);
});

// Test 2 : Connexion à la base de données
Route::get('/test-database', function () {
    try {
        $pdo = DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();

        return response()->json([
            'status' => 'OK',
            'database' => $dbName,
            'message' => 'Connexion à la base de données réussie !'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Test 3 : Récupération de l'utilisateur
Route::get('/test-user', function () {
    try {
        $user = DB::select("SELECT * FROM users WHERE email = 'sandershulas@gmail.com'");

        if (empty($user)) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $user = $user[0];

        return response()->json([
            'status' => 'OK',
            'user' => [
                'id' => $user->id_user,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'actif' => $user->actif,
                'role_id' => $user->role_id,
                'password_length' => strlen($user->password),
                'password_start' => substr($user->password, 0, 20)
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test 4 : Vérification du mot de passe
Route::get('/test-password', function () {
    try {
        $password = 'admin123';
        $user = DB::select("SELECT * FROM users WHERE email = 'sandershulas@gmail.com'");

        if (empty($user)) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $user = $user[0];

        // Test avec Hash::check
        $hashCheck = Hash::check($password, $user->password);

        // Test avec password_verify natif
        $phpCheck = password_verify($password, $user->password);

        // Générer un nouveau hash
        $newHash = Hash::make($password);
        $newHashCheck = Hash::check($password, $newHash);

        return response()->json([
            'status' => 'OK',
            'tests' => [
                'password_tested' => $password,
                'hash_in_db' => substr($user->password, 0, 30) . '...',
                'hash_check' => $hashCheck ? '✅ VALIDE' : '❌ INVALIDE',
                'php_password_verify' => $phpCheck ? '✅ VALIDE' : '❌ INVALIDE',
                'new_hash_test' => $newHashCheck ? '✅ VALIDE' : '❌ INVALIDE',
            ],
            'verdict' => $hashCheck && $phpCheck ? '✅ Le mot de passe est CORRECT !' : '❌ Le mot de passe est INCORRECT !',
            'solution' => !$hashCheck ? "Exécutez cette requête SQL dans phpMyAdmin:\n\nUPDATE users SET password = '{$newHash}' WHERE email = 'sandershulas@gmail.com';" : '✅ Tout est OK côté mot de passe !'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test 5 : Générer un nouveau hash
Route::get('/generate-password', function () {
    $password = 'admin123';
    $hash = Hash::make($password);

    return response()->json([
        'password' => $password,
        'hash' => $hash,
        'sql' => "UPDATE users SET password = '{$hash}' WHERE email = 'sandershulas@gmail.com';"
    ]);
});
