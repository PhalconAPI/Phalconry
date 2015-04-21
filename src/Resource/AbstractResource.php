<?php
/**
 * @author: Radek Adamiec
 * Date: 21.04.15
 * Time: 13:22
 */

namespace Phalconry\Resource;

/**
 * Class AbstractResource
 * @package Phalconry\Resource
 */
abstract class AbstractResource {

    /**
     * @param $data
     */
    public function create($data){
        throw new \BadMethodCallException('This method is not allowed', 403);
    }

    /**
     * @param $id
     */
    public function fetch($id){
        throw new \BadMethodCallException('This method is not allowed', 403);
    }

    /**
     *
     */
    public function fetchAll(){
        throw new \BadMethodCallException('This method is not allowed', 403);
    }

    /**
     * @param $id
     * @param $data
     */
    public function update($id, $data){
        throw new \BadMethodCallException('This method is not allowed', 403);
    }

    /**
     * @param $data
     */
    public function updateList($data){
        throw new \BadMethodCallException('This method is not allowed', 403);
    }

    /**
     * @param $id
     * @param $data
     */
    public function replace($id, $data){
        throw new \BadMethodCallException('This method is not allowed', 403);
    }

    /**
     * @param $data
     */
    public function replaceList($data){
        throw new \BadMethodCallException('This method is not allowed', 403);
    }

    /**
     * @param $id
     */
    public function delete($id){
        throw new \BadMethodCallException('This method is not allowed', 403);
    }

    /**
     *
     */
    public function deleteList(){
        throw new \BadMethodCallException('This method is not allowed', 403);
    }
}