<?php
/**
 * Created by IntelliJ IDEA.
 * User: Radek Adamiec <radek@adamiec.it>
 * Date: 14.04.15
 * Time: 13:08
 */

namespace Phalconry\Service;


use AGmakonts\STL\String\String;
use Phalcon\Http\Request;
use Phalcon\Mvc\Router as PhalconRouter;
use Phalconry\Config\Config;

class PhalconryAPI
{

    /**
     * Constructor is deciding whether or not API module should be initialized
     *
     * @param \Phalconry\Config\Config $config
     */
    public function __construct(Config $config)
    {
        $request     = new Request();
        $method      = $request->getMethod();
        $contentType = String::get($request->getHeader('CONTENT_TYPE'));
        $accept      = String::get($request->getHeader('ACCEPT'));
        switch ($method) {
            case 'GET':
                // If HTTP method is GET then we need to check also ACCEPT header. It is because some JS frameworks
                // strip header Content-Type
                if (TRUE === $this->checkIfApiCall($contentType) || TRUE === $this->checkIfApiCall($accept)) {
                    $this->registerApi($config);
                }
                break;
            default:
                if ($this->checkIfApiCall($contentType)) {
                    $this->registerApi($config);
                }
                break;
        }
    }


    /**
     * Check if this is api call.
     * @param \AGmakonts\STL\String\String $header
     *
     * @return bool
     */
    private function checkIfApiCall(String $header)
    {
        return (strpos($header->value(), 'application/hal') !== FALSE);
    }


    /**
     * Register routes and callback for them.
     * @param \Phalconry\Config\Config $config
     */
    private function registerApi(Config $config)
    {
    }
}