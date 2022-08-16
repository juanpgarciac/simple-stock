<?php

namespace Models;

use Core\Classes\ModelRepository;

class ProductRepository extends ModelRepository
{
    protected string $table = 'product';
    protected array $fields = ['name','presentation','unit','category','stock'];

    /**
     * @param array{ id:string, name: string, presentation: string, unit: string, category: string, stock: float } $recordArray
     *
     * @return Product
     */
    public static function fromState(array $recordArray): Product
    {
        $product = new Product($recordArray['name'], $recordArray['presentation'], $recordArray['unit'], $recordArray['category'], $recordArray['stock']);
        $product->setValue('id', $recordArray['id']);
        return $product;
    }
}
