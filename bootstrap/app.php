<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e) {

            $prettyModel = 'resource';

            $model = $e->getPrevious()->getModel();

            if (!is_null($model)) {
                $prettyModel = Str::lower(ltrim(preg_replace('/[A-Z]/', ' $0', class_basename($model))));
            }

            return response()->json([
                'message' => "Unable to locate the {$prettyModel} you requested."
            ], 404);
        });
    })->create();
