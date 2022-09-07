<?php


declare(strict_types=1);

use Core\Classes\Route\RouteHandler;
use Core\Classes\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    private string $viewsdir;
    protected function setUp(): void
    {
        parent::setUp();
        $this->viewsdir = path(configdir('tests_resources'), 'views');
    }
    public function test_view_return_string_content(): void
    {
        $view = new View('testview', 'this is content', true);

        $this->assertSame('this is content', $view());
    }

    public function test_view_from_file_and_render(): void
    {
        $view = new View('testview', '', true, $this->viewsdir);
        $this->assertSame('this is content from php template', $view());
    }

    public function test_view_as_route_callback()
    {
        $view = new View('testview', 'this is content on route', true);
        $route = new RouteHandler('/route-with-content-view', $view);

        $this->assertSame('this is content on route', $route->callback());
    }

    public function test_view_as_routed_route_callback()
    {
        $view1 = new View('fakeview', 'this is content on view1', true);
        $view2 = new View('testview', '', true, $this->viewsdir);
        $route1 = new RouteHandler('/route-with-content-view', $view1);
        $route2 = new RouteHandler('/route-with-content-view-from-file', $view2);

        router()->add($route1, $route2);

        $this->assertSame('this is content on view1', router()->route('/route-with-content-view'));
        $this->assertSame('this is content from php template', router()->route('/route-with-content-view-from-file'));
    }

    public function test_view_receiving_args()
    {
        $view1 = new View('fakeview', 'this is content on view1 with output %s, %s and %s', true);
        $view2 = new View('testview', '', true);

        $route1 = new RouteHandler('/route-with-content-view/:a/:b/:c', $view1);

        router()->add($route1);

        $this->assertSame('this is content on view1 with output 1, 2 and 3', router()->route('/route-with-content-view/1/2/3'));
    }

    public function test_view_with_layout()
    {
        $view1Content = 'This is content on view1 with layout';
        $layoutContent = sprintf('header %s footer', $view1Content);

        $view1 = (new View('testview', $view1Content, true, $this->viewsdir))->layout('layout');
        $this->assertSame($layoutContent, $view1());
    }

    public function test_get_layout_name_from_view_file_using_doc_comment()
    {
        $tokens = token_get_all(file_get_contents(path($this->viewsdir, 'testviewwithdoc.phtml')));
        $filtered = array_filter($tokens, fn ($arr) => ($arr[0] == T_DOC_COMMENT || $arr[0] == T_COMMENT) && preg_match('/.*\@layout.*/', $arr[1]));
        $filtered = ($filtered[array_key_first($filtered)]);

        $this->assertContains(T_DOC_COMMENT, $filtered);

        $layout = preg_replace('/(\@(\blayout\b))|[^a-z\_\-\.0-9]|\b\*\/\b/i', '', explode(' ', $filtered[1]));
        $layout = (implode('', $layout));

        $this->assertSame('layouts/main123_45-67', str_replace('.', '/', $layout));
    }
}
