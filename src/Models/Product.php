<?php

namespace Models;

use Core\Classes\Model;
use Models\StockTransaction;

class Product extends Model
{
    protected ?int $id = null;
    protected string $name;
    protected string $presentation;
    protected ?int $unit_id;
    protected ?int $category_id;
    protected ?float  $stock = null;
    protected ?string $category = null; //from category table
    protected ?string $unit = null; //from unit table


    /**
     * @param string $name
     * @param string $presentation
     * @param string $unit
     * @param string $category
     */
    public function __construct(string $name = '', string $presentation = '1', int|string $unit_id = null, int|string $category_id = null, ?float $stock = null)
    {
        $this->id = null;
        $this->name = $name;
        $this->presentation = $presentation;
        $this->unit_id = !empty($unit_id)?(int)$unit_id:null;
        $this->category_id = !empty($category_id)?(int)$category_id:null;
        $this->stock = $stock;
    }


}
