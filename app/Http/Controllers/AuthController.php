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
        // Si déjà connecté, vérifier le statut et rediriger selon le rôle
        if (Auth::check()) {
            $user = Auth::user();

            // Vérifier si le compte est toujours actif
            if (!$user->actif) {
                Log::warning('Tentative d\'accès avec compte inactif', [
                    'user_id' => $user->id_user,
                    'email' => $user->email
                ]);

                // Déconnexion forcée
                Auth::logout();
                Session::flush();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return redirect()->route('index')->with('error', 'Votre compte a été désactivé. Contactez l\'administrateur.');
            }

            return $this->redirectByRole($user);
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
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Veuillez saisir votre adresse email',
            'email.email' => 'Veuillez utiliser une adresse email valide',
            'password.required' => 'Veuillez saisir votre mot de passe',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
        ]);

        try {
            // Rechercher l'utilisateur par email avec sa relation role
            $user = User::with('role')
                        ->where('email', $request->email)
                        ->first();

            // Log pour déboguer
            Log::info('Tentative de connexion', [
                'email' => $request->email,
                'user_found' => $user ? 'oui' : 'non',
                'user_actif' => $user ? ($user->actif ? 'oui' : 'non') : 'n/a'
            ]);

            // Vérifier si l'utilisateur existe
            if (!$user) {
                Log::warning('Utilisateur non trouvé', ['email' => $request->email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ], 401);
            }

            // Vérifier si le compte est actif
            if (!$user->actif) {
                Log::warning('Tentative de connexion avec compte inactif', [
                    'email' => $request->email,
                    'user_id' => $user->id_user
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.'
                ], 403);
            }

            // Vérifier le mot de passe
            if (!Hash::check($request->password, $user->password)) {
                Log::warning('Mot de passe incorrect', ['email' => $request->email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ], 401);
            }

            // Vérifier que l'utilisateur a un rôle
            if (!$user->role) {
                Log::error('Utilisateur sans rôle', ['user_id' => $user->id_user]);
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte n\'a pas de rôle assigné. Contactez l\'administrateur.'
                ], 403);
            }

            // Authentification réussie
            Auth::login($user, $request->remember ?? false);

            // Enregistrer des informations supplémentaires dans la session
            Session::put('user_id', $user->id_user);
            Session::put('user_name', $user->prenom . ' ' . $user->nom);
            Session::put('user_role', $user->role->nom_role);
            Session::put('user_role_id', $user->role->id_role);
            Session::put('user_email', $user->email);
            Session::put('user_matricule', $user->matricule);
            Session::put('departement_id', $user->departement_id);
            Session::put('user_actif', $user->actif); // Pour vérification ultérieure

            Log::info('Connexion réussie', [
                'user_id' => $user->id_user,
                'role' => $user->role->nom_role,
                'actif' => $user->actif
            ]);

            // Déterminer l'URL de redirection selon le rôle
            $redirectUrl = $this->getRedirectUrlByRole($user);

            return response()->json([
                'success' => true,
                'message' => "Bienvenue {$user->prenom} {$user->nom} ! Connexion réussie",
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $user->id_user,
                    'name' => $user->prenom . ' ' . $user->nom,
                    'email' => $user->email,
                    'role' => $user->role->nom_role,
                    'role_id' => $user->role->id_role,
                    'matricule' => $user->matricule,
                    'departement_id' => $user->departement_id,
                    'actif' => $user->actif,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la connexion'
            ], 500);
        }
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
            case 'Admin':
                return route('admin.dashboard-admin');

            case 'chef de departement':
                return route('chef-de-departement.tableau-de-bord-manager');

            case 'emplpoyé':
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
            Auth::logout();
            Session::flush();
            return redirect()->route('index')->with('error', 'Votre compte n\'a pas de rôle assigné');
        }

        $roleName = $user->role->nom_role;

        // Utiliser les noms exacts de la base de données
        switch ($roleName) {
            case 'Admin':
                return redirect()->route('admin.dashboard-admin');

            case 'chef de departement':
                return redirect()->route('chef-de-departement.tableau-de-bord-manager');

            case 'emplpoyé':
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
            // Vérifier si l'utilisateur existe ET est actif
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

            // Mettre à jour le mot de passe - Vérifier que l'utilisateur est actif
            $user = User::where('email', $request->email)
                       ->where('actif', true)
                       ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé ou compte inactif.'
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
    public function logout(Request $request)
{
    $userId = Auth::id();

    Log::info('Déconnexion utilisateur', ['user_id' => $userId]);

    // Déconnexion complète
    Auth::logout();

    // Détruire toutes les données de session
    Session::flush();

    // Invalider la session actuelle
    $request->session()->invalidate();

    // Régénérer le token CSRF pour la sécurité
    $request->session()->regenerateToken();

    // Redirection avec headers pour empêcher le cache
    return redirect()->route('index')
        ->with('success', 'Vous êtes déconnecté')
        ->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
        ]);
}
}
