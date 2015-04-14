<?php
/**
 * Created by IntelliJ IDEA.
 * User: Radek Adamiec <radek@adamiec.it>
 * Date: 14.04.15
 * Time: 13:11
 */

namespace Phalconry\Config;

/**
 * Class Config
 *
 * @package Phalconry\Config
 */
class Config implements ConfigInterface
{

    public function getRoutes()
    {
        return [
            'test',
        ];
    }
}