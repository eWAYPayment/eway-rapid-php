<?php

namespace Eway\Rapid\Model;

use Eway\Rapid\Contract\Arrayable;
use Eway\Rapid\Model\Support\CanGetClassTrait;
use Eway\Rapid\Model\Support\CanValidateEnumTrait;
use Eway\Rapid\Model\Support\CanValidateInstanceTrait;
use Eway\Rapid\Model\Support\HasAttributesTrait;

/**
 * Class AbstractModel.
 */
abstract class AbstractModel implements Arrayable
{
    use HasAttributesTrait, CanValidateInstanceTrait, CanValidateEnumTrait, CanGetClassTrait;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributesToArray();
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
