<?php
namespace Framework\Descriptors\Params;

use Framework\Descriptors\Param;

class BodyParserParam extends Param {

    public function __construct(
        private $bodyParserClassName
    ) {}

    public function getBodyParserClassName() {
        return $this->bodyParserClassName;
    }
}