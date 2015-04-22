<?php
/**
 *
 * @author Radek Adamiec
 * Date: 14.04.15
 * Time: 12:40
 */

namespace Phalconry\Validator;

interface ValidatorInterface
{

    /**
     * @return AbstractValidator
     */
    public function getJsonValidator();
}