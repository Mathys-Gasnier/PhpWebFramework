<?php
namespace Framework\Attributes;

use Framework\Method;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Route {
    public function __construct(
        private string $path,
        private Method $method = Method::GET
    ) {}

    public function getPath(): string {
        return $this->path;
    }
    public function getMethod(): Method {
        return $this->method;
    }
}
