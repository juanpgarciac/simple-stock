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

    public function test_route_handler_id_correctlly_set()
    {
        $route1 = new RouteHandler('/test-route1');

        $route2 = new RouteHandler('/test-route2/:id/edit');

        $route3 = new RouteHandler('/test-route3/:id/:name/edit');

        $pattern = RouteHandler::PARAMETER_PATTERN;
        $this->assertSame('\/test-route1',$route1->id());
        $this->assertSame('\/test-route2\/'.$pattern.'\/edit',$route2->id());
        $this->assertSame('\/test-route3\/'.$pattern.'\/'.$pattern.'\/edit',$route3->id());
    
    }

    public function test_route_handler_has_parameters()
    {
        $route1 = new RouteHandler('/test-route1');

        $route2 = new RouteHandler('/test-route2/:id/edit');

        $route3 = new RouteHandler('/test-route3/:id/:name/edit');

        $this->assertCount(0,$route1->getURIParameters());
        $this->assertCount(1,$route2->getURIParameters());

        $parameters =  $route3->getURIParameters();
        $this->assertContains('id',$parameters);
        $this->assertContains('name',$parameters);
    
    }

    public function test_find_route_with_uri_from_pool()
    {
        router()->clearRoutePool();
        
        $route1 =  new RouteHandler('/test-route1','', RouteHandler::POST);

        router()->registerRoutes([
            $route1,
            new RouteHandler('/test-route2/:id/edit'),
            new RouteHandler('/test-route3/:id/:name/edit'),
        ]);

        $route4 = RouteHandler::create('/non-existing-route');

        $this->assertTrue(router()->routeExists($route1->getBaseURI(), $route1->getMethod()));
        $this->assertTrue(router()->routeExists('/test-route2/55/edit', RouteHandler::GET));
        $this->assertTrue(router()->routeExists('/test-route3/123456/juan/edit'));

        $this->assertFalse(router()->routeExists($route4->getBaseURI(),RouteHandler::POST));
        $this->assertFalse(router()->routeExists('/test-route2/123456/juan/edit'));

        


    }


}
