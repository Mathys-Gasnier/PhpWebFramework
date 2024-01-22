<?php
namespace Framework;

use Framework\Attributes\Utils as AttributesUtils;

class Injector {

    private static ?Injector $instance = null;
    public static function get(): Injector {
        if(self::$instance == null) self::$instance = new Injector();
        return self::$instance;
    }

    public function construct($class) {
        if(method_exists($class, '__construct')) return new $class(...$this->resolveMethodDependencies($class, '__construct'));
        
        return new $class;
    }

    private function resolveMethodDependencies($class, string $method): array {
        $args = [];
        $reflection = new \ReflectionMethod($class, $method);

        foreach ($reflection->getParameters() as $param) {
            $type = $param->getType();

            if(
                $type == null ||
                !($type instanceof \ReflectionNamedType) ||
                !AttributesUtils::instatiatable($type)
            ) continue;

            $className = $type->getName();

            if(method_exists($className, '__construct'))
                $args[] = new $className(...$this->resolveMethodDependencies($className, '__construct'));
            else
                $args[] = new $className;
        }

        return $args;
    }

}