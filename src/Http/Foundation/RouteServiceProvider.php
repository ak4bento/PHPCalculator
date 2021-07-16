<?php

namespace Jakmall\Recruitment\Calculator\Http\Foundation;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Jakmall\Recruitment\Calculator\Container\ContainerServiceProviderInterface;

class RouteServiceProvider implements ContainerServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $container): void
    {
        $this->registerEventDispatcher($container);
        $this->registerRouter($container);
        $this->registerUrlGenerator($container);
        $this->registerRedirector($container);
        $this->registerResponseFactory($container);
        $this->registerControllerDispatcher($container);
    }

    /**
     * @param Container $container
     */
    protected function registerEventDispatcher(Container $container): void
    {
        $container->singleton(
            'events',
            function ($container) {
                return new Dispatcher($container);
            }
        );
    }

    /**
     * @param Container $container
     */
    protected function registerRouter(Container $container): void
    {
        $container->singleton(
            'router',
            function ($container) {
                return new Router($container['events'], $container);
            }
        );
    }

    /**
     * @param Container $container
     */
    protected function registerUrlGenerator(Container $container): void
    {
        $container->singleton(
            'url',
            function ($container) {
                $routes = $container['router']->getRoutes();

                $container->instance('routes', $routes);

                $url = new UrlGenerator(
                    $routes,
                    $container->rebinding('request', $this->requestRebinder())
                );

                $url->setSessionResolver(
                    function () {
                        return null;
                    }
                );

                $url->setKeyResolver(
                    function () use ($container) {
                        return null;
                    }
                );

                $container->rebinding(
                    'routes',
                    function ($container, $routes) {
                        $container['url']->setRoutes($routes);
                    }
                );

                return $url;
            }
        );
    }

    /**
     * @return \Closure
     */
    protected function requestRebinder(): \Closure
    {
        return function ($container, $request) {
            $container['url']->setRequest($request);
        };
    }

    /**
     * @param Container $container
     */
    protected function registerRedirector(Container $container): void
    {
        $container->singleton(
            'redirect',
            function ($container) {
                return new Redirector($container['url']);
            }
        );
    }

    /**
     * @param Container $container
     */
    protected function registerResponseFactory(Container $container): void
    {
        $container->singleton(
            ResponseFactoryContract::class,
            function ($container) {
                return new ResponseFactory($container[ViewFactoryContract::class], $container['redirect']);
            }
        );
    }

    /**
     * @param Container $container
     */
    protected function registerControllerDispatcher(Container $container): void
    {
        $container->singleton(
            ControllerDispatcherContract::class,
            function ($container) {
                return new ControllerDispatcher($container);
            }
        );
    }
}
