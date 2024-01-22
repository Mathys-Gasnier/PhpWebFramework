<?php
namespace Framework\Descriptors;

use Framework\Attributes\Utils as AttributesUtils;
use Framework\Descriptors\Params\BodyParserParam;
use Framework\Descriptors\Params\QueryParam;
use ReflectionNamedType;
use ReflectionParameter;

// Check if a type is a class/class constructor
function instatiatable ($type){
    return $type != 'Closure' && !is_callable($type) && class_exists($type);
}

class Param {

    public function __construct(private ReflectionParameter $metadata) {}

    public static function construct(ReflectionParameter $metadata) {
        if(AttributesUtils::findAttribute($metadata, "Framework\Attributes\QueryParam") != null) {
            return new QueryParam($metadata);
        }else if(AttributesUtils::findAttribute($metadata, "Framework\Attributes\BodyParser") != null) {
            $bodyParserType = $metadata->getType();
            
            // Makes sure the type is a body parser
            // TODO: Add a check to see if the type extends BodyParser
            if(
                $bodyParserType == null ||
                !($bodyParserType instanceof ReflectionNamedType) ||
                !instatiatable($bodyParserType)
            ) throw new \Error('Not a compatible body parser');

            $bodyParserClassName = $bodyParserType->getName();

            return new BodyParserParam($bodyParserClassName);
        }
    }

    public function getMetadata(): ReflectionParameter {
        return $this->metadata;
    }

}