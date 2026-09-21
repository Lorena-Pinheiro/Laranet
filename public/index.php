<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\CreateUserController;
use App\Controllers\UserController;
use Laranet\Container\Container;
use Laranet\Http\Request;
use Laranet\Http\Response;
use Laranet\Routing\Router;

$request = Request::capture();
$container = new Container();
$router = new Router($container);

$container->bind(\App\Interfaces\ILogger::class, \App\Services\FileLoggerService::class);

$router->get('/', function(Request $request){
    return new Response('Hello World');
});

$router->group('/api', function(Router $router){
    $router->group('/v1', function(Router $router){
        $router->get('/users', [UserController::class, 'index'])->name('users.index');
    });
    
    $router->get('/users/{id}', [UserController::class, 'show'])->where('id', '\d+')->name('users.show');
    
    $router->post('/users', CreateUserController::class);
});

$response = $router->dispatch($request);
$response->send();