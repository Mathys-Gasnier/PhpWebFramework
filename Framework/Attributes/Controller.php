<?php
namespace Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Controller {
    public function __construct(
        private string $path
    ) {}

    public function getPath(): string {
        return $this->path;
    }
}
