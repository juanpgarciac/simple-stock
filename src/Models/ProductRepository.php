<?php

namespace Models;

use Core\Classes\ModelRepository;

class ProductRepository extends ModelRepository
{
    protected string $table = 'product';
    protected array $fields = ['name','presentation','unit','category','stock'];
}
