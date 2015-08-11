<?php

namespace Eway\Rapid\Model\Support;

use Eway\Rapid\Validator\ClassValidator;

/**
 * Class CanValidateInstanceTrait.
 */
trait CanValidateInstanceTrait
{
    /**
     * @param string $class
     * @param string $field
     * @param mixed  $value
     */
    protected function validateInstance($class, $field, $value)
    {
        if (is_null($value)) {
            $this->attributes[$field] = null;
        } else {
            $this->attributes[$field] = ClassValidator::getInstance($class, $value);
        }
    }
}
