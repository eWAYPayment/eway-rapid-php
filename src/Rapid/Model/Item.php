<?php

namespace Eway\Rapid\Model;

/**
 * Class Item.
 *
 * @property string $SKU         ID of the Line Item's product
 * @property string $Description Product description of the item
 * @property int    $Quantity    The number of items
 * @property int    $UnitCost    Price (in cents) of each item
 * @property int    $UnitTax     Unit Tax for each item
 * @property int    $Tax         Combined tax (in cents) for all the items
 * @property int    $Total       Total (including Tax) in cents for all the items.
 */
class Item extends AbstractModel
{
    protected $fillable = [
        'SKU',
        'Description',
        'Quantity',
        'UnitCost',
        'Tax',
        'Total',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->calculateTotal();
    }


    /**
     * Used to set the line item's values so that the total and tax add up correctly.
     *
     * @param int $price
     * @param int $unitTax
     * @param int $quantity
     *
     * @return $this
     */
    public function calculate($price, $unitTax, $quantity = 0)
    {
        $this->Tax = $unitTax * $quantity;
        $this->Total = $this->Tax + ($quantity * $price);

        return $this;
    }

    /**
     * @return $this
     */
    private function calculateTotal()
    {
        if (isset($this->Quantity) && isset($this->UnitCost)) {
            if (isset($this->Tax)) {
                $this->Total = $this->Tax + ($this->Quantity * $this->UnitCost);
            } else {
                $this->Total = $this->Quantity * $this->UnitCost;
            }
        }

        return $this;
    }
}
