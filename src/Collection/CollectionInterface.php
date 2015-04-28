<?php
/**
 * @author: Radek Adamiec
 * Date: 23.04.15
 * Time: 13:40
 */

namespace Phalconry\Collection;


use Phalconry\Resource\ResourceResult;

interface CollectionInterface extends ResourceResult{

    /**
     * Returns count of all models
     * @return int
     */
    public function getCount();

    /**
     * This method should return models set that will be converted to entity.
     * @return \Traversable
     */
    public function getData();

    /**
     * Returns page size
     * @return int
     */
    public function getPageSize();
}