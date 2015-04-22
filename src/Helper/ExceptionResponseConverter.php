<?php
/**
 * @author: Radek Adamiec
 * Date: 21.04.15
 * Time: 12:57
 */

namespace Phalconry\Helper;

use AGmakonts\STL\String\String;
use Phalcon\Http\Response;
use Phalconry\Request\PhalconryRestRequest;

/**
 * Class ExceptionResponseConverter
 * @package Phalconry\Helper
 */
class ExceptionResponseConverter
{
    /**
     * @param \Exception                              $exception
     * @param \Phalconry\Request\PhalconryRestRequest $request
     *
     * @return \Phalcon\Http\Response
     */
    static function convert(\Exception $exception, PhalconryRestRequest $request)
    {
        switch (strtoupper($request->getFormat()->value())) {
            case 'JSON':
                $response = new Response(
                    json_encode(
                        [
                            'message' => $exception->getMessage(),
                            'code'    => $exception->getCode(),
                        ]
                    ), $exception->getCode()
                );
                $response->setContentType('application/json');

                return $response;
                break;
            default:
                $response = new Response($exception->getMessage(), $exception->getCode());
                $response->setContentType('text/html');
                return $response;
                break;
        }

    }
}