<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Afficher la page de connexion
     */
    public function showLoginForm()
    {
        // Si déjà connecté, rediriger selon le rôle
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
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

        // Rechercher l'utilisateur par email
        $user = User::where('email', $request->email)
                    ->where('actif', true)
                    ->first();

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        // Authentification réussie
        Auth::login($user, $request->remember ?? false);

        // Enregistrer des informations supplémentaires dans la session
        Session::put('user_id', $user->id_user);
        Session::put('user_name', $user->prenom . ' ' . $user->nom);
        Session::put('user_role', $user->role->nom_role ?? 'Employes');
        Session::put('user_email', $user->email);
        Session::put('user_matricule', $user->matricule);
        Session::put('departement_id', $user->departement_id);

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
                'role' => $user->role->nom_role ?? 'Employes',
                'matricule' => $user->matricule,
            ]
        ], 200);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Déconnexion réussie');
    }

    /**
     * Obtenir l'URL de redirection selon le rôle
     */
    private function getRedirectUrlByRole($user)
    {
        $roleName = $user->role->nom_role ?? 'Employes';

        switch ($roleName) {
            case 'Administrateur':
                return route('admin.dashboard');

            case 'chef de departement':
                return route('manager.dashboard');

            case 'Employes':
            default:
                return route('employee.dashboard');
        }
    }

    /**
     * Rediriger selon le rôle (pour les utilisateurs déjà connectés)
     */
    private function redirectByRole($user)
    {
        $roleName = $user->role->nom_role ?? 'Employes';

        switch ($roleName) {
            case 'Administrateur':
                return redirect()->route('admin.dashboard');

            case 'chef de departement':
                return redirect()->route('manager.dashboard');

            case 'Employes':
            default:
                return redirect()->route('employee.dashboard');
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
     * Envoyer le lien de réinitialisation
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Veuillez saisir votre adresse email',
            'email.email' => 'Veuillez utiliser une adresse email valide',
            'email.exists' => 'Cette adresse email n\'existe pas dans notre système',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Générer un token de réinitialisation
            $token = \Str::random(64);

            // Sauvegarder le token dans la base de données (tu devras créer une table password_resets)
            \DB::table('password_resets')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Envoyer l'email de réinitialisation (à implémenter avec MailService)
            // $mailService = new \App\Services\MailService();
            // $mailService->envoyerResetPassword($user->email, $user->prenom, $token);

            return response()->json([
                'success' => true,
                'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Une erreur s\'est produite'
        ], 500);
    }
}
