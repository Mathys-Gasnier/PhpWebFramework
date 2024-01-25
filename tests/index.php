<?php
require __DIR__ . '/../vendor/autoload.php';

use Framework\Server;

use Controllers\TestController;
use Controllers\Test1Controller;

$controllers = [
    TestController::class,
    Test1Controller::class
];

(new Server(
    $controllers
))->handle();