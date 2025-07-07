<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcherContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\CallableDispatcher;
use Illuminate\Routing\Contracts\CallableDispatcher as CallableDispatcherContract;
use Illuminate\Routing\Router;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewFinderInterface;

// --------------------------------------------------------
// Setup Service Container and Event Dispatcher
// --------------------------------------------------------
$container = new Container;
$events = new Dispatcher($container);

// Register container for all facades
$allowedFacades = ['Http'];

foreach ($allowedFacades as $facade) {
    $class = "Illuminate\\Support\\Facades\\$facade";
    class_exists($class) && $class::setFacadeApplication($container);
}

// Register Dispatcher for route callbacks
$container->bind(CallableDispatcherContract::class, CallableDispatcher::class);

// --------------------------------------------------------
// Setup View System (Blade & PHP Engines)
// --------------------------------------------------------

// Bind FileViewFinder to locate view templates
$container->bind(ViewFinderInterface::class, function () {
    $filesystem = new Filesystem;
    $paths = [__DIR__.'/../views'];

    return new FileViewFinder($filesystem, $paths);
});

// Bind Event Dispatcher contract
$container->bind(EventsDispatcherContract::class, function () use ($events) {
    return $events;
});

// Setup cache directory for compiled Blade templates
$filesystem = new Filesystem;
$cachePath = __DIR__.'/../storage/views';
if (! is_dir($cachePath)) {
    mkdir($cachePath, 0755, true);
}

// Initialize Blade Compiler
$bladeCompiler = new BladeCompiler($filesystem, $cachePath);

// Register view engines
$engineResolver = new EngineResolver;

// Register Blade Engine
$engineResolver->register('blade', function () use ($bladeCompiler) {
    return new CompilerEngine($bladeCompiler);
});

// Register PHP Engine (fallback for .php views)
$engineResolver->register('php', function () use ($filesystem) {
    return new PhpEngine($filesystem);
});

// Safety Check: Ensure Blade engine is available
if (! method_exists($engineResolver, 'resolve') || ! $engineResolver->resolve('blade')) {
    throw new Exception('Blade engine was not registered properly!');
}

// Build the View Factory
$viewFinder = $container->make(ViewFinderInterface::class);
$viewFactory = new ViewFactory($engineResolver, $viewFinder, $events);

$viewFactory->addExtension('blade.php', 'blade');

$container->instance(ViewFactory::class, $viewFactory);

// --------------------------------------------------------
// Setup Router and Define Routes
// --------------------------------------------------------
$router = new Router($events, $container);

require __DIR__.'/../routes/web.php';

// --------------------------------------------------------
// Handle Incoming Request and Send Response
// --------------------------------------------------------
$request = Request::capture();
$response = $router->dispatch($request);
$response->send();
