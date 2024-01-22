<?php
namespace Framework\Attributes;

class Utils {
    public static function findAttribute($reflection, string $attributeName): ?\ReflectionAttribute {
        $found = null;
        foreach($reflection->getAttributes() as $attribute) {
            if($attribute->getName() == $attributeName)
                $found = $attribute;
        }
        return $found;
    }

    // Check if a type is a class/class constructor
    public static function instatiatable($type) {
        return $type != 'Closure' && !is_callable($type) && class_exists($type);
    }
}
