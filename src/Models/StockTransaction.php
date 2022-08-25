<?php

namespace Models;

use Core\Classes\Model;

class StockTransaction extends Model
{
    protected float $amount;
    protected string $observation;
    protected string $date;
    protected ?int $product_id;

    /**
     * @param null $product_id
     * @param float $amount
     * @param string $observation
     * @param string|null $date
     */
    public function __construct($product_id = null, float $amount = 0, string $observation = 'Stock Adjustment', string $date = null)
    {
        $this->product_id = $product_id;
        $this->amount = $amount;
        $this->observation = $observation;
        $this->date = $date ?? date('Y-m-d h:i:s');
    }

    public function getProductID():int
    {
        return $this->product_id;
    }
    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getObservation(): string
    {
        return $this->observation;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
