<?php

namespace Eway\Rapid\Model\Support;

/**
 * Class HasOptionsTrait.
 */
trait HasOptionsTrait
{
    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptionsAttribute($options)
    {
        if (!is_array($options)) {
            throw new \InvalidArgumentException('Options must be an array');
        }

        $this->attributes['Options'] = $options;

        return $this;
    }
}
