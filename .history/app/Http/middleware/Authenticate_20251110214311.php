<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
  public function handle(Request $request, Closure $next): Response
{
    // Autoriser la page login
    if ($request->routeIs('index') || $request->routeIs('login')) {
        return $next($request);
    }

    if (!Auth::check()) {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }
        return redirect()->route('index');
    }

    return $next($request);
}
  }

