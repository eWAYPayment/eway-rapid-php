<?php

namespace Eway\Rapid\Model\Support;

use Eway\Rapid\Contract\Arrayable;

trait HasAttributesTrait
{
    /**
     * The cache of the mutated attributes for each class.
     *
     * @var array
     */
    protected static $mutatorCache = [];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill(array $attributes)
    {
        $class = get_called_class();

        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }

        return $this;
    }

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isFillable($key)
    {
        if (in_array($key, $this->fillable)) {
            return true;
        }

        return empty($this->fillable);
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = $this->getAttributeFromArray($key);

        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            $method = 'set'.$this->getStudlyCase($key).'Attribute';

            return $this->{$method}($value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasGetMutator($key)
    {
        return method_exists($this, 'get'.$this->getStudlyCase($key).'Attribute');
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasSetMutator($key)
    {
        return method_exists($this, 'set'.$this->getStudlyCase($key).'Attribute');
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = $this->attributes;

        foreach ($attributes as $key => $value) {
            $attributes[$key] = $this->_toArrayRecursive($value);
        }

        return $attributes;
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if an attribute exists on the model.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]) || ($this->hasGetMutator($key) && !is_null($this->getAttribute($key)));
    }

    /**
     * Unset an attribute on the model.
     *
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        trigger_error(sprintf("Undefined property '%s' in class '%s'", $key, get_called_class()), E_USER_NOTICE);

        return null;
    }

    /**
     * @param $str
     *
     * @return mixed
     */
    protected function getStudlyCase($str)
    {
        $str = ucwords(str_replace(['-', '_'], ' ', $str));

        return str_replace(' ', '', $str);
    }

    /**
     * @param $subject
     *
     * @return array
     */
    private function _toArrayRecursive($subject)
    {
        if (is_array($subject)) {
            foreach ($subject as $key => $value) {
                $subject[$key] = $this->_toArrayRecursive($value);
            }

            return $subject;
        } else {
            return $subject instanceof Arrayable ? $subject->toArray() : $subject;
        }
    }
}
