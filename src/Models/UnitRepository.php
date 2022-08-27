<?php

namespace Models;

use Core\Classes\ModelRepository;

class UnitRepository extends ModelRepository
{
    protected string $table = 'unit';
    /**
     * @var array<string>
     */
    protected array $fields = ['id','unit'];

}