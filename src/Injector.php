<?php
namespace Framework;

use Framework\Attributes\Utils as AttributesUtils;

class Injector {

    private static ?Injector $instance = null;
    public static function get(): Injector {
        if(self::$instance == null) self::$instance = new Injector();
        return self::$instance;
    }

    private array $instances = [];

    public function construct($class) {
        if(array_key_exists($class, $this->instances)) return $this->instances[$class];

        if(method_exists($class, '__construct')) $instance = new $class(...$this->resolveMethodDependencies($class, '__construct'));
        else $instance = new $class();

        $this->instances[$class] = $instance;
        return $instance;
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

            $args[] = $this->construct($className);
        }

        return $args;
    }

}