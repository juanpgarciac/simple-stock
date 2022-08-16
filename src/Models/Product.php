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


    public function __construct($name, $presentation = '1', $unit = 'unit', $category = '')
    {
        $this->id = null;
        $this->name = $name;
        $this->presentation = $presentation;
        $this->unit = $unit;
        $this->category = $category;
        $this->stock = 0.0;
    }


    public function updateStock(StockTransaction $stockAdjustment)
    {
        $this->stock += $stockAdjustment->getAmount();
    }
}
