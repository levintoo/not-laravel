<?php

/** @var Router $router */

use App\Controllers\HomeController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Http;

$router->get('/', [HomeController::class, 'index']);

$router->get('/hello/{name}', function ($name) {
    return new Response("Hello, $name");
});

$router->get('/json', function () {
    $response = Http::acceptJson()
        ->withUserAgent('MyNotLaravelApp/1.0')
        ->get('https://icanhazdadjoke.com/');

    return new JsonResponse($response->json());
});
