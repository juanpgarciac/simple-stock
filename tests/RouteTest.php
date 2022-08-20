<?php

declare(strict_types=1);

use Core\Classes\RouteHandler;
use Core\Classes\Router;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function test_router_clear_and_register_routes_on_app_run()
    {
        $routes = router()->getRoutePool();

        $this->assertGreaterThanOrEqual(1,count($routes));
    }

    public function test_router_register_failure_due_emptyroute()
    {

        $this->expectException(InvalidArgumentException::class);

        router()->clearRoutePool();

        router()->registerRoute('');

    }

    public function test_router_register_failure_due_bad_array_configuration()
    {

        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage("Method parameter should be ".implode('|',RouteHandler::METHODS).", 'NONAMETHOD' given");

        router()->clearRoutePool();

        router()->registerRoute(['/','callback','NONAMETHOD']);

    }

    public function test_router_register_failure_due_bad_array_configuration_2()
    {

        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage("The array should at least have a string value as a route, in the 'route' or 0 index");

        router()->clearRoutePool();

        router()->registerRoute([124,'rattatoile' => '/']);

    }

    public function test_router_register_routes_from_array()
    {

        router()->clearRoutePool();

        $f = function(){ echo 'tets';  };

        router()->registerRoutes(['/','/home','/about', ['/another-route',$f,'GET']]);

        $routes = router()->getRoutePool();

        $this->assertCount(4,$routes);
    }

    public function test_router_register_routes_individually()
    {

        router()->clearRoutePool();

        //with string
        router()->registerRoute('/');

        //with array
        router()->registerRoute(['/route1']);

        //with complete array
        router()->registerRoute(['/route2','string callback','GET']);

        
        //with route handler constructor
        router()->registerRoute(new RouteHandler('/route3','text','POST'));

        //with route handler helper
        router()->registerRoute(RouteHandler::create(['/route4','','POST']));

        $this->assertCount(3,router()->getRoutePool());

        $this->assertCount(2,router()->getRoutePool(RouteHandler::POST));
    }


}
