<?php


declare(strict_types=1);

use Core\Classes\RouteHandler;
use Core\Classes\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    private string $viewsdir;
    protected function setUp(): void
    {
        parent::setUp();
        $this->viewsdir = path(configdir('tests_resources'),'views');
    }
    public function test_view_return_string_content(): void
    {
        $view = new View('testview','this is content',true);

        $this->assertSame('this is content', $view());
    }

    public function test_view_from_file_and_render(): void
    {        
        $view = new View('testview','',true,$this->viewsdir);
        $this->assertSame('this is content from php template', $view());
    }

    public function test_view_as_route_callback()
    {
        $view = new View('testview','this is content on route',true);
        $route = new RouteHandler('/route-with-content-view', $view);

        $this->assertSame('this is content on route', $route->callback());

    }

    public function test_view_as_routed_route_callback()
    {
        $view1 = new View('fakeview','this is content on view1',true);
        $view2 = new View('testview','',true,$this->viewsdir);
        //$view2 = new View('testview','',true);
        $route1 = new RouteHandler('/route-with-content-view', $view1);
        $route2 = new RouteHandler('/route-with-content-view-from-file', $view2);
        //$route = new RouteHandler('/route-with-content-view-from-file', $view3);

        router()->add($route1, $route2);

        $this->assertSame('this is content on view1',router()->route('/route-with-content-view'));
        $this->assertSame('this is content from php template',router()->route('/route-with-content-view-from-file'));


        //$this->assertSame('this is content on route', $route->callback());
    }

    public function test_view_receiving_args()
    {
        $view1 = new View('fakeview','this is content on view1 with output %s, %s and %s',true);
        $view2 = new View('testview','',true);
        //$view2 = new View('testview','',true);
        $route1 = new RouteHandler('/route-with-content-view/:a/:b/:c', $view1);
        //$route2 = new RouteHandler('/route-with-content-view-from-file', $view2);
        //$route = new RouteHandler('/route-with-content-view-from-file', $view3);

        router()->add($route1);

        $this->assertSame('this is content on view1 with output 1, 2 and 3',router()->route('/route-with-content-view/1/2/3'));
        //$this->assertSame('this is content from php template',router()->route('/route-with-content-view-from-file'));


        //$this->assertSame('this is content on route', $route->callback());
    }
}
