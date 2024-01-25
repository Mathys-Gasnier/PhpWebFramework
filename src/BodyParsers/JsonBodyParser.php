<?php
namespace Framework\BodyParsers;

class JsonBodyParser implements BodyParser {
    private mixed $body;

    public function __construct(string $body) {
        $this->body = json_decode($body);
    }
    
    public function get(): mixed {
        return $this->body;
    }
}