<?php
/**
 * @author: Radek Adamiec
 * Date: 22.04.15
 * Time: 14:38
 */

namespace Phalconry\Request;


use AGmakonts\STL\String\String;
use Phalcon\Http\Request;

class PhalconryRestRequest
{

    /**
     * @var \AGmakonts\STL\String\String
     */
    private $contentType;

    /**
     * @var \AGmakonts\STL\String\String
     */
    private $apiName;

    /**
     * @var \AGmakonts\STL\String\String
     */
    private $apiVersion;

    /**
     * @var \AGmakonts\STL\String\String
     */
    private $format;

    /**
     * @var \AGmakonts\STL\String\String
     */
    private $payload;

    /**
     * @param \Phalcon\Http\Request $request
     *
     * @throws \Exception
     */
    public function __construct(Request $request)
    {
        $this->methodName  = String::get($request->getMethod());
        $this->setContentTypeFromRequest($request);
        $this->setApiNameFromContentType($this->contentType);
        $this->setApiVersionFromContentType($this->contentType);
        $this->setFormatFromContentType($this->contentType);
        $this->setPayloadFromRequest($request);
    }

    /**
     * @return \AGmakonts\STL\String\String
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return \AGmakonts\STL\String\String
     */
    public function getApiName()
    {
        return $this->apiName;
    }

    /**
     * @return \AGmakonts\STL\String\String
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @return \AGmakonts\STL\String\String
     */
    public function getRawPayload()
    {
        return $this->payload;
    }

    /**
     * @return \AGmakonts\STL\String\String
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return \AGmakonts\STL\String\String
     */
    public function getMethodName()
    {
        return $this->methodName;
    }


    /**
     * @param \AGmakonts\STL\String\String $method
     *
     * @return bool
     */
    public function doesMethodNeedPayload(String $method = NULL){
        if(NULL === $method){
            $method = $this->getMethodName();
        }


        if($method === String::get('GET') || $method === String::get('OPTIONS')){
            return FALSE;
        }

        return TRUE;
    }

    /**
     * @param \Phalcon\Http\Request $request
     *
     * @throws \Exception
     */
    private function setContentTypeFromRequest(Request $request)
    {
        if ( String::get('GET') === String::get($request->getMethod()) ) {
            $contentTypeRaw = $request->getHeader('ACCEPT');
        } else {
            $contentTypeRaw = $request->getHeader('CONTENT_TYPE');
        }

        if ( NULL === $contentTypeRaw || TRUE === empty($contentTypeRaw) ) {
            throw new \Exception('Invalid headers.', 400);
        }

        $this->contentType = String::get($contentTypeRaw);
    }


    /**
     * @param \AGmakonts\STL\String\String $contentType
     *
     * @throws \Exception
     */
    private function setApiVersionFromContentType(String $contentType)
    {
        $apiVersionRaw = preg_replace('/(.*)\./', '', $contentType->value());
        $apiVersionRaw = strtoupper(preg_replace('/\+(.*)/', '', $apiVersionRaw));

        if ( 1 !== preg_match('/V(\d+)/', $apiVersionRaw) ) {
            throw new \Exception('Invalid api version');
        }

        $this->apiVersion = String::get($apiVersionRaw);
    }


    /**
     * @param \AGmakonts\STL\String\String $contentType
     *
     * @throws \Exception
     */
    private function setApiNameFromContentType(String $contentType)
    {
        $apiNameRaw = preg_replace('/^.+?(?=\.)\./', '', $contentType->value());
        $apiNameRaw = preg_replace('/\.(.*)/', '', $apiNameRaw);
        $apiNameRaw = ucfirst($apiNameRaw);
        if ( NULL === $apiNameRaw || TRUE === empty($apiNameRaw) ) {
            throw new \Exception('Invalid api name');
        }

        $this->apiName =  String::get($apiNameRaw);
    }

    /**
     * @param \AGmakonts\STL\String\String $contentType
     */
    private function  setFormatFromContentType(String $contentType)
    {
        $this->format =  String::get(preg_replace('/^(.*)\+/', '', $contentType->value()));
    }

    /**
     * @param \Phalcon\Http\Request $request
     *
     * @throws \Exception
     */
    private function setPayloadFromRequest(Request $request){

            $rawPayload = (string)$request->getRawBody();
            if((NULL === $rawPayload || empty($rawPayload)) && $this->doesMethodNeedPayload()){
                throw new \Exception('Payload empty', 400);
            }
            $payload = String::get($rawPayload);



        $this->payload = $payload;
    }

}