<?php

namespace Models;

use Core\Classes\Model;

class StockTransaction extends Model
{
    protected float $amount;
    protected float $stock;
    protected string $observation;
    protected string $date;
    protected ?int $product_id;

    /**
     * @param null $product_id
     * @param float $amount
     * @param mixed string
     * @param string|null $date
     * @param float $stock
     */
    public function __construct($product_id = null, float $amount = 0.0, string $observation = 'Stock Adjustment', string $date = null, float $stock = 0.0)
    {
        $this->product_id = $product_id;
        $this->amount = $amount;
        $this->stock = $stock ?? $amount;
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
    public function getStock(): float
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
