<?php
namespace Framework;

use Framework\Descriptors\Controller as DescriptorController;

class ControllerManager {

    private $controllers = [];

    public function __construct(array $controllers) {
        // Construct all controllers descriptors
        foreach($controllers as $controller) {
            $this->controllers[] = new DescriptorController($controller);
        }
    }

    public function getControllers(): array {
        return $this->controllers;
    }
    
}
