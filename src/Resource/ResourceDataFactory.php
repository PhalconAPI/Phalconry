<?php
/**
 * @author: Radek Adamiec
 * Date: 22.04.15
 * Time: 13:54
 */

namespace Phalconry\Resource;



use Phalconry\Request\PhalconryRestRequest;

class ResourceDataFactory {

    public static function getDataObjectFromRequest(PhalconryRestRequest $request){

        switch(strtoupper($request->getFormat()->value())){
            case 'JSON':
                return new JsonResourceData($request->getRawPayload());
                break;
            case 'XML':
                break;
            default:
                throw new \Exception('Unsupported format', 400);
        }
    }
}