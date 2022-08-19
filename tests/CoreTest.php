<?php

declare(strict_types=1);

use Core\Classes\App;
use Core\Classes\Router;
use PHPUnit\Framework\TestCase;

final class CoreTest extends TestCase
{

    public function test_singleton_class_make_real_singletons()
    {
        $app1 = App::getInstance(); 
        $app2 = App::getInstance(); 

        $router1 = Router::getInstance();
        $router2 = Router::getInstance();

        $this->assertSame($app1,$app2);

        $this->assertSame($router1,$router2);

    }

    public function test_global_app_functions_instantiates_singletons()
    {
        $app1 = App::getInstance(); 

        $router1 = Router::getInstance();

        $this->assertSame($app1,app());

        $this->assertSame($router1,router());
    }


}