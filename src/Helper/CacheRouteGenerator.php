<?php
/**
 *
 * @author Radek Adamiec
 * Date: 14.04.15
 * Time: 11:57
 */

namespace Phalconry\Helper;

use AGmakonts\STL\String\String;

/**
 * Class CacheRouteGenerator
 *
 * @package Phalconry\Helper
 */
class CacheRouteGenerator {

    /**
     * This method will generate cached version of config file for speeding up routing.
     * It will put new file in config directory
     *
     * @param \AGmakonts\STL\String\String $configFile
     */
    public static function generateCache(String $configFile){
        if(FALSE === file_exists($configFile->value())){
            throw new \RuntimeException('Please provide valid file path to config');
        }

        //TODO: Implementation needed

    }
}