<?php
/**
 *
 * @author Radek Adamiec
 * Date: 14.04.15
 * Time: 13:08
 */

namespace Phalconry\Service;


use AGmakonts\STL\String\String;
use Phalcon\DiInterface;
use Phalcon\Http\Request;
use Phalcon\Loader;
use Phalcon\Mvc\Router as PhalconRouter;
use Phalcon\Mvc\Router;
use Phalconry\Config\ConfigInterface;
use Phalconry\Controller\PhalconryController;
use Phalconry\Helper\RequestHelper;

class PhalconryAPI
{

    const NAMESPACE_RESOURCE_PREFIX = 'Phalconry\Rest';

    private $config;

    /**
     *
     * Constructor is deciding whether or not API module should be initialized
     *
     *
     * @param \Phalconry\Config\ConfigInterface $config
     * @param \Phalcon\DiInterface              $di
     */
    public function __construct(ConfigInterface $config, DiInterface $di)
    {
        $this->config = $config;

        $request = $di->get('request');
        $router  = $di->get('router');


        try {
            $contentType = RequestHelper::getContentTypeFromRequest($request);
        } catch (\Exception $exception) {
            return;
        }

        if ( FALSE == $this->checkIfApiCall($contentType) ) {
            return;
        }

        $this->registerApiRoutes($router);
        $this->registerResourceNamespace($config->getResourcesDir());

        $di->set(
            'PhalconryController',
            function () use ($request, $router) {
                $controller = new PhalconryController(
                    $request, String::get($router->getRewriteUri())
                );

                return $controller;
            }
        );
    }


    /**
     * Check if this is api call.
     *
     * @param \AGmakonts\STL\String\String $contentType
     *
     * @return bool
     */
    private function checkIfApiCall(String $contentType)
    {
        return ((strpos($contentType->value(), 'application/vnd') !== FALSE) ||
            (strpos($contentType->value(), 'application/hal') !== FALSE));
    }


    /**
     * Register routes and callback for them.
     *
     * @param \Phalcon\Mvc\Router $router
     */
    private function registerApiRoutes(Router $router)
    {
        /* @var $router \Phalcon\Mvc\Router */

        $routes = $this->config->getRoutes();
        foreach ($routes as $route) {

            $router->add(
                '/' . ltrim($route, '/'),
                [
                    'controller' => 'Phalconry',
                    'action'     => 'handleRequest',
                    'id'         => -1,
                ]
            );
            $router->add(
                '/' . ltrim($route, '/') . "/([a-zA-Z0-9]+)",
                [
                    'controller' => 'Phalconry',
                    'action'     => 'handleRequest',
                    'id'         => 1,
                ]
            );
        }
    }

    private function registerResourceNamespace(String $resourceDir)
    {
        $loader = new Loader();
        $loader->registerNamespaces(
            [
                self::NAMESPACE_RESOURCE_PREFIX => $resourceDir->value(),
            ]
        );
        $loader->register();
    }
}