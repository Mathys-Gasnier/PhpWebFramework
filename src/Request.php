<?php
namespace Framework;

class Request {
    public function __construct(
        private string $path,
        private Method $method,
        private $params, // URL params of the request (?name=World)
        private array $headers,
        private string $body
    ) {}

    public function getPath(): string {
        return $this->path;
    }
    public function getMethod(): Method {
        return $this->method;
    }
    public function getParams() {
        return $this->params;
    }
    public function getHeaders() {
        return $this->headers;
    }
    public function getBody() {
        return $this->body;
    }
}
