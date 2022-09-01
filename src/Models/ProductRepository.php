<?php

namespace Models;

use Core\Classes\ModelRepository;

class ProductRepository extends ModelRepository
{
    protected string $table = 'product';

    protected array $fields = ['name','presentation','stock','category_id', 'unit_id'];

    protected array $nullable = ['category_id', 'unit_id'];

    protected string $modelClass = Product::class;
}
