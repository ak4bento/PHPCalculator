<?php

namespace Jakmall\Recruitment\Calculator\Container;

use Illuminate\Contracts\Container\Container;

interface ContainerServiceProviderInterface
{
    /**
     * @param Container $container
     *
     * @return void
     */
    public function register(Container $container): void;
}
