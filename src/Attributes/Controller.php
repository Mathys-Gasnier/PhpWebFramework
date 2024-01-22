<?php
namespace Framework\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Controller {
    public function __construct(
        private string $path
    ) {}

    public function getPath(): string {
        return $this->path;
    }
}
