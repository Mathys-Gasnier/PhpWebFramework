<?php
namespace Framework\Descriptors;

use Framework\Attributes\Utils as AttributesUtils;
use Framework\Descriptors\Params\QueryParam;
use Framework\Descriptors\Params\BodyParserParam;
use Framework\Descriptors\Params\HeaderParam;

class Param {

    public static function construct(\ReflectionParameter $metadata) {
        if(AttributesUtils::findAttribute($metadata, "Framework\Attributes\Params\QueryParam") != null) {
            $queryParam = AttributesUtils::findAttribute($metadata, "Framework\Attributes\Params\QueryParam")->newInstance();

            // Get name from attribute, but default to parameter name
            return new QueryParam(
                $queryParam && $queryParam->getParamName() ? $queryParam->getParamName() : $metadata->getName(),
                $metadata->getType()->allowsNull()
            );
        }else if(AttributesUtils::findAttribute($metadata, "Framework\Attributes\Params\HeaderParam") != null) {
            $headerParam = AttributesUtils::findAttribute($metadata, "Framework\Attributes\Params\HeaderParam")->newInstance();

            // Get name from attribute, but default to parameter name
            return new HeaderParam(
                strtolower($headerParam && $headerParam->getHeaderName() ? $headerParam->getHeaderName() : $metadata->getName()),
                $metadata->getType()->allowsNull()
            );
        }else if(AttributesUtils::findAttribute($metadata, "Framework\Attributes\Params\BodyParser") != null) {
            $bodyParserType = $metadata->getType();
            
            // Makes sure the type is a class/class constructor
            if(
                $bodyParserType == null ||
                !($bodyParserType instanceof \ReflectionNamedType) ||
                !AttributesUtils::instatiatable($bodyParserType)
            ) throw new \Error('Attribute #[BodyParser] requires a type that implements Framework\BodyParsers\BodyParser');

            $bodyParserClassName = $bodyParserType->getName();
            $reflection = new \ReflectionClass($bodyParserClassName);

            // Check if the type class implements body parser
            if(
                !in_array("Framework\BodyParsers\BodyParser", $reflection->getInterfaceNames())
            ) throw new \Error('Attribute #[BodyParser] requires a type that implements Framework\BodyParsers\BodyParser');

            return new BodyParserParam($bodyParserClassName);
        }else {
            return null;
        }
    }

}