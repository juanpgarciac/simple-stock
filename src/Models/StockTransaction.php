<?php

namespace Core\Models;

class StockTransaction
{
    private float $amount;
    private string $observation;
    private string $date;

    /**
     * @param float $amount
     * @param string $observation
     * @param string $date
     */
    public function __construct(float $amount, string $observation = 'Stock Adjustment', string $date = date('Ymdhis'))
    {
        $this->amount = $amount;
        $this->observation = $observation;
        $this->date = $date;
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
