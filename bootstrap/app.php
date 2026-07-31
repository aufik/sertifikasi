<?php

declare(strict_types=1);

use App\Exceptions\ApiException;
use App\Http\Middleware\AssignRequestId;
use App\Http\Middleware\AuditProductActivity;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LogProductApiActivity;
use App\Http\Middleware\MonitorApiPerformance;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Biar halaman Inertia yang sudah login bisa akses /api/v1 pakai session browser yang sama.
        $middleware->statefulApi();
        $middleware->api(prepend: [AssignRequestId::class]);
        // Log teknis (file) + audit bisnis (DB) lewat middleware saja, 1x per request sukses.
        $middleware->api(append: [LogProductApiActivity::class, MonitorApiPerformance::class, AuditProductActivity::class]);
        $middleware->web(append: [HandleInertiaRequests::class, AuditProductActivity::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return api_error('Validation Error', 40001, 422, $e->errors());
            }
        });
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return api_error('Resource not found', 40401, 404);
            }
        });
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return api_error('Resource not found', 40401, 404);
            }
        });
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return api_error('Unauthenticated', 40101, 401);
            }
        });
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return api_error('Forbidden', 40301, 403);
            }
        });
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return api_error('Forbidden', 40301, 403);
            }
        });
        $exceptions->render(function (ApiException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return api_error($e->getMessage(), $e->internalCode(), $e->httpStatus(), $e->errors());
            }
        });
    })->create();
