<?php
namespace Framework\Attributes\Params;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class QueryParam {
    public function __construct(
        private ?string $paramName = null
    ) {}

    public function getParamName() {
        return $this->paramName;
    }
}
