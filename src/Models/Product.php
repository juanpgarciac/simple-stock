<?php

namespace Models;

use Core\IDBConnection;
use Core\Model;

class Product extends Model
{
    protected string $table = 'product';
    protected array $fields = ['id','name','presentation','unit','category'];
    
}

