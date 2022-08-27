<?php

namespace Models;

use Core\Classes\ModelRepository;

class CategoryRepository extends ModelRepository
{
    protected string $table = 'category';
    /**
     * @var array<string>
     */
    protected array $fields = ['id','category','parent_id'];

    protected array $nullable = ['parent_id'];

}