<?php
/**
 * @author: Radek Adamiec
 * Date: 22.04.15
 * Time: 12:50
 */

namespace Phalconry\Filter;


use AGmakonts\STL\String\String;

abstract class AbstractFilter
{

    protected $filters;

    public final function __construct()
    {
        $this->filters = new \SplObjectStorage();
    }


    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Remove all filters for field name
     *
     * @param \AGmakonts\STL\String\String $fieldName
     *
     * @throws \Exception
     */
    public function removeFilters(String $fieldName)
    {
        if ( FALSE === $this->filters->offsetExists($fieldName) ) {
            throw new \Exception('Filters for %s does not exist');
        }
        $this->filters->offsetUnset($fieldName);
    }
}