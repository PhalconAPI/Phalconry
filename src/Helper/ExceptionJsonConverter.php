<?php
/**
 * @author: Radek Adamiec
 * Date: 21.04.15
 * Time: 12:57
 */

namespace Phalconry\Helper;

use AGmakonts\STL\String\String;

/**
 * Class ExceptionJsonConverter
 * @package Phalconry\Helper
 */
class ExceptionJsonConverter
{
    /**
     *
     * This will probably have more logic in future
     * @param \Exception $exception
     *
     * @return string
     */
    static function convert(\Exception $exception)
    {
        $data = [
            'code'=>$exception->getCode(),
            'message'=>$exception->getMessage()
        ];

        return String::get(json_encode($data));
    }
}