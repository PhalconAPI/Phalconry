<?php

namespace Phalconry;

use Phalcon\DiInterface;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View;
use Phalconry\Config\Config;
use Phalconry\Service\PhalconryAPI;


class Module implements ModuleDefinitionInterface
{
    public function registerAutoloaders(DiInterface $dependencyInjector = NULL)
    {
        /**
         * This is handled by composer autoloader
         */
    }

    public function registerServices($dependencyInjector)
    {
        $config                           = new Config();
        $service                          = new PhalconryAPI($config);
        $dependencyInjector['phalconry'] = $service;
    }
}
