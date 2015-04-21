<?php
/**
 *
 * @author Radek Adamiec
 * Date: 14.04.15
 * Time: 12:40
 */

namespace Phalconry\Filter;

interface FilterInterface {

    /**
     * Return filters for request
     *
     * @return array
     *
     * <code>
     * return [
     *    'name'=>['string'],
     *    'screen_name'=>['string', function($value){ return str_replace(' ', '_', $value); }
     * ]
     *
     * </code>
     */
    public function getFilters();
}