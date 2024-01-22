<?php
namespace Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Child {
    public function __construct() {}
}
