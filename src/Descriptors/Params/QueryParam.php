<?php
namespace Framework\Descriptors\Params;

use Framework\Descriptors\Param;

class QueryParam extends Param {
    public function __construct(
        private string $paramName,
        private bool $nullable
    ) {}

    public function getParamName() {
        return $this->paramName;
    }
    public function getNullable() {
        return $this->nullable;
    }
}