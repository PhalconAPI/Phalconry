<?php
/**
 * @author: Radek Adamiec
 * Date: 22.04.15
 * Time: 12:53
 */

namespace Phalconry\Resource;


use AGmakonts\STL\String\String;
use Phalcon\Filter;
use Phalconry\Filter\PhalconryFilter;
use Phalconry\Validator\PhalconryValidator;

class JsonResourceData
{

    /**
     * @var \stdClass
     */
    private $data;

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

    /**
     * @param \Phalconry\Filter\PhalconryFilter $filter
     */
    public function filterData(PhalconryFilter $filter)
    {

        foreach ($filter->getFilters() as $fieldName) {
            /* @var $fieldName \AGmakonts\STL\String\String */
            if ( FALSE === $this->fieldExist($fieldName) ) {
                continue;
            }


            $this->appendFilters($fieldName, $filter);
        }
    }


    /**
     * @param \AGmakonts\STL\String\String $fieldName
     * @param PhalconryFilter              $phalconryFilter
     *
     * @return void
     */
    private function appendFilters(String $fieldName, PhalconryFilter $phalconryFilter)
    {
        foreach ($phalconryFilter->getFilters()->offsetGet($fieldName) as $filter) {
            if ( FALSE == function_exists($filter) && TRUE == is_callable($filter) ) {
                $this->data->{$fieldName->value()} = $filter($this->data->{$fieldName->value()});
            } else {
                $this->data->{$fieldName->value()} = $phalconryFilter->sanitize
                (
                    $this->data->{$fieldName->value()},
                    $filter
                );
            }
        }
    }


    /**
     * @param \Phalconry\Validator\PhalconryValidator $validator
     *
     * @throws \Exception
     */
    public function validateData(PhalconryValidator $validator)
    {
        $validationResult = $validator->validate($this->data);
        if(TRUE === $validationResult->count()>0){
            $error = "";
            foreach($validationResult as $message){
                /* @var $message \Phalcon\Validation\Message */
                $error .= sprintf('%s: %s, ', $message->getField(), $message->getMessage());
            }
            $error = trim($error, ', ');
            throw new \Exception($error, 400);
        }

    }
}