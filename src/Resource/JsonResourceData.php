<?php
/**
 * @author: Radek Adamiec
 * Date: 22.04.15
 * Time: 12:53
 */

namespace Phalconry\Resource;


use AGmakonts\STL\String\String;
use Phalcon\Filter;
use Phalconry\Filter\FilterInterface;
use Phalconry\Validator\ValidatorInterface;

class JsonResourceData
{

    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * This object currently supports only json data.
     * In future I will add XML support as well
     *
     *
     * @param \AGmakonts\STL\String\String $data
     *
     * @throws \Exception
     */
    public function __construct(String $data)
    {
        $rawData = json_decode($data->value());

        if ( NULL === $rawData ) {
            throw new \Exception('Invalid json object in payload', 400);
        }
        $this->data = $rawData;
    }


    /**
     * @param \AGmakonts\STL\String\String $field
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get(String $field)
    {
        if ( FALSE === $this->fieldExist($field) ) {
            throw new \Exception(sprintf('%s does not exist', ucfirst($field->value())), 500);
        }

        return $this->data->{$field->value()};
    }

    /**
     * @return \stdClass
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \AGmakonts\STL\String\String $fieldName
     *
     * @return bool
     */
    private function fieldExist(String $fieldName)
    {
        return isset($this->data->{$fieldName->value()});
    }

    public function filterData(FilterInterface $filter)
    {
        $jsonFiltersObject = $filter->getJsonFilter();


        foreach ($jsonFiltersObject->getFilters() as $fieldName) {
            /* @var $fieldName \AGmakonts\STL\String\String */
            if ( FALSE === $this->fieldExist($fieldName) ) {
                continue;
            }

            /* @var $filters array */
            $filters = $jsonFiltersObject->getFilters()->offsetGet($fieldName);

            $this->appendFilters($fieldName, $filters);
        }
    }


    /**
     * @param \AGmakonts\STL\String\String $fieldName
     * @param array                        $filters
     *
     * @return void
     */
    private function appendFilters(String $fieldName, array $filters)
    {
        foreach ($filters as $filter) {
            if ( FALSE == function_exists($filter) && TRUE == is_callable($filter) ) {
                $this->data->{$fieldName->value()} = $filter($this->data->{$fieldName->value()});
            } else {
                $this->data->{$fieldName->value()} = $this->getFilterObject()->sanitize
                (
                    $this->data->{$fieldName->value()},
                    $filter
                );
            }
        }
    }


    /**
     * @return \Phalcon\Filter
     */
    private function getFilterObject()
    {
        if ( NULL === $this->filter ) {
            $this->filter = new Filter();
        }

        return $this->filter;
    }


    public function validateData(ValidatorInterface $validator)
    {
    }
}