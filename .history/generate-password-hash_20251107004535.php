<?php
/**
 * Script pour générer un hash de mot de passe
 * Placez ce fichier à la racine de votre projet Laravel
 * Exécutez : php generate-password-hash.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Hash;

echo "╔════════════════════════════════════════╗\n";
echo "║  GÉNÉRATEUR DE HASH DE MOT DE PASSE    ║\n";
echo "╚════════════════════════════════════════╝\n\n";

echo "Entrez le mot de passe à hasher : ";
$password = trim(fgets(STDIN));

if (empty($password)) {
    echo "❌ Mot de passe vide !\n";
    exit(1);
}

echo "\n🔐 Génération du hash...\n\n";

$hash = Hash::make($password);

echo "✅ Hash généré avec succès !\n\n";
echo "Mot de passe : {$password}\n";
echo "Hash : {$hash}\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Requête SQL pour mettre à jour :\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "UPDATE users \n";
echo "SET password = '{$hash}'\n";
echo "WHERE email = 'sandershulas@gmail.com';\n\n";

// Test de vérification
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Test de vérification :\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if (Hash::check($password, $hash)) {
    echo "✅ Vérification réussie ! Le hash fonctionne correctement.\n";
} else {
    echo "❌ Erreur lors de la vérification du hash.\n";
}
