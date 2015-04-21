<?php
/**
 * @author: Radek Adamiec
 * Date: 21.04.15
 * Time: 15:01
 */

namespace Phalconry\Helper;


use AGmakonts\STL\String\String;
use Phalcon\Http\Request;

class RequestHelper
{

    /**
     * This class does not require instance
     */
    protected function __construct()
    {
    }

    /**
     * @param \Phalcon\Http\Request $request
     *
     * @return \AGmakonts\STL\String\String
     * @throws \Exception
     */
    public static function getContentTypeFromRequest(Request $request)
    {
        if ( String::get('GET') === String::get($request->getMethod()) ) {
            $contentTypeRaw = $request->getHeader('ACCEPT');
        } else {
            $contentTypeRaw = $request->getHeader('CONTENT_TYPE');
        }

        if ( NULL === $contentTypeRaw || TRUE === empty($contentTypeRaw) ) {
            throw new \Exception('Invalid headers.', 400);
        }

        return String::get($contentTypeRaw);
    }


    /**
     * @param \AGmakonts\STL\String\String $contentType
     *
     * @return \AGmakonts\STL\String\String
     * @throws \Exception
     */
    public static function getApiVersionFromContentType(String $contentType)
    {
        $apiVersionRaw = preg_replace('/(.*)\./', '', $contentType->value());
        $apiVersionRaw = strtoupper(preg_replace('/\+(.*)/', '', $apiVersionRaw));

        if ( 1 !== preg_match('/V(\d+)/', $apiVersionRaw) ) {
            throw new \Exception('Invalid api version');
        }

        return String::get($apiVersionRaw);
    }


    public static function getApiNameFromContentType(String $contentType)
    {
        $apiNameRaw = preg_replace('/^.+?(?=\.)\./', '', $contentType->value());
        $apiNameRaw = preg_replace('/\.(.*)/', '', $apiNameRaw);
        $apiNameRaw = ucfirst($apiNameRaw);
        if ( NULL === $apiNameRaw || TRUE === empty($apiNameRaw) ) {
            throw new \Exception('Invalid api name');
        }

        return String::get($apiNameRaw);
    }

}