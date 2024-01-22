<?php
namespace Controllers;

use Framework\Attributes\Controller;
use Framework\Attributes\Route;
use Framework\Attributes\Params\HeaderParam;
use Framework\Response;

#[Controller("/machin")]
class SubController {

    #[Route("/")]
    public function index(#[HeaderParam()] ?string $test): Response {
        return new Response("test: " . $test, 200, [
            "Test: Machin"
        ]);
    }
}