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
     * This method should return JsonFilter object
     *
     *
     * @return JsonFilter
     */
    public function getJsonFilter();
}