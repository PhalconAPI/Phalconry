<?php
/**
 * @author: Radek Adamiec
 * Date: 22.04.15
 * Time: 12:50
 */

namespace Phalconry\Filter;


use AGmakonts\STL\String\String;
use Phalcon\Filter as PhalconFilter;

class PhalconryFilter extends PhalconFilter
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
    public function removeFiltersForField(String $fieldName)
    {
        if ( FALSE === $this->filters->offsetExists($fieldName) ) {
            throw new \Exception('Filters for %s does not exist');
        }
        $this->filters->offsetUnset($fieldName);
    }

    /**
     * Removes all filters
     */
    public function removeAllFilters(){
        $this->filters = new \SplObjectStorage();
    }

    /**
     *
     * Add filter to field.
     * filter may be Phalcon filter or anonymous function
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

    /**
     * @param $name
     * @param $handler
     *
     * @return void
     */
    public function add($name, $handler)
    {
        $this->addFilter(String::get($name), $handler);
    }
}