<?php
namespace Framework\Attributes;

use ReflectionAttribute;

class Utils {
    public static function findAttribute($reflection, string $attributeName): ?ReflectionAttribute {
        $found = null;
        foreach($reflection->getAttributes() as $attribute) {
            if($attribute->getName() == $attributeName)
                $found = $attribute;
        }
        return $found;
    }
}
