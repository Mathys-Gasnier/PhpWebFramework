<?php
namespace Framework\BodyParsers;

interface BodyParser {
    public function __construct(string $body);
    
    public function get();
}