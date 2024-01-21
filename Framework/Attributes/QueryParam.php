<?php
namespace Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryParam {
    public function __construct(
    ) {}
}
