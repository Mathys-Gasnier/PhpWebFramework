<?php
namespace Framework;

class Response {
    public function __construct(
        public string $body = "",
        public int $code = 200,
        public array $headers = []
    ) {}
}
