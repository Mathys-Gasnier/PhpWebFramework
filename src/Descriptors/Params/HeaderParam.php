<?php
namespace Framework\Descriptors\Params;

use Framework\Descriptors\Param;

class HeaderParam extends Param {
    public function __construct(
        private string $headerName,
        private bool $nullable
    ) {}

    public function getHeaderName() {
        return $this->headerName;
    }
    public function getNullable() {
        return $this->nullable;
    }
}