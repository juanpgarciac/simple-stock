<?php

namespace Models;

use Core\Classes\Model;
use Core\Models\StockTransaction;

class Product extends Model
{
    protected ?int $id;
    protected string $name;
    protected string $presentation;
    protected string $unit;
    protected string $category;
    protected float  $stock = 0.0;


    /**
     * @param string $name
     * @param string $presentation
     * @param string $unit
     * @param string $category
     */
    public function __construct(string $name, string $presentation = '1', string $unit = 'unit', string $category = '', float $stock = 0.0)
    {
        $this->id = null;
        $this->name = $name;
        $this->presentation = $presentation;
        $this->unit = $unit;
        $this->category = $category;
        $this->stock = $stock;
    }


    /**
     * @param StockTransaction $stockAdjustment
     *
     * @return void
     */
    public function updateStock(StockTransaction $stockAdjustment): void
    {
        $this->stock += $stockAdjustment->getAmount();
    }
}
