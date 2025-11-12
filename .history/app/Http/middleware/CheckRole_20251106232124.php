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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
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

        // Vérifier si l'utilisateur a le bon rôle
        if ($user->role->nom_role !== $role) {
            // Rediriger vers son propre dashboard
            return $this->redirectToUserDashboard($user);
        }

        return $next($request);
    }

    /**
     * Rediriger l'utilisateur vers son dashboard selon son rôle
     */
    private function redirectToUserDashboard($user)
    {
        $roleName = $user->role->nom_role;

        switch ($roleName) {
            case 'Admin':
                return redirect()->route('admin.dashboard-admin')
                    ->with('warning', 'Accès refusé à cette section');

            case 'chef de departement':
                return redirect()->route('chef-de-departement.tableau-de-bord-manager')
                    ->with('warning', 'Accès refusé à cette section');

            case 'emplpoyé':
            default:
                return redirect()->route('employes.tableau-de-bord-employers')
                    ->with('warning', 'Accès refusé à cette section');
        }
    }
}
