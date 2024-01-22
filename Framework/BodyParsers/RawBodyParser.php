<?php
namespace Framework\BodyParsers;

class RawBodyParser implements BodyParser {
    public function __construct(public string $body) {}
    
    public function get(): string {
        return $this->body;
    }
}