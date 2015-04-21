<?php
/**
 *
 * @author Radek Adamiec
 * Date: 14.04.15
 * Time: 13:10
 */

namespace Phalconry\Config;


/**
 * Interface ConfigInterface
 *
 * @package Phalconry\Config
 */
interface ConfigInterface {

    /**
     * @return array
     */
    public function getRoutes();

    /**
     * @return \AGmakonts\STL\String\String
     */
    public function getResourcesDir();
}