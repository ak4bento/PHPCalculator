<?php

require_once __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Jakmall\Recruitment\Calculator\Http\Foundation\RouteServiceProvider;

$appConfig = require_once __DIR__.'/../config/app.php';
$providers = $appConfig['providers'];

$container = new Container();
$container->make(RouteServiceProvider::class)->register($container);
foreach ($providers as $provider) {
    $container->make($provider)->register($container);
}

/** @var \Illuminate\Routing\Router $router */
$router = $container->get('router');
$router
    ->prefix('calculator')
    ->namespace('Jakmall\Recruitment\Calculator\Http\Controller')
    ->group(
        function (\Illuminate\Routing\Router $router) {
            $router->get('/', 'HistoryController@index');
            $router->get('/{id}', 'HistoryController@show');
            $router->delete('/{id}', 'HistoryController@remove');
            $router->post('/{action}', 'CalculatorController@calculate');
        }
    )
;

$request = Request::capture();
$request->server->set('SCRIPT_FILENAME', '/index.php');
$container->bind(
    Illuminate\Http\Request::class,
    function () use ($request) {
        return $request;
    }
);
$response = $router->dispatch($request);

$response->send();
