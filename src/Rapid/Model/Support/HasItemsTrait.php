<?php

namespace Eway\Rapid\Model\Support;

use Eway\Rapid\Model\Item;

/**
 * Trait HasItemsTrait.
 */
trait HasItemsTrait
{
    /**
     * @param array $items
     *
     * @return $this
     */
    public function setItemsAttribute($items)
    {
        if (!is_array($items)) {
            throw new \InvalidArgumentException('Items must be an array');
        }

        foreach ($items as $key => $item) {
            if (!($item instanceof Item)) {
                $items[$key] = new Item($item);
            }
        }

        $this->attributes['Items'] = $items;

        return $this;
    }
}
