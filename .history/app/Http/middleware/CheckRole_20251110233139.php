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
            // ✅ Rediriger intelligemment selon le rôle de l'utilisateur
            return $this->redirectToUserDashboard($user, $request);
        }

        return $next($request);
    }

    /**
     * Rediriger l'utilisateur vers son dashboard selon son rôle
     * UNIQUEMENT si ce n'est pas déjà sa page de destination
     */
    private function redirectToUserDashboard($user, $request)
    {
        $roleName = $user->role->nom_role;
        $currentPath = $request->path();

        switch ($roleName) {
            case 'Admin':
                $dashboardRoute = 'employes.tableau-de-bord-employers';
                $dashboardPath = 'employes/tableau-de-bord-employers';
                break;

            case 'chef de departement':
                $dashboardRoute = 'chef-de-departement.tableau-de-bord-manager';
                $dashboardPath = 'chef-de-departement/tableau-de-bord-manager';
                break;

            case 'emplpoyé':
            default:
                $dashboardRoute = 'employes.tableau-de-bord-employers';
                $dashboardPath = 'employes/tableau-de-bord-employers';
                break;
        }

        // ✅ Éviter la boucle : si on est déjà sur le bon dashboard, on arrête
        if (str_contains($currentPath, $dashboardPath)) {
            abort(403, 'Accès refusé à cette section');
        }

        return redirect()->route($dashboardRoute)
            ->with('warning', 'Vous n\'avez pas accès à cette section');
    }
}
