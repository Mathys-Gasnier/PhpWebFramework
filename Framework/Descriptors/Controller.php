<?php
namespace Framework\Descriptors;

use Framework\Attributes\Utils as AttributesUtils;
use Framework\Attributes\Controller as AttributeController;
use Framework\Descriptors\Route as DescriptorRoute;
use Framework\Injector;

class Controller {
    private $instance;
    private AttributeController $metadata;
    private array $childs = [];
    private array $routes = [];
    
    public function __construct(
        $class // The Controller::class value
    ) {
        $reflection = new \ReflectionClass($class);

        // Getting the #[Controller()] attribute from the reflection class
        $attribute = AttributesUtils::findAttribute($reflection, "Framework\Attributes\Controller");
        if($attribute == null) throw new \Error('Missing controller attribute on ' . $reflection->getName());
        
        $this->metadata = $attribute->newInstance();
        $this->instance = Injector::get()->construct($class);

        // Child controller are class properties with a #[Child()] attribute
        foreach($reflection->getProperties() as $property) {
            if(AttributesUtils::findAttribute($property, "Framework\Attributes\Child") == null) continue;

            $this->childs[] = self::fromProperty($property);
        }

        // Routes are methods with a #[Route()] attribute
        foreach($reflection->getMethods() as $method) {
            $routeAttribute = AttributesUtils::findAttribute($method, "Framework\Attributes\Route");
            if($routeAttribute == null) continue;
            $routeMetadata = $routeAttribute->newInstance();

            $this->routes[] = new DescriptorRoute(
                $routeMetadata,
                $method,
                $this
            );
        }
    }

    public static function fromProperty(\ReflectionProperty $property): Controller {
        $propertyType = $property->getType();

        // Makes sure the type is a class/class constructor
        if(
            $propertyType == null ||
            !($propertyType instanceof \ReflectionNamedType) ||
            !AttributesUtils::instatiatable($propertyType)
        ) throw new \Error('Attribute #[Child] requires a property with a type that is a controller');
        
        return new self($propertyType->getName());
    }

    public function getInstance() {
        return $this->instance;
    }
    public function getMetadata(): AttributeController {
        return $this->metadata;
    }
    public function getChilds(): array {
        return $this->childs;
    }
    public function getRoutes(): array {
        return $this->routes;
    }
}
