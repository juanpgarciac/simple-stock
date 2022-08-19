<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function test_router_clear_and_register_routes_on_app_run()
    {
        $routes = router()->getRoutePool();

        $this->assertGreaterThanOrEqual(1,count($routes));
    }

    public function test_router_register_routes_from_array()
    {

        router()->clearRoutePool();

        router()->registerRoutes(['/','/home','/about']);

        $routes = router()->getRoutePool();

        $this->assertCount(3,$routes);
    }

    public function test_router_register_individual_route()
    {

        router()->clearRoutePool();

        router()->registerRoute('/');

        $routes = router()->getRoutePool();

        $this->assertCount(1,$routes);
    }


}
