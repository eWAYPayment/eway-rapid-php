<?php

namespace Eway\Rapid\Model\Support;

/**
 * Trait CanGetClassTrait.
 */
trait CanGetClassTrait
{
    /**
     * Because PHP 5.4 doesn't have ::class yet
     *
     * @return string
     */
    public static function getClass()
    {
        return get_called_class();
    }
}
