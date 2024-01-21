<?php
namespace Framework\Descriptors;

use Framework\Attributes\Route as AttributeRoute;

class Route {
    public function __construct(
        private AttributeRoute $metadata,
        private $method,
        private array $params
    ) {}

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
