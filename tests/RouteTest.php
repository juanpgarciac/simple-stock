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

    public function test_router_register_failure_due_empty_route()
    {

        $this->expectException(InvalidArgumentException::class);

        router()->clearRoutePool();

        router()->registerRoute('');

    }

    public function test_router_register_failure_due_bad_method()
    {

        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage("Method parameter should be ".implode('|',RouteHandler::METHODS).", 'NONAMETHOD' given");

        router()->clearRoutePool();

        router()->registerRoute(['/','callback','NONAMETHOD']);

    }

    public function test_router_register_failure_due_bad_URI()
    {

        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage("The array should at least have a string value as a URI, in the 'uri' or 0 index");

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
        router()->clearRoutePool();

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
        router()->clearRoutePool();

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

    public function test_route_can_do_the_callback()
    {
        router()->clearRoutePool();
        $route1 =  new RouteHandler('/test-route1','hello', RouteHandler::POST);
        $route2 =  new RouteHandler('/test-route2/:id/edit',function(){ return 'hi, this is the result';  });
        $route3 =  new RouteHandler('/test-route3',function(){ return 150;  });
        $route4 =  new RouteHandler('/get-router',function(){ return app();  });
        router()->registerRoutes([
            $route1,
            $route2,
            $route4
        ]);

        $this->assertSame('hello', router()->route('/test-route1', RouteHandler::POST));
        $this->assertSame('hi, this is the result', router()->route('/test-route2/100/edit'));
        $this->assertSame(150, call_user_func($route3->getCallback()));

        $this->assertInstanceOf(\Core\Classes\App::class, call_user_func($route4->getCallback()));


    }

    public function test_not_found_route_default()
    {
        //router()->clearRoutePool();
        $this->assertSame('Not found',router()->route('/not-existing-route'));
        $this->assertSame('/404',router()->getNotFoundRoute()->getBaseURI());
    }

    public function test_not_found_route_cutom()
    {
        //router()->clearRoutePool();
        router()->setNotFoundRoute(['/not-found','Sorry not found']);

        $this->assertSame('Sorry not found',router()->route('/not-existing-route'));
        //$this->assertSame('HTTP/1.1 404 Not Found',get_headers('http://127.0.0.1:8888/')[0]);
        $this->assertSame('/not-found',router()->getNotFoundRoute()->getBaseURI());


    }

}
