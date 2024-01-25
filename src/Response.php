<?php
namespace Framework;

class Response {
    public function __construct(
        public string $body = "",
        public int $code = 200,
        public array $headers = []
    ) {}

    public static function json(mixed $body): self {
        return new self(json_encode($body), 200, [
            "Content-Type: application/json"
        ]);
    }

    public static function raw(string $body): self {
        return new self($body, 200, [
            "Content-Type: text/plain"
        ]);
    }

    public static function html(string $body): self {
        return new self($body, 200, [
            "Content-Type: text/html"
        ]);
    }

    public static function redirect(string $url): self {
        return new self("", 302, [
            "Location: " . $url
        ]);
    }
}
