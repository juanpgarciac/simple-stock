<?php

namespace Models;

use Core\Classes\ModelRepository;

class ProductRepository extends ModelRepository
{
    protected string $table = 'product';
    protected array $fields = ['name','presentation','unit','category','stock'];

    /**
     * @param array<mixed> $recordArray
     * 
     * @return Product
     */
    public static function fromState(array $recordArray): Product
    {
        $product = new Product($recordArray['name'], $recordArray['presentation'], $recordArray['unit'], $recordArray['category']);
        $product->setValue('id', $recordArray['id']);
        return $product;
    }
}
