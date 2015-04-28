<?php
/**
 * @author: Radek Adamiec
 * Date: 22.04.15
 * Time: 11:51
 */

namespace Phalconry\Validator;


use AGmakonts\STL\String\String;
use Phalcon\Validation;
use Phalcon\Validation\Validator;

class PhalconryValidator extends Validation
{

    private $validators;

    public function __construct()
    {
        $this->validators = new \SplObjectStorage();
    }

    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * Removes all validators for given field name
     * @param \AGmakonts\STL\String\String $fieldName
     *
     * @throws \Exception
     */
    public function removeValidatorsForField(String $fieldName)
    {
        if ( FALSE === $this->validators->offsetExists($fieldName) ) {
            throw new \Exception('This field does not have any validators');
        }

        $this->validators->offsetUnset($fieldName);
    }

    /**
     * Removes all validators
     */
    public function removeAllValidators()
    {
        $this->validators = new \SplObjectStorage();
    }

    /**
     * Add validator to validators set
     *
     * @param \AGmakonts\STL\String\String $fieldName
     * @param                              $validator
     */
    public function addValidator(String $fieldName, Validation\ValidatorInterface $validator)
    {
        $this->add($fieldName->value(), $validator);
    }

}