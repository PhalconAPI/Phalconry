<?php
/**
 *
 * @author Radek Adamiec
 * Date: 14.04.15
 * Time: 13:11
 */

namespace Phalconry\Config;

use AGmakonts\STL\String\String;


/**
 * Class Config
 *
 * @package Phalconry\Config
 */
class Config implements ConfigInterface
{
    /**
     * @var string
     */
    private $config;

    /**
     * @var String
     */
    private $configDir;

    /**
     * @param $configFile
     */
    public function __construct($configFile)
    {
        $this->config = include_once $configFile;
        $this->configDir = String::get(dirname($configFile));
        if ( FALSE === $this->config ) {
            throw new \InvalidArgumentException('Please provide valid config file');
        }
    }

    /**
     * Return array with only routes
     * @return array
     */
    public function getRoutes()
    {

        $routes = [];
        foreach ($this->config['apis'] as $apiName => $versions) {
            foreach ($versions as $versionName => $resource) {
                $routes[key($resource)] = key($resource);
            }
        }

        return $routes;
    }

    /**
     * This will provide directory for api namespace
     * @return String
     */
    public function getResourcesDir()
    {
        if(TRUE === isset($this->config['resourceDir']) && NULL !== $this->config['resourceDir']){
            return String::get($this->config['resourceDir']);
        }

        return $this->configDir;
    }
}