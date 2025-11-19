<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->actif) {
                Log::warning('Déconnexion automatique - compte inactif', [
                    'user_id' => $user->id_user,
                    'email' => $user->email,
                    'url' => $request->fullUrl()
                ]);

                Auth::logout();
                Session::flush();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
                        'redirect' => route('index')
                    ], 403);
                }

                return redirect()->route('index')
                    ->with('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
            }
        }

        return $next($request);
    }
}
