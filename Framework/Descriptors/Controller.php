<?php
namespace Framework\Descriptors;

use Framework\Attributes\Controller as AttributeController;

class Controller {
    public function __construct(
        private $instance,
        private AttributeController $metadata,
        private array $routes
    ) {}

    public function getInstance() {
        return $this->instance;
    }
    public function getMetadata(): AttributeController  {
        return $this->metadata;
    }
    public function getRoutes(): array {
        return $this->routes;
    }
}
