<?php

namespace Eway\Rapid\Model\Support;

use Eway\Rapid\Validator\EnumValidator;

/**
 * Class CanValidateEnumTrait.
 */
trait CanValidateEnumTrait
{
    /**
     * @param string $class
     * @param string $field
     * @param mixed  $value
     */
    protected function validateEnum($class, $field, $value)
    {
        $this->attributes[$field] = EnumValidator::validate($class, $field, $value);
    }
}
