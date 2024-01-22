<?php
namespace Framework\Descriptors;

use Framework\Attributes\Utils as AttributesUtils;
use Framework\Attributes\Controller as AttributeController;
use Framework\Descriptors\Route as DescriptorRoute;

use ReflectionClass;

class Controller {
    private $instance;
    private AttributeController $metadata;
    private array $routes = [];
    
    public function __construct(
        $class // The Controller::class value
    ) {
        $reflection = new ReflectionClass($class);

        // Getting the #[Controller()] attribute from the reflection class
        $attribute = AttributesUtils::findAttribute($reflection, "Framework\Attributes\Controller");
        if($attribute == null) throw new \Error('Missing controller attribute on ' . $reflection->getName());
        $this->metadata = $attribute->newInstance();
        $this->instance = new $class();

        // Routes are methods with a #[Route()] attribute
        foreach($reflection->getMethods() as $method) {
            $routeAttribute = AttributesUtils::findAttribute($method, "Framework\Attributes\Route");
            if($routeAttribute == null) continue;
            $routeMetadata = $routeAttribute->newInstance();

            $this->routes[] = new DescriptorRoute(
                $routeMetadata,
                $method
            );
        }
    }

    public function getInstance() {
        return $this->instance;
    }
    public function getMetadata(): AttributeController {
        return $this->metadata;
    }
    public function getRoutes(): array {
        return $this->routes;
    }
}
