<?php
namespace Framework\Attributes;

class Utils {
    public static function findAttribute($reflection, string $attributeName) {
        $found = null;
        foreach($reflection->getAttributes() as $attribute) {
            if($attribute->getName() == $attributeName)
                $found = $attribute;
        }
        return $found;
    }
}
