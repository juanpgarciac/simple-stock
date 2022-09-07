<?php 

namespace Core\Command;

use Core\Classes\Route\Router;

trait DefaultClosures
{

    private function list_routes()
    {
        $routePool = Router::getInstance()->getRoutePool('*');
        foreach ($routePool as $methodKey => $methodRoutePool) {   
            foreach ($methodRoutePool as $uri_pattern => $route) {
                /** @var \Core\Classes\Route\RouteHandler $route*/
                print $methodKey.' => '.$route->getBaseURI()."\n";
            }
        }
    }

    private function create_model(string $name = 'UnnamedModel')
    {
        $modelClassName = $name;
        $filecontent = include path(SRC_DIR,'Core','Command','Templates','model');
        $filecontent = str_replace('@ClassName',$modelClassName,$filecontent);
        $filePath = path(SRC_DIR,'Models',$modelClassName.'.php');
        file_put_contents($filePath, $filecontent);
    }

    private function create_controller(string $name = 'UnnamedController')
    {
        $modelClassName = $name;
        $filecontent = include path(SRC_DIR,'Core','Command','Templates','controller');
        $filecontent = str_replace('@ClassName',$modelClassName,$filecontent);
        $filePath = path(SRC_DIR,'Controllers',$modelClassName.'.php');
        file_put_contents($filePath, $filecontent);
    }

    private function create_repository(string $name = 'UnnamedRepository', string $table = '')
    {
        $modelClassName = $name;
        $filecontent = include path(SRC_DIR,'Core','Command','Templates','repository');
        $filecontent = str_replace('@ClassName',$modelClassName,$filecontent);
        $filecontent = str_replace('@table',$table,$filecontent);
        $filePath = path(SRC_DIR,'Models',$modelClassName.'.php');
        file_put_contents($filePath, $filecontent);
    }

}