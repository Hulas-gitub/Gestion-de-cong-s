<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Ajouter le middleware pour empêcher le retour arrière
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // ✅ Enregistrer les alias de middleware
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'check.role' => \App\Http\Middleware\CheckRole::class,
            'chef' => \App\Http\Middleware\ChefDepartementMiddleware::class,
            'employe' => \App\Http\Middleware\EmployeMiddleware::class,
            'check.status' => \App\Http\Middleware\CheckUserStatus::class,
            'prevent.back' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
