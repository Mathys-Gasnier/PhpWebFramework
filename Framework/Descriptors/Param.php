<?php
namespace Framework\Descriptors;

use Framework\Attributes\Utils as AttributesUtils;
use Framework\Descriptors\Params\BodyParserParam;
use Framework\Descriptors\Params\QueryParam;
use ReflectionClass;
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
            
            // Makes sure the type is a class/class constructor
            if(
                $bodyParserType == null ||
                !($bodyParserType instanceof ReflectionNamedType) ||
                !instatiatable($bodyParserType)
            ) throw new \Error('Attribute #[BodyParser] requires a type that implements Framework\BodyParsers\BodyParser');

            $bodyParserClassName = $bodyParserType->getName();
            $reflection = new ReflectionClass($bodyParserClassName);

            // Check if the type class implements body parser
            if(
                !in_array("Framework\BodyParsers\BodyParser", $reflection->getInterfaceNames())
            ) throw new \Error('Attribute #[BodyParser] requires a type that implements Framework\BodyParsers\BodyParser');

            return new BodyParserParam($bodyParserClassName);
        }
    }

    public function getMetadata(): ReflectionParameter {
        return $this->metadata;
    }

}