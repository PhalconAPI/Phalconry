<?php
/**
 * @author: Radek Adamiec
 * Date: 22.04.15
 * Time: 11:51
 */

namespace Phalconry\Filter;

use AGmakonts\STL\String\String;

/**
 * Class JsonFilter
 * @package Phalconry\JsonFilter
 */
class JsonFilter extends AbstractFilter
{
    /**
     *
     * Add filter to field.
     * Filter may be Phalcon filter or anonymous function
     *
     *
     * @param \AGmakonts\STL\String\String $fieldName
     * @param                              $filter
     */
    public function addFilter(String $fieldName, $filter)
    {
        if ( FALSE === $this->filters->offsetExists($fieldName) ) {
            $filters = [$filter];
            $this->filters->attach($fieldName, $filters);
        } else {
            $filters   = $this->filters->offsetGet($fieldName);
            $filters[] = $filter;
            $this->filters->offsetSet($fieldName, $filters);
        }
    }
}