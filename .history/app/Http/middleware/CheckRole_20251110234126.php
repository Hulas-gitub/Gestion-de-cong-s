<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur a un rôle
        if (!$user->role) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Votre compte n\'a pas de rôle assigné');
        }

        // Normaliser les noms de rôles pour la comparaison
        $userRole = strtolower(trim($user->role->nom_role));
        $requiredRole = strtolower(trim($role));

        // ✅ Si l'utilisateur a le bon rôle, on le laisse passer
        if ($userRole === $requiredRole) {
            return $next($request);
        }

        // ❌ Sinon, on le redirige vers SON dashboard selon son rôle
        // SANS vérifier à nouveau le rôle (pour éviter la boucle)

        switch ($userRole) {
            case 'admin':
                // Si c'est un admin mais qu'il essaie d'accéder à une autre section
                // On suppose qu'il a accès à /employes/tableau-de-bord-employers
                return redirect('/employes/tableau-de-bord-employers')
                    ->with('warning', 'Accès refusé à cette section');

            case 'chef de departement':
            case 'chef de département':
                return redirect('/chef-de-departement/tableau-de-bord-manager')
                    ->with('warning', 'Accès refusé à cette section');

            case 'emplpoyé':
            case 'employé':
            default:
                return redirect('/employes/tableau-de-bord-employers')
                    ->with('warning', 'Accès refusé à cette section');
        }
    }
}
