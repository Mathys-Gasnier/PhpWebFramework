<?php
namespace Controllers;

use Framework\Attributes\Controller;
use Framework\Attributes\Route;
use Framework\Response;

#[Controller("/machin")]
class SubController {

    #[Route("/")]
    public function index(): Response {
        return new Response("test");
    }
}