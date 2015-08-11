<?php

namespace Eway\Rapid\Validator;

/**
 * Class ClassValidator.
 */
abstract class ClassValidator
{
    /**
     * @param $class
     * @param $instance
     *
     * @return mixed
     */
    public static function getInstance($class, $instance)
    {
        if (is_a($instance, $class)) {
            return $instance;
        }

        return new $class($instance);
    }
}
