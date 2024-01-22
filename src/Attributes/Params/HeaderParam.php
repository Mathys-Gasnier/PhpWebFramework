<?php
namespace Framework\Attributes\Params;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class HeaderParam {
    public function __construct(
        private ?string $headerName = null
    ) {}

    public function getHeaderName() {
        return $this->headerName;
    }
}
