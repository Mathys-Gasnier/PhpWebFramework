<?php
namespace Framework;

use Framework\ControllerManager;
use Framework\Request;
use Framework\Descriptors\Controller as DescriptorController;
use Framework\Descriptors\Params\BodyParserParam;
use Framework\Descriptors\Params\QueryParam;
use Framework\Descriptors\Route as DescriptorRoute;

class Invoker {
    public static function invokeRoute(DescriptorRoute $route, DescriptorController $controller, Request $request): Response {
        $args = [];

        // resolving route params to real value args
        foreach($route->getParams() as $param) {
            if($param instanceof QueryParam) {
                // If the query param doesn't exist we return an 400 error
                if(!isset($request->getParams()[$param->getMetadata()->getName()])) {
                    return new Response("Missing query param `" . $param->getMetadata()->getName() . "`", 400);
                }
                $args[] = $request->getParams()[$param->getMetadata()->getName()];
            }else if($param instanceof BodyParserParam) {
                $args[] = new ($param->getBodyParserClassName())($request->getBody());
            }
        }
        
        return $route->getMethod()->invoke($controller->getInstance(), ...$args);
    }
}

class Router {

    public function __construct(
        private ControllerManager $controllerManager
    ) {}

    public function route(Request $request): Response {
        $path = ltrim($request->getPath(), '/');
        
        foreach($this->controllerManager->getControllers() as $controller) {
            $controllerPath = ltrim($controller->getMetadata()->getPath(), '/');
            
            // If the start of the path is the same as the controller path, then we can proceed
            if(!str_starts_with($path, $controllerPath)) continue;
            
            // We try to match a route in the controller
            $subPath = ltrim(substr($path, strlen($controllerPath)), '/');
            $route = $this->routeInController($request, $subPath, $controller);

            // If we found a route we invoke it and return the response
            if($route == null) continue;
            return Invoker::invokeRoute($route, $controller, $request);
        }

        return new Response("Not Found", 404);
    }

    private function routeInController(Request $request, string $path, DescriptorController $controller): ?DescriptorRoute {
        foreach($controller->getRoutes() as $route) {
            // Try to find a matching route by method and path
            if($route->getMetadata()->getMethod() != $request->getMethod()) continue;
            
            $routePath = ltrim($route->getMetadata()->getPath(), '/');

            if($path != $routePath) continue;

            return $route;
        }
        
        return null;
    }
    
}
