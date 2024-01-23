<?php
namespace Framework;

use Framework\ControllerManager;
use Framework\Request;
use Framework\Descriptors\Controller as DescriptorController;
use Framework\Descriptors\Route as DescriptorRoute;
use Framework\Descriptors\Params\QueryParam;
use Framework\Descriptors\Params\BodyParserParam;
use Framework\Descriptors\Params\HeaderParam;

class Invoker {
    public static function invokeRoute(DescriptorRoute $route, Request $request): Response {
        $args = [];

        // resolving route params to real value args
        foreach($route->getParams() as $param) {
            if($param instanceof QueryParam) {
                $paramExists = isset($request->getParams()[$param->getParamName()]);
                // If the param is not nullable and the param isn't set on the request we return an error
                if(!$param->getNullable() && !$paramExists) {
                    return new Response("Missing query param `" . $param->getParamName() . "`", 400);
                }
                
                $args[] = $paramExists ? $request->getParams()[$param->getParamName()] : null;
            }else if($param instanceof HeaderParam) {
                $headerExists = isset($request->getHeaders()[$param->getHeaderName()]);
                // If the header is not nullable and the header isn't set on the request we return an error
                if(!$param->getNullable() && !$headerExists) {
                    return new Response("Missing header `" . $param->getHeaderName() . "`", 400);
                }

                $args[] = $headerExists ? $request->getHeaders()[$param->getHeaderName()] : null;
            }else if($param instanceof BodyParserParam) {
                $args[] = new ($param->getBodyParserClassName())($request->getBody());
            }
        }
        
        return $route->getMethod()->invoke($route->getOwner()->getInstance(), ...$args);
    }
}

class Router {

    public function __construct(
        private array $controllers
    ) {}

    public function route(Request $request): Response {
        $path = trim($request->getPath(), '/');
        
        foreach($this->controllers as $controller) {
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
