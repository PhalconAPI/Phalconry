<?php
/**
 * @author: Radek Adamiec
 * Date: 22.04.15
 * Time: 15:08
 */

namespace Phalconry\Entity;


use Phalconry\Resource\ResourceResult;

interface EntityInterface extends ResourceResult{

    /**
     * @param $model
     *
     * @return array
     */
    public function getEntity($model);
}