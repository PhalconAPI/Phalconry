<?php
/**
 *
 * @author Radek Adamiec
 * Date: 20.04.15
 * Time: 16:28
 */

namespace Phalconry\Controller;


use AGmakonts\STL\String\String;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Url;
use Phalconry\Collection\CollectionInterface;
use Phalconry\Entity\EntityInterface;
use Phalconry\Filter\FilterInterface;
use Phalconry\Filter\PhalconryFilter;
use Phalconry\Helper\ExceptionResponseConverter;
use Phalconry\Renderer\RendererSelectorStrategy;
use Phalconry\Request\PhalconryRestRequest;
use Phalconry\Resource\AbstractResource;
use Phalconry\Resource\ResourceDataFactory;
use Phalconry\Service\PhalconryAPI;
use Phalconry\Validator\PhalconryValidator;
use Phalconry\Validator\ValidatorInterface;

class PhalconryController
{


    /**
     * @var \Phalconry\Request\PhalconryRestRequest
     */
    private $request;

    /**
     * @var \AGmakonts\STL\String\String
     */
    private $uri;

    /**
     * @param \Phalconry\Request\PhalconryRestRequest $request
     * @param \AGmakonts\STL\String\String            $uri
     */
    public function __construct(PhalconryRestRequest $request, String $uri)
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


            $id = $this->processIdentifyingProperty($id);


            $resource  = $this->getResourceForRequestFromPhalconryRequest($this->request);
            $method    = $this->getActionName($id);
            $methodRaw = $method->value();


            $resourceData = $this->processRequestData($resource);


            $result = $resource->$methodRaw($id, $resourceData);


            $this->checkIfCorrectResultFromResource($result, $resource);

            $renderer = RendererSelectorStrategy::getRenderer($this->request->getFormat(), $result);

            var_dump($renderer);


        } catch (\Exception $exception) {
            $response = ExceptionResponseConverter::convert($exception, $this->request);

            return $response->send();
        }
    }


    /**
     * @param \Phalconry\Request\PhalconryRestRequest $request
     *
     * @return AbstractResource
     * @throws \Exception
     */
    private function getResourceForRequestFromPhalconryRequest(PhalconryRestRequest $request)
    {

        $resourceDefaultName = $this->getResourceName();
        $className           = sprintf(
            '%s\\%s\\%s\\%s\\%sResource',
            PhalconryAPI::NAMESPACE_RESOURCE_PREFIX,
            $request->getApiName()->value(),
            $request->getApiVersion()->value(),
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
     * @param \Phalconry\Resource\AbstractResource $resource
     *
     * @return PhalconryFilter
     * @throws \Exception
     */
    private function getFilterForResource(AbstractResource $resource)
    {
        $resourceName = get_class($resource);
        $filterName   = preg_replace('/Resource$/', 'Filter', $resourceName);
        if ( FALSE === class_exists($filterName) ) {
            throw new \Exception('Please define filter in you resource directory or config file', 500);
        }


        if ( FALSE === is_subclass_of($filterName, FilterInterface::class) ) {
            throw new \Exception(sprintf('%s should implement FilterInterface interface', get_class($filterName)), 500);
        }

        $filterRetriever = new $filterName();
        $filter          = $filterRetriever->getFilter();

        if ( FALSE === $filter instanceof PhalconryFilter ) {
            throw new \Exception(
                sprintf('%s should return PhalconryFilter', get_class($filterRetriever)),
                500
            );
        }

        return new $filter;
    }

    /**
     * @param \Phalconry\Resource\AbstractResource $resource
     *
     * @return PhalconryValidator
     * @throws \Exception
     */
    private function getValidatorForResource(AbstractResource $resource)
    {
        $resourceName  = get_class($resource);
        $validatorName = preg_replace('/Resource$/', 'Validator', $resourceName);

        if ( FALSE === class_exists($validatorName) ) {
            throw new \Exception('Please define validator in you resource directory or config file', 500);
        }


        if ( FALSE === is_subclass_of($validatorName, ValidatorInterface::class) ) {
            throw new \Exception(
                sprintf('%s should implement ValidatorInterface interface', get_class($validatorName)),
                500
            );
        }

        $validatorRetriever = new $validatorName();
        $validator          = $validatorRetriever->getValidator();

        if ( FALSE === $validator instanceof PhalconryValidator ) {
            throw new \Exception(
                sprintf('%s should return PhalconryValidator', get_class($validatorRetriever)), 500
            );
        }

        return $validator;
    }

    /**
     * @param null $id
     *
     * @return \AGmakonts\STL\String\String
     * @throws \Exception
     */
    public function getActionName($id = NULL)
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

        $method = $this->request->getMethodName();

        if ( NULL === $id ) {
            if ( isset($methodMap['collection'][$method->value()]) ) {
                return String::get($methodMap['collection'][$method->value()]);
            }
        } else {
            if ( isset($methodMap['entity'][$method->value()]) ) {
                return String::get($methodMap['entity'][$method->value()]);
            }
        }

        throw new \Exception('Unsupported request method', 400);
    }


    /**
     * @return \AGmakonts\STL\String\String
     */
    private function getResourceName()
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
     * @return string|int|null
     */
    private function processIdentifyingProperty($id)
    {

        if ( $id === -1 || $this->request->getMethodName() === String::get('POST') ) {
            $id = NULL;
        } else {
            if ( $id == filter_var($id, FILTER_VALIDATE_INT) ) {
                $id = (int)$id;
            }
        }


        return $id;
    }

    /**
     * @param $resource
     *
     * @return \Phalconry\Resource\JsonResourceData|null
     * @throws \Exception
     */
    protected function processRequestData($resource)
    {
        $resourceData = NULL;
        if ( $this->request->doesMethodNeedPayload() ) {
            $resourceData = ResourceDataFactory::getDataObjectFromRequest($this->request);

            $filterDefinitions = $this->getFilterForResource($resource);
            $resourceData->filterData($filterDefinitions);

            $validatorDefinitions = $this->getValidatorForResource($resource);
            $resourceData->validateData($validatorDefinitions);
        }


        return $resourceData;
    }

    /**
     * @param $result
     * @param $resource
     *
     * @throws \Exception
     */
    protected function checkIfCorrectResultFromResource($result, $resource)
    {
        if ( FALSE === $result instanceof EntityInterface && FALSE === $result instanceof CollectionInterface ) {
            throw new \Exception(
                sprintf(
                    '%s must return EntityInterface or CollectionInterface instance',
                    get_class($resource)
                ), 500
            );
        }
    }
}