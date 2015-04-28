<?php
/**
 * @author: Radek Adamiec
 * Date: 23.04.15
 * Time: 13:48
 */

namespace Phalconry\Renderer;


use AGmakonts\STL\String\String;
use Phalconry\Collection\CollectionInterface;
use Phalconry\Entity\EntityInterface;
use Phalconry\Resource\ResourceResult;

class RendererSelectorStrategy
{

    /**
     * @var array
     */
    private static $rendersMap = [
        'collection' => ['JSON' => JsonCollectionRenderer::class],
        'entity'     => ['JSON' => JsonEntityRenderer::class],
    ];


    /**
     * @param \AGmakonts\STL\String\String       $format
     * @param \Phalconry\Resource\ResourceResult $result
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getRenderer(String $format, ResourceResult $result)
    {
        if($result instanceof EntityInterface){
            switch (strtoupper($format->value())) {
                case 'JSON':
                    return new self::$rendersMap['entity']['JSON'];
                    break;
            }
        }else if($result instanceof CollectionInterface){
            switch (strtoupper($format->value())) {
                case 'JSON':
                    return new self::$rendersMap['collection']['JSON'];
                    break;
            }
        }

        throw new \Exception('Invalid result class', 500);

    }
}