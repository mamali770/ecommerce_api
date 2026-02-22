<?php

use App\Http\Controllers\ApiController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$apiController = new ApiController();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) use($apiController): void {
        $exceptions->render(function (ModelNotFoundException $e) use($apiController) {
            return $apiController->responser(null, 404, $e->getMessage());
        });

        $exceptions->render(function (NotFoundHttpException $e) use($apiController) {
            return $apiController->responser(null, 404, $e->getMessage());
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e) use($apiController) {
            return $apiController->responser(null, 422, $e->getMessage());
        });

        $exceptions->render(function (Exception $e) use($apiController) {
            return $apiController->responser(null, 422, $e->getMessage());
        });

        $exceptions->render(function (Error $e) use($apiController) {
            return $apiController->responser(null, 422, $e->getMessage());
        });

        $exceptions->render(function (QueryException $e) use($apiController) {
            return $apiController->responser(null, 500, $e->getMessage());
        });

        $exceptions->render(function (ModelNotFoundException $e) use($apiController) {
            return $apiController->responser(null, 404, $e->getMessage());
        });
    })->create();
