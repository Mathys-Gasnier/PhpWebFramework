<?php
namespace Framework;

use Framework\ControllerManager;
use Framework\Request;
use Framework\Descriptors\Controller as DescriptorController;
use Framework\Descriptors\Params\BodyParserParam;
use Framework\Descriptors\Params\QueryParam;
use Framework\Descriptors\Route as DescriptorRoute;

class Invoker {
    public static function invokeRoute(DescriptorRoute $route, Request $request): Response {
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
        
        return $route->getMethod()->invoke($route->getOwner()->getInstance(), ...$args);
    }
}

class Router {

    public function __construct(
        private ControllerManager $controllerManager
    ) {}

    public function route(Request $request): Response {
        $path = trim($request->getPath(), '/');
        
        foreach($this->controllerManager->getControllers() as $controller) {
            $controllerPath = trim($controller->getMetadata()->getPath(), '/');
            
            // If the start of the path is the same as the controller path, then we can proceed
            if(!str_starts_with($path, $controllerPath)) continue;
            
            // We try to match a route in the controller
            $subPath = trim(substr($path, strlen($controllerPath)), '/');
            $route = $this->routeInController($request, $subPath, $controller);

            // If we found a route we invoke it and return the response
            if($route == null) continue;
            return Invoker::invokeRoute($route, $request);
        }

        return new Response("Not Found", 404);
    }

    private function routeInController(Request $request, string $path, DescriptorController $controller): ?DescriptorRoute {

        foreach($controller->getChilds() as $childController) {
            $controllerPath = trim($childController->getMetadata()->getPath(), '/');

            // If the start of the path is not the same as the controller path, then we can skip this controller
            if(!str_starts_with($path, $controllerPath)) continue;

            $subPath = trim(substr($path, strlen($controllerPath)), '/');
            
            return $this->routeInController($request, $subPath, $childController);
        }

        foreach($controller->getRoutes() as $route) {
            // Try to find a matching route by method and path
            if($route->getMetadata()->getMethod() != $request->getMethod()) continue;
            
            $routePath = trim($route->getMetadata()->getPath(), '/');

            if($path != $routePath) continue;

            return $route;
        }
        
        return null;
    }
    
}
