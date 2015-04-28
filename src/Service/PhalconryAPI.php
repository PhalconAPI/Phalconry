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
use Phalcon\Http\Response;
use Phalcon\Loader;
use Phalcon\Mvc\Router as PhalconRouter;
use Phalcon\Mvc\Router;
use Phalconry\Config\ConfigInterface;
use Phalconry\Controller\PhalconryController;
use Phalconry\Helper\ExceptionResponseConverter;
use Phalconry\Helper\RequestHelper;
use Phalconry\Request\PhalconryRestRequest;

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

        /* @var $request Request */
        $request = $di->get('request');
        $router  = $di->get('router');

        if ( FALSE == $this->checkIfApiCall($request) ) {
            return;
        }

        try {
            $phalconryRequest = new PhalconryRestRequest($request);
        } catch (\Exception $exception) {
            $response = new Response($exception->getMessage(), $exception->getCode());
            $response->send();

        }


        $this->registerApiRoutes($router);
        $this->registerResourceNamespace($config->getResourcesDir());

        $di->set(
            'PhalconryController',
            function () use ($phalconryRequest, $router) {
                $controller = new PhalconryController(
                    $phalconryRequest, String::get($router->getRewriteUri())
                );

                return $controller;
            }
        );
    }


    /**
     * @param \Phalcon\Http\Request $request
     *
     * @return bool
     */
    private function checkIfApiCall(Request $request)
    {
        if(String::get('GET') === String::get($request->getMethod())){
            $contentType = String::get((string)$request->getHeader('ACCEPT'));
        }else{
            $contentType = String::get((string)$request->getHeader('CONTENT_TYPE'));
        }


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

    /**
     *
     * Register namespace with resources
     *
     * @param \AGmakonts\STL\String\String $resourceDir
     *
     */
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