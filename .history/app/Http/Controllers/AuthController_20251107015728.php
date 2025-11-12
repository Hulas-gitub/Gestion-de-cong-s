<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Services\MailService;

class AuthController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Afficher la page de connexion
     */
    public function showLoginForm()
    {
        // Si déjà connecté, rediriger selon le rôle
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.index');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        // Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Veuillez saisir votre adresse email',
            'email.email' => 'Veuillez utiliser une adresse email valide',
            'password.required' => 'Veuillez saisir votre mot de passe',
        ]);

        try {
            // Rechercher l'utilisateur par email (SANS la condition actif d'abord)
            $user = User::with(['role', 'departement'])
                        ->where('email', $request->email)
                        ->first();

            // Log détaillé pour déboguer
            Log::info('=== TENTATIVE DE CONNEXION ===', [
                'email' => $request->email,
                'user_found' => $user ? 'OUI' : 'NON',
                'password_provided' => $request->password ? 'OUI' : 'NON',
            ]);

            // Vérifier si l'utilisateur existe
            if (!$user) {
                Log::warning('❌ Utilisateur non trouvé', [
                    'email' => $request->email
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ], 401);
            }

            // Vérifier si le compte est actif
            if (!$user->actif) {
                Log::warning('❌ Compte inactif', [
                    'email' => $request->email,
                    'user_id' => $user->id_user
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.'
                ], 403);
            }

            // Log des informations de l'utilisateur trouvé
            Log::info('✅ Utilisateur trouvé', [
                'user_id' => $user->id_user,
                'email' => $user->email,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'actif' => $user->actif,
                'has_password' => !empty($user->password),
                'password_length' => strlen($user->password),
                'password_starts_with' => substr($user->password, 0, 7),
            ]);

            // Vérifier le mot de passe
            $passwordCheck = Hash::check($request->password, $user->password);

            Log::info('🔐 Vérification du mot de passe', [
                'password_check_result' => $passwordCheck ? 'VALIDE' : 'INVALIDE',
                'password_provided_length' => strlen($request->password),
                'hash_in_db' => substr($user->password, 0, 20) . '...',
            ]);

            if (!$passwordCheck) {
                Log::warning('❌ Mot de passe incorrect', [
                    'email' => $request->email,
                    'user_id' => $user->id_user
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ], 401);
            }

            // Vérifier que l'utilisateur a un rôle
            if (!$user->role) {
                Log::error('❌ Utilisateur sans rôle', [
                    'user_id' => $user->id_user,
                    'email' => $user->email,
                    'role_id' => $user->role_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte n\'a pas de rôle assigné. Contactez l\'administrateur.'
                ], 403);
            }

            Log::info('✅ Rôle de l\'utilisateur', [
                'role_id' => $user->role->id_role,
                'role_name' => $user->role->nom_role
            ]);

            // Authentification réussie avec Laravel
            Auth::login($user, $request->remember ?? false);

            // Enregistrer TOUTES les informations de l'utilisateur dans la session
            Session::put('user_id', $user->id_user);
            Session::put('user_nom', $user->nom);
            Session::put('user_prenom', $user->prenom);
            Session::put('user_name', $user->prenom . ' ' . $user->nom);
            Session::put('user_email', $user->email);
            Session::put('user_telephone', $user->telephone);
            Session::put('user_profession', $user->profession);
            Session::put('user_photo_url', $user->photo_url);
            Session::put('user_matricule', $user->matricule);
            Session::put('user_date_embauche', $user->date_embauche);
            Session::put('user_role_id', $user->role->id_role);
            Session::put('user_role', $user->role->nom_role);
            Session::put('departement_id', $user->departement_id);
            Session::put('departement_nom', $user->departement ? $user->departement->nom_departement : null);
            Session::put('solde_conges_annuel', $user->solde_conges_annuel);
            Session::put('conges_pris', $user->conges_pris);
            Session::put('conges_restants', $user->solde_conges_annuel - $user->conges_pris);
            Session::put('user_actif', $user->actif);

            Log::info('🎉 CONNEXION RÉUSSIE', [
                'user_id' => $user->id_user,
                'email' => $user->email,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'role' => $user->role->nom_role,
                'departement' => $user->departement ? $user->departement->nom_departement : 'Aucun'
            ]);

            // Déterminer l'URL de redirection selon le rôle
            $redirectUrl = $this->getRedirectUrlByRole($user);

            return response()->json([
                'success' => true,
                'message' => "Bienvenue {$user->prenom} {$user->nom} ! Connexion réussie",
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $user->id_user,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'name' => $user->prenom . ' ' . $user->nom,
                    'email' => $user->email,
                    'telephone' => $user->telephone,
                    'profession' => $user->profession,
                    'photo_url' => $user->photo_url,
                    'matricule' => $user->matricule,
                    'date_embauche' => $user->date_embauche,
                    'role' => $user->role->nom_role,
                    'role_id' => $user->role->id_role,
                    'departement_id' => $user->departement_id,
                    'departement_nom' => $user->departement ? $user->departement->nom_departement : null,
                    'solde_conges_annuel' => $user->solde_conges_annuel,
                    'conges_pris' => $user->conges_pris,
                    'conges_restants' => $user->solde_conges_annuel - $user->conges_pris,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('💥 ERREUR LORS DE LA CONNEXION', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la connexion'
            ], 500);
        }
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $userId = Session::get('user_id');
        $userName = Session::get('user_name');

        Log::info('👋 Déconnexion', [
            'user_id' => $userId,
            'user_name' => $userName
        ]);

        Auth::logout();
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index')->with('success', 'Déconnexion réussie');
    }

    /**
     * Obtenir l'URL de redirection selon le rôle
     */
    private function getRedirectUrlByRole($user)
    {
        // Vérifier que le rôle existe
        if (!$user->role) {
            Log::error('Tentative de redirection sans rôle', ['user_id' => $user->id_user]);
            return route('index');
        }

        $roleName = $user->role->nom_role;

        // Utiliser les noms exacts de la base de données
        switch ($roleName) {

            case 'chef de departement':
                return route('chef-de-departement.tableau-de-bord-manager');

            case 'emplpoyé': // ATTENTION: Typo dans votre BDD
            default:
                return route('employes.tableau-de-bord-employers');
        }
    }

    /**
     * Rediriger selon le rôle (pour les utilisateurs déjà connectés)
     */
    private function redirectByRole($user)
    {
        // Vérifier que le rôle existe
        if (!$user->role) {
            Log::error('Tentative de redirection sans rôle', ['user_id' => $user->id_user]);
            return redirect()->route('index');
        }

        $roleName = $user->role->nom_role;

        // Utiliser les noms exacts de la base de données
        switch ($roleName) {
            case 'Admin':
                return redirect()->route('admin.dashboard-admin');

            case 'chef de departement':
                return redirect()->route('chef-de-departement.tableau-de-bord-manager');

            case 'emplpoyé': // ATTENTION: Typo dans votre BDD
            default:
                return redirect()->route('employes.tableau-de-bord-employers');
        }
    }

    /**
     * Afficher le formulaire de réinitialisation de mot de passe
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoyer le lien de réinitialisation avec MailService
     */
    public function sendResetLink(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Veuillez saisir votre adresse email',
            'email.email' => 'Veuillez utiliser une adresse email valide',
        ]);

        try {
            // Vérifier si l'utilisateur existe
            $user = User::where('email', $request->email)
                       ->where('actif', true)
                       ->first();

            if (!$user) {
                // Pour des raisons de sécurité, on ne dit pas si l'email existe ou non
                return response()->json([
                    'success' => true,
                    'message' => 'Si cette adresse email existe dans notre système, un lien de réinitialisation a été envoyé.'
                ], 200);
            }

            // Générer un token unique
            $token = Str::random(64);

            // Supprimer les anciens tokens pour cet email
            DB::table('password_resets')->where('email', $request->email)->delete();

            // Sauvegarder le nouveau token
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            // Envoyer l'email avec le MailService
            try {
                $emailSent = $this->mailService->envoyerResetPassword(
                    $user->email,
                    $user->nom,
                    $user->prenom,
                    $token
                );

                if ($emailSent) {
                    Log::info('Email de réinitialisation envoyé', [
                        'email' => $user->email,
                        'user_id' => $user->id_user
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email. Veuillez vérifier votre boîte de réception.'
                    ], 200);
                } else {
                    Log::error('Échec de l\'envoi de l\'email de réinitialisation', [
                        'email' => $user->email
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Une erreur s\'est produite lors de l\'envoi de l\'email. Veuillez réessayer.'
                    ], 500);
                }
            } catch (\Exception $mailException) {
                Log::error('Erreur lors de l\'envoi de l\'email', [
                    'error' => $mailException->getMessage(),
                    'email' => $user->email
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur s\'est produite lors de l\'envoi de l\'email.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de la réinitialisation du mot de passe', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Afficher le formulaire de reset password avec token
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required' => 'Veuillez saisir un nouveau mot de passe',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
        ]);

        try {
            // Vérifier le token
            $resetRecord = DB::table('password_resets')
                            ->where('email', $request->email)
                            ->first();

            if (!$resetRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien de réinitialisation est invalide.'
                ], 400);
            }

            // Vérifier si le token n'a pas expiré (48 heures)
            $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
            if ($createdAt->addHours(48)->isPast()) {
                DB::table('password_resets')->where('email', $request->email)->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien de réinitialisation a expiré.'
                ], 400);
            }

            // Vérifier le token
            if (!Hash::check($request->token, $resetRecord->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien de réinitialisation est invalide.'
                ], 400);
            }

            // Mettre à jour le mot de passe
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé.'
                ], 404);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            // Supprimer le token utilisé
            DB::table('password_resets')->where('email', $request->email)->delete();

            Log::info('Mot de passe réinitialisé avec succès', [
                'user_id' => $user->id_user,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la réinitialisation du mot de passe', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite.'
            ], 500);
        }
    }
}
