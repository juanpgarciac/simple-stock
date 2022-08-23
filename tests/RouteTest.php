<?php

declare(strict_types=1);

use Core\Classes\FakeClass;
use Core\Classes\RouteHandler;
use Core\Classes\Router;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function test_router_register_failure_due_empty_route()
    {
        $this->expectException(InvalidArgumentException::class);

        router()->clearRoutePool();

        router()->registerRoute('');
    }

    public function test_router_register_failure_due_bad_method()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage("Method parameter should be ".implode('|', RouteHandler::METHODS).", 'NONAMETHOD' given");

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

        $f = function () {
            echo 'tets';
        };

        router()->registerRoutes(['/','/home','/about', ['/another-route',$f,'GET']]);

        $routes = router()->getRoutePool();

        $this->assertCount(4, $routes);
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
        router()->registerRoute(new RouteHandler('/route3', 'text', 'POST'));

        //with route handler helper
        router()->registerRoute(RouteHandler::create(['/route4','','POST']));

        $this->assertCount(3, router()->getRoutePool());

        $this->assertCount(2, router()->getRoutePool(RouteHandler::POST));
    }

    public function test_route_handler_id_correctlly_set()
    {
        router()->clearRoutePool();

        $route1 = new RouteHandler('/test-route1');

        $route2 = new RouteHandler('/test-route2/:id/edit');

        $route3 = new RouteHandler('/test-route3/:id/:name/edit');

        $pattern = RouteHandler::PARAMETER_PATTERN;
        $this->assertSame('\/test\-route1', $route1->id());
        $this->assertSame('\/test\-route2\/'.$pattern.'\/edit', $route2->id());
        $this->assertSame('\/test\-route3\/'.$pattern.'\/'.$pattern.'\/edit', $route3->id());
    }

    public function test_route_handler_has_parameters()
    {
        router()->clearRoutePool();

        $route1 = new RouteHandler('/test-route1');

        $route2 = new RouteHandler('/test-route2/:id/edit');

        $route3 = new RouteHandler('/test-route3/:id/:name/edit');

        $this->assertCount(0, $route1->getURIParameters());
        $this->assertCount(1, $route2->getURIParameters());

        $parameters =  $route3->getURIParameters();
        $this->assertContains('id', $parameters);
        $this->assertContains('name', $parameters);
    }

    public function test_find_route_with_uri_from_pool()
    {
        router()->clearRoutePool();

        $route1 =  new RouteHandler('/test-route1', '', RouteHandler::POST);

        router()->registerRoutes([
            $route1,
            new RouteHandler('/test-route2/:id/edit'),
            new RouteHandler('/test-route3/:id/:name/edit'),
        ]);

        $route4 = RouteHandler::create('/non-existing-route');

        $this->assertTrue(router()->routeExists($route1->getBaseURI(), $route1->getMethod()));
        $this->assertTrue(router()->routeExists($route1->getBaseURI().'?var=1&var2=true', $route1->getMethod()));
        $this->assertTrue(router()->routeExists('/test-route2/55/edit', RouteHandler::GET));
        $this->assertTrue(router()->routeExists('/test-route3/123456/juan/edit'));

        $this->assertFalse(router()->routeExists($route4->getBaseURI(), RouteHandler::POST));
        $this->assertFalse(router()->routeExists('/test-route2/123456/juan/edit'));
    }

    public function test_not_found_route_default()
    {
        router()->setNotFoundRoute(RouteHandler::notFoundRoute());
        $this->assertSame('Not Found', router()->route('/not-existing-route'));
        $this->assertSame('/404', router()->getNotFoundRoute()->getBaseURI());
    }

    public function test_not_found_route_custom()
    {
        router()->setNotFoundRoute(['/not-found',fn () => 'Sorry not found']);

        $this->assertSame('Sorry not found', router()->route('/not-existing-route'));
        //$this->assertSame('HTTP/1.1 404 Not Found',get_headers('http://127.0.0.1:8888/')[0]);
        $this->assertSame('/not-found', router()->getNotFoundRoute()->getBaseURI());
    }

    public function test_route_can_do_the_callback()
    {
        router()->clearRoutePool();
        $route1 =  new RouteHandler('/test-route1', fn () => 'hello', RouteHandler::POST);
        $route2 =  new RouteHandler('/test-route2/:id/edit', fn () => 'hi, this is the result');
        $route3 =  new RouteHandler('/test-route3', fn () => 150);
        $route4 =  new RouteHandler('/get-router', fn () => app());
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

    public function test_route_handler_callback_receives_vars()
    {

        //send route with empty vars
        $route1 = new RouteHandler('/route-without-vars', fn () => null);

        $this->assertNull($route1->callback());

        //request route with vars

        $route2 = new RouteHandler('/route-with-var', fn ($something) => is_string($something));

        $this->assertTrue($route2->callback('string'));

        $route3 = new RouteHandler('/route-with-2-vars', fn ($a, $b) => $a + $b);

        $this->assertEquals(5, $route3->callback(['a'=>2,'b'=>3]));
    }

    public function test_routed_route_callback_receives_vars()
    {
        //send route with empty vars
        $route1 = new RouteHandler('/route-without-vars', fn () => null);
        $route2 = new RouteHandler('/route-with-var', fn ($request) => 'string' === $request);
        $route3 = new RouteHandler('/route-with-2-vars', fn ($a, $b) => $a + $b);

        router()->clearRoutePool();

        router()->registerRoutes([$route1, $route2, $route3]);
        /* */
        $this->assertNull(router()->route('/route-without-vars'));
        $this->assertTrue(router()->route('/route-with-var', RouteHandler::GET, 'string'));
        $this->assertEquals(5, router()->route('/route-with-2-vars', RouteHandler::GET, ['a'=>2,'b'=>3]));
        /* */
    }

    public function test_routed_route_callback_Receive_url_parameters()
    {
        $route1 = new RouteHandler('/route1/:var1/:var2', fn ($var1, $var2) => $var1 + $var2);

        router()->clearRoutePool();

        router()->registerRoutes([$route1]);

        $params = $route1->getParametersValues("/route1/2/3");

        $this->assertIsArray($params);
        $this->assertArrayHasKey('var1', $params);
        $this->assertArrayHasKey('var2', $params);

        $this->assertEquals(5, router()->route("/route1/2/3"));
    }

    public function test_route_callback_can_call_class_methods()
    {

        /* */

        $route1 = new RouteHandler('/route1', [FakeClass::class,'doSomethingStatically']);
        $this->assertSame('something statically', $route1->callback());


        $route2 = new RouteHandler('/route2', [FakeClass::class,'doSomethingWithInstance']);
        $this->assertSame('something with instance', $route2->callback());


        $callback = '\Core\Classes\FakeClass@doSomethingWithInstance';
        $route3 = new RouteHandler('/route3', $callback);
        $this->assertSame('something with instance', $route3->callback());

        $callback = 'FakeClass@doSomethingInstanceWithVars';
        $route3 = new RouteHandler('/route4', $callback);
        $this->assertSame('something with instance using 1 & 2', $route3->callback([1,2]));
    }

    public function test_routed_route_callback_from_classes_methods()
    {
        $route1 = new RouteHandler('/route1', [FakeClass::class,'doSomethingStatically']);
        $route2 = new RouteHandler('/route2/:param1/:param2', [FakeClass::class,'doSomethingInstanceWithVars']);
        $route3 = new RouteHandler('/route3', [FakeClass::class,'doSomethingStaticallyWithVars']);

        router()->add($route1, $route2, $route3);

        $this->assertSame('something statically', router()->route($route1->getBaseURI()));

        $this->assertSame('something with instance using 100 & 200', router()->route('/route2/100/200'));

        $this->assertSame('something statically using 300 & 400', router()->route('/route3', $route3->getMethod(), [300,400]));
    }

    public function test_request_global_function()
    {
        router()->add(
            ['/route1',fn () =>  request()['var1'] ],
            ['/route2/:a',fn () =>  request('a') + request('request')['b'] ],
            ['/route2/:a/:b',fn () =>  request('a') + request('b') ],
            ['/route3/:a/:b/:c',fn () =>  request('a') + request('b') + request('c') ],
            ['/route3/:a/:request',fn () =>  request('a') + request('request') + request('_GET')['c'] ],
            //['/route4/:param1/:param2',fn() =>  request('a') + request('request') + request('_GET')['c'] ],
        );

        $this->assertSame('Something', router()->route('/route1', 'GET', ['var1' => 'Something']));
        $this->assertSame(5, router()->route('/route2/3', 'GET', ['b' => 2]));
        $this->assertSame(10, router()->route('/route2/3/7', 'GET', ['c' => 100]));
        $this->assertSame(6, router()->route('/route3/1/2/3', 'GET', ['b' => 20]));
        $this->assertSame(14, router()->route('/route3/5/5', 'GET', ['c' => 4]));
    }
}
