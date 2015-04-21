<?php
/**
 *
 * @author Radek Adamiec
 * Date: 20.04.15
 * Time: 16:28
 */

namespace Phalconry\Controller;


use AGmakonts\STL\Number\Integer;
use AGmakonts\STL\String\String;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Url;
use Phalconry\Helper\ExceptionJsonConverter;
use Phalconry\Helper\RequestHelper;
use Phalconry\Resource\AbstractResource;
use Phalconry\Service\PhalconryAPI;

class PhalconryController
{


    /**
     * @var Request
     */
    private $request;

    /**
     * @var \AGmakonts\STL\String\String
     */
    private $uri;

    /**
     * @param \Phalcon\Http\Request        $request
     * @param \AGmakonts\STL\String\String $uri
     */
    public function __construct(Request $request, String $uri)
    {
        $this->request = $request;
        $this->uri     = $uri;
    }


    /**
     * @param $id
     *
     * @return \Phalcon\Http\Response
     */
    public function handleRequestAction($id)
    {
        try {
            //Just for separating entity request from collection request
            if($id === -1){
                $id = NULL;
            }else{
                $id = $this->processIdentifyingProperty($id);
            }

            $dataRaw  = $this->getRequestData();
            $resource = $this->getResourceForRequest();
            $method = $this->getActionName($this->getRequestMethod(), $id);
            $methodRaw = $method->value();

            //TODO: get $dataRaw and filter and validate it via appropiate class

            $resource->$methodRaw($id, $dataRaw);


        } catch (\Exception $exception) {
            return $this->getErrorResponse(String::get($exception->getMessage()), Integer::get($exception->getCode()));
        }
    }




    /**
     * @return AbstractResource
     * @throws \Exception
     */
    private function getResourceForRequest()
    {
        $contentType = RequestHelper::getContentTypeFromRequest($this->request);
        $apiVersion  = RequestHelper::getApiVersionFromContentType($contentType);
        $apiName     = RequestHelper::getApiNameFromContentType($contentType);

        $resourceDefaultName = $this->getResourceDefaultName();
        $className           = sprintf(
            '%s\\%s\\%s\\%s\\%sResource',
            PhalconryAPI::NAMESPACE_RESOURCE_PREFIX,
            $apiName->value(),
            $apiVersion->value(),
            $resourceDefaultName->value(),
            $resourceDefaultName->value()
        );

        if ( FALSE === class_exists($className) ) {
            throw new \Exception('Resource not found', 500);
        }

        if ( FALSE === is_subclass_of($className, AbstractResource::class) ) {
            throw new \Exception('Resource must be instance of AbstractResource', 500);
        }

        return new $className();
    }

    /**
     * @param \AGmakonts\STL\String\String $method
     * @param null                         $id
     *
     * @return \AGmakonts\STL\String\String
     * @throws \Exception
     */
    public function getActionName(String $method, $id = NULL)
    {
        $methodMap = [
            'entity'     => [
                'GET'    => 'fetch',
                'POST'   => 'create',
                'PUT'    => 'replace',
                'PATCH'  => 'update',
                'DELETE' => 'delete',
            ],
            'collection' => [
                'GET'    => 'fetchAll',
                'POST'   => 'create',
                'PUT'    => 'replaceList',
                'PATCH'  => 'updateList',
                'DELETE' => 'deleteList',
            ],
        ];


        if(NULL === $id){
            if(isset($methodMap['collection'][$method->value()])){
                return String::get($methodMap['collection'][$method->value()]);
            }
        }else{
            if(isset($methodMap['entity'][$method->value()])){
                return String::get($methodMap['entity'][$method->value()]);
            }
        }

        throw new \Exception('Unsupported request method', 400);
    }

    /**
     * @return \AGmakonts\STL\String\String
     */
    private function getResourceDefaultName()
    {
        $uriRaw    = $this->uri->value();
        $uriRawArr = explode('/', trim($uriRaw, '/'));

        $resourceRawName = $uriRawArr[0];

        $resourceRawNameArr = explode('-', $resourceRawName);

        foreach ($resourceRawNameArr as &$partUri) {
            $partUri = ucfirst(strtolower($partUri));
        }
        $resourceRawName = implode('', $resourceRawNameArr);

        return String::get($resourceRawName);
    }

    /**
     * @param $id
     *
     * @return string|int
     */
    private function processIdentifyingProperty($id)
    {
        if ($id == filter_var($id, FILTER_VALIDATE_INT) ) {
            $id = (int)$id;
        }

        return $id;
    }

    /**
     * @param \AGmakonts\STL\String\String  $message
     * @param \AGmakonts\STL\Number\Integer $code
     *
     * @return \Phalcon\Http\Response
     */
    private function getErrorResponse(String $message, Integer $code)
    {
        $response = new Response();
        $response->setContent(ExceptionJsonConverter::convert(new \Exception($message->value(), $code->value())));
        $response->setStatusCode($code->value(), "");
        $response->setContentType('application/json');

        return $response;
    }

    /**
     * @return String
     * @throws \Exception
     */
    private function getRequestData()
    {
        $method = $this->getRequestMethod();

        if ( String::get('GET') === $method ) {
            $data = $this->request->getQuery();
            if ( NULL === $data ) {
                $data = "";
            }
        } else {
            $data = $this->request->getRawBody();
            if ( NULL === $data && 'DELETE' !== $method ) {
                throw new \Exception('Malformed or empty json', 400);
            }
        }

        $data = String::get($data);

        return $data;
    }

    /**
     * @return \AGmakonts\STL\String\String
     */
    private function getRequestMethod()
    {
        return String::get($this->request->getMethod());
    }
}