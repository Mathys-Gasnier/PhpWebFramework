<?php
namespace Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class BodyParser {
    public function __construct() {}
}
