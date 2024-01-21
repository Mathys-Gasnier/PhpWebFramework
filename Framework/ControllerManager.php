<?php
namespace Framework;

use ReflectionClass;
use ReflectionNamedType;
use Framework\Attributes\Utils as AttributesUtils;
use Framework\Descriptors\Controller as DescriptorController;
use Framework\Descriptors\Route as DescriptorRoute;

class ControllerManager {

    private $controllers = [];

    public function __construct(array $controllers) {
        foreach($controllers as $controller) {
            $reflection = new ReflectionClass($controller);
            $controllerAttribute = AttributesUtils::findAttribute($reflection, "Framework\Attributes\Controller");
            if($controllerAttribute == null) continue;
            $controllerMetadata = $controllerAttribute->newInstance();

            $routes = [];

            foreach($reflection->getMethods() as $method) {
                $routeAttribute = AttributesUtils::findAttribute($method, "Framework\Attributes\Route");
                if($routeAttribute == null) continue;
                $routeMetadata = $routeAttribute->newInstance();

                $returnType = $method->getReturnType();

                if(
                    $returnType == null ||
                    !($returnType instanceof ReflectionNamedType) ||
                    $returnType->getName() !== 'Framework\Response'
                ) throw new \Error('The return type of a route should always be Framework\Response');

                $params = [];
                foreach($method->getParameters() as $param) {
                    if(AttributesUtils::findAttribute($param, "Framework\Attributes\QueryParam") != null) {
                        $params[] = [
                            "query_param", $param
                        ];
                    }else if(AttributesUtils::findAttribute($param, "Framework\Attributes\BodyParser") != null) {
                        $params[] = [
                            "body_parser", $param
                        ];
                    }
                }

                $routes[] = new DescriptorRoute(
                    $routeMetadata,
                    $method,
                    $params
                );
            }
            
            $this->controllers[] = new DescriptorController(
                new $controller(),
                $controllerMetadata,
                $routes
            );
        }
    }

    public function getControllers(): array {
        return $this->controllers;
    }
    
}
