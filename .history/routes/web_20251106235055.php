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



Route::get('/test-connexion', function() {
    // Copiez tout le code de l'artifact "Route de test pour déboguer la connexion"

/**
 * ROUTE DE TEST - À ajouter temporairement dans routes/web.php
 * Cette route va nous aider à identifier le problème exact
 */

Route::get('/test-connexion', function() {
    echo "<h1>🔍 TEST DE CONNEXION - DIAGNOSTIC COMPLET</h1>";
    echo "<style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        pre { background: #f0f0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #667eea; color: white; }
    </style>";

    // ====================
    // 1. VÉRIFIER LA BASE DE DONNÉES
    // ====================
    echo "<div class='section'>";
    echo "<h2>📊 1. VÉRIFICATION DE LA BASE DE DONNÉES</h2>";

    try {
        DB::connection()->getPdo();
        echo "<p class='success'>✅ Connexion à la base de données : OK</p>";
    } catch (\Exception $e) {
        echo "<p class='error'>❌ Erreur de connexion à la base de données : " . $e->getMessage() . "</p>";
        return;
    }
    echo "</div>";

    // ====================
    // 2. VÉRIFIER L'UTILISATEUR
    // ====================
    echo "<div class='section'>";
    echo "<h2>👤 2. VÉRIFICATION DE L'UTILISATEUR</h2>";

    $email = 'sandrshulas@gmail.com'; // L'email de votre admin

    try {
        $user = User::where('email', $email)->first();

        if (!$user) {
            echo "<p class='error'>❌ UTILISATEUR NON TROUVÉ avec l'email : {$email}</p>";

            // Lister tous les utilisateurs
            echo "<h3>📋 Liste de tous les utilisateurs dans la base :</h3>";
            $allUsers = User::all();
            if ($allUsers->count() > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Actif</th><th>Role ID</th></tr>";
                foreach ($allUsers as $u) {
                    echo "<tr>";
                    echo "<td>{$u->id_user}</td>";
                    echo "<td>{$u->nom}</td>";
                    echo "<td>{$u->prenom}</td>";
                    echo "<td>{$u->email}</td>";
                    echo "<td>" . ($u->actif ? 'OUI' : 'NON') . "</td>";
                    echo "<td>{$u->role_id}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='error'>❌ Aucun utilisateur dans la base de données !</p>";
            }
            echo "</div>";
            return;
        }

        echo "<p class='success'>✅ Utilisateur trouvé !</p>";
        echo "<table>";
        echo "<tr><th>Champ</th><th>Valeur</th></tr>";
        echo "<tr><td>ID</td><td>{$user->id_user}</td></tr>";
        echo "<tr><td>Nom</td><td>{$user->nom}</td></tr>";
        echo "<tr><td>Prénom</td><td>{$user->prenom}</td></tr>";
        echo "<tr><td>Email</td><td>{$user->email}</td></tr>";
        echo "<tr><td>Matricule</td><td>{$user->matricule}</td></tr>";
        echo "<tr><td>Actif</td><td>" . ($user->actif ? 'OUI' : 'NON') . "</td></tr>";
        echo "<tr><td>Role ID</td><td>{$user->role_id}</td></tr>";
        echo "<tr><td>Département ID</td><td>{$user->departement_id}</td></tr>";
        echo "<tr><td>Hash du mot de passe</td><td>" . substr($user->password, 0, 50) . "...</td></tr>";
        echo "<tr><td>Longueur du hash</td><td>" . strlen($user->password) . " caractères</td></tr>";
        echo "</table>";

    } catch (\Exception $e) {
        echo "<p class='error'>❌ Erreur : " . $e->getMessage() . "</p>";
        return;
    }
    echo "</div>";

    // ====================
    // 3. VÉRIFIER LE RÔLE
    // ====================
    echo "<div class='section'>";
    echo "<h2>🎭 3. VÉRIFICATION DU RÔLE</h2>";

    try {
        if ($user->role) {
            echo "<p class='success'>✅ Rôle trouvé !</p>";
            echo "<table>";
            echo "<tr><th>Champ</th><th>Valeur</th></tr>";
            echo "<tr><td>ID Rôle</td><td>{$user->role->id_role}</td></tr>";
            echo "<tr><td>Nom du rôle</td><td>{$user->role->nom_role}</td></tr>";
            echo "<tr><td>Description</td><td>{$user->role->description}</td></tr>";
            echo "</table>";
        } else {
            echo "<p class='error'>❌ AUCUN RÔLE ASSIGNÉ !</p>";

            // Lister tous les rôles disponibles
            echo "<h3>📋 Rôles disponibles dans la base :</h3>";
            $roles = DB::table('roles')->get();
            if ($roles->count() > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Nom</th><th>Description</th></tr>";
                foreach ($roles as $role) {
                    echo "<tr>";
                    echo "<td>{$role->id_role}</td>";
                    echo "<td>{$role->nom_role}</td>";
                    echo "<td>{$role->description}</td>";
                    echo "</tr>";
                }
                echo "</table>";

                echo "<p class='info'>💡 Pour assigner un rôle, utilisez :</p>";
                echo "<pre>php artisan tinker\n\$user = App\Models\User::find({$user->id_user});\n\$user->role_id = 1; // ID du rôle Admin\n\$user->save();</pre>";
            }
        }
    } catch (\Exception $e) {
        echo "<p class='error'>❌ Erreur lors de la vérification du rôle : " . $e->getMessage() . "</p>";
    }
    echo "</div>";

    // ====================
    // 4. TESTER LE MOT DE PASSE
    // ====================
    echo "<div class='section'>";
    echo "<h2>🔐 4. TEST DU MOT DE PASSE</h2>";

    // CHANGEZ CE MOT DE PASSE PAR CELUI QUE VOUS ESSAYEZ D'UTILISER
    $passwordToTest = 'VotreMotDePasse'; // ⚠️ CHANGEZ CETTE VALEUR

    echo "<p class='info'>📝 Mot de passe testé : <strong>{$passwordToTest}</strong></p>";

    $isValid = Hash::check($passwordToTest, $user->password);

    if ($isValid) {
        echo "<p class='success'>✅ LE MOT DE PASSE EST CORRECT !</p>";
        echo "<p>Vous devriez pouvoir vous connecter avec :</p>";
        echo "<ul>";
        echo "<li>Email : {$user->email}</li>";
        echo "<li>Mot de passe : {$passwordToTest}</li>";
        echo "</ul>";
    } else {
        echo "<p class='error'>❌ LE MOT DE PASSE EST INCORRECT !</p>";
        echo "<p>Le hash dans la base de données ne correspond pas au mot de passe testé.</p>";

        echo "<h3>🔧 Solution : Réinitialiser le mot de passe</h3>";
        echo "<p>Exécutez ces commandes dans Tinker :</p>";
        echo "<pre>php artisan tinker</pre>";
        echo "<pre>\$user = App\Models\User::where('email', '{$email}')->first();\n\$user->password = Hash::make('NouveauMotDePasse123');\n\$user->save();\necho 'Mot de passe mis à jour !';</pre>";

        // Générer un nouveau hash pour comparaison
        $newHash = Hash::make($passwordToTest);
        echo "<h4>Comparaison des hashs :</h4>";
        echo "<table>";
        echo "<tr><th>Type</th><th>Hash</th></tr>";
        echo "<tr><td>Hash actuel (BD)</td><td>" . $user->password . "</td></tr>";
        echo "<tr><td>Hash qui devrait être</td><td>" . $newHash . "</td></tr>";
        echo "</table>";
    }
    echo "</div>";

    // ====================
    // 5. VÉRIFIER LA CONFIGURATION AUTH
    // ====================
    echo "<div class='section'>";
    echo "<h2>⚙️ 5. CONFIGURATION DE L'AUTHENTIFICATION</h2>";

    $authConfig = config('auth.providers.users');
    echo "<table>";
    echo "<tr><th>Paramètre</th><th>Valeur</th></tr>";
    echo "<tr><td>Driver</td><td>{$authConfig['driver']}</td></tr>";
    echo "<tr><td>Model</td><td>{$authConfig['model']}</td></tr>";
    echo "</table>";

    if ($authConfig['model'] !== 'App\Models\User') {
        echo "<p class='error'>⚠️ ATTENTION : Le modèle configuré ne correspond pas !</p>";
    } else {
        echo "<p class='success'>✅ Configuration correcte</p>";
    }
    echo "</div>";

    // ====================
    // 6. TEST FINAL DE CONNEXION
    // ====================
    echo "<div class='section'>";
    echo "<h2>🎯 6. RÉSUMÉ ET RECOMMANDATIONS</h2>";

    $problems = [];

    if (!$user->actif) {
        $problems[] = "Le compte est désactivé (actif = 0)";
    }

    if (!$user->role) {
        $problems[] = "Aucun rôle assigné à l'utilisateur";
    }

    if (!$isValid) {
        $problems[] = "Le mot de passe ne correspond pas au hash dans la BD";
    }

    if (empty($problems)) {
        echo "<p class='success'>🎉 TOUT EST OK ! Vous devriez pouvoir vous connecter.</p>";
        echo "<p>Si la connexion échoue encore, vérifiez les logs Laravel dans <code>storage/logs/laravel.log</code></p>";
    } else {
        echo "<p class='error'>❌ Problèmes détectés :</p>";
        echo "<ul>";
        foreach ($problems as $problem) {
            echo "<li class='error'>{$problem}</li>";
        }
        echo "</ul>";
    }
    echo "</div>";

    echo "<div class='section'>";
    echo "<h2>📝 COMMANDES UTILES</h2>";
    echo "<pre># Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Réinitialiser le mot de passe
php artisan tinker
\$user = App\Models\User::where('email', '{$email}')->first();
\$user->password = Hash::make('NouveauMotDePasse');
\$user->save();

# Assigner un rôle
\$user->role_id = 1;
\$user->save();

# Activer le compte
\$user->actif = true;
\$user->save();</pre>";
    echo "</div>";
});
});
