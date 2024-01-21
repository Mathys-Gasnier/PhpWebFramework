<?php
namespace Framework;

use Framework\ControllerManager;
use Framework\Request;
use Framework\Descriptors\Controller as DescriptorController;
use Framework\Descriptors\Route as DescriptorRoute;
use ReflectionNamedType;

function instatiatable ($type){
    return $type != 'Closure' && !is_callable($type) && class_exists($type);
}

class Invoker {
    public static function invokeRoute(DescriptorRoute $route, DescriptorController $controller, Request $request): Response {
        $args = [];

        foreach($route->getParams() as $param) {
            [$paramType, $paramMetadata] = $param;
            if($paramType == "query_param") {
                if(!isset($request->getParams()[$paramMetadata->getName()])) {
                    return new Response("Missing query param `" . $paramMetadata->getName() . "`", 400);
                }
                $args[] = $request->getParams()[$paramMetadata->getName()];
            }else if($paramType == "body_parser") {
                $bodyParserType = $paramMetadata->getType();
                if(
                    $bodyParserType == null ||
                    !($bodyParserType instanceof ReflectionNamedType) ||
                    !instatiatable($bodyParserType)
                ) throw new \Error('Not a compatible body parser');

                $bodyParserClassName = $bodyParserType->getName();
                
                $args[] = new $bodyParserClassName($request->getBody());
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
            
            if(!str_starts_with($path, $controllerPath)) continue;
            
            $subPath = ltrim(substr($path, strlen($controllerPath)), '/');
            $route = $this->routeInController($request, $subPath, $controller);

            if($route == null) continue;

            return Invoker::invokeRoute($route, $controller, $request);
        }

        return new Response("Not Found", 404);
    }

    private function routeInController(Request $request, string $path, DescriptorController $controller): ?DescriptorRoute {
        foreach($controller->getRoutes() as $route) {
            if($route->getMetadata()->getMethod() != $request->getMethod()) continue;
            
            $routePath = ltrim($route->getMetadata()->getPath(), '/');

            if($path != $routePath) continue;

            return $route;
        }
        
        return null;
    }
    
}
