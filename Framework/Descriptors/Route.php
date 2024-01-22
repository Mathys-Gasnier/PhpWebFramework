<?php
namespace Framework\Descriptors;

use Framework\Attributes\Route as AttributeRoute;
use Framework\Descriptors\Param as DescriptorParam;

use ReflectionMethod;
use ReflectionNamedType;

class Route {
    private array $params = [];

    public function __construct(
        private AttributeRoute $metadata,
        private ReflectionMethod $method
    ) {
        $returnType = $method->getReturnType();
        
        // The return type should exist, and be a response
        if(
            $returnType == null ||
            !($returnType instanceof ReflectionNamedType) ||
            $returnType->getName() !== 'Framework\Response'
        ) throw new \Error('The return type of a route should always be Framework\Response');

        foreach($method->getParameters() as $param) {
            $this->params[] = DescriptorParam::construct($param);
        }
    }

    public function getMetadata(): AttributeRoute {
        return $this->metadata;
    }
    public function getMethod() {
        return $this->method;
    }
    public function getParams(): array {
        return $this->params;
    }
}
