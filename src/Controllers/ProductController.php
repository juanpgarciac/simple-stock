<?php

namespace Controllers;

use Core\Classes\Controller;

class ProductController extends Controller
{
    /**
     * @return void
     */
    public function index(): void
    {
        echo "<h2>This is the Product index</h2>";
    }

    /**
     * @return void
     */
    public function show(string $id): void
    {
        echo "<h2>This is the Product $id detail</h2>";
    }
}
