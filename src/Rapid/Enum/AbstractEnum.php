<?php

namespace Eway\Rapid\Enum;

use Eway\Rapid\Model\Support\CanGetClassTrait;

/**
 * Class AbstractEnum.
 */
abstract class AbstractEnum
{
    use CanGetClassTrait;

    /**
     * @var array
     */
    private static $constCacheArray = null;

    /**
     * @var array
     */
    private static $constValueCacheArray = null;

    /**
     * @param      $value
     * @param bool $strict
     *
     * @return bool
     */
    public static function isValidValue($value, $strict = false)
    {
        $values = self::getConstantValues();

        return in_array($value, $values, $strict);
    }

    /**
     * @param $name
     *
     * @return string
     */
    public static function getValidationMessage($name)
    {
        $allowedValues = self::getConstantValues();

        return sprintf('%s must be one of the following: %s', $name, implode(', ', $allowedValues));
    }

    /**
     * @return array
     */
    public static function getOptionsArray()
    {
        return self::getConstants();
    }

    /**
     * @return mixed
     */
    private static function getConstants()
    {
        if (null === self::$constCacheArray) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);

            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    /**
     * @return mixed
     */
    private static function getConstantValues()
    {
        if (null === self::$constValueCacheArray) {
            self::$constValueCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constValueCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);

            self::$constValueCacheArray[$calledClass] = array_values($reflect->getConstants());
        }

        return self::$constValueCacheArray[$calledClass];
    }
}
