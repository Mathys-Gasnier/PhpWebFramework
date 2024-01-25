<?php
namespace Controllers;

use Framework\Attributes\Controller;
use Framework\Attributes\Route;
use Framework\Attributes\Params\QueryParam;
use Framework\Attributes\Params\BodyParser;
use Framework\BodyParsers\JsonBodyParser;
use Framework\BodyParsers\RawBodyParser;
use Framework\Method;
use Framework\Response;

#[Controller("/")]
class TestController {
    
    #[Route("/")]
    function getAll(): Response {
        return new Response("\nGETALL");
    }

    #[Route("/", Method::POST)]
    function posting(#[BodyParser] JsonBodyParser $body): Response {
        print_r($body->get());
        return Response::json($body->get());
        // return new Response("\nYeah post ! with body: " . print_r($body->get()));
    }

    #[Route("/test")]
    function test(#[QueryParam] string $name): Response {
        return new Response("\nHello " . $name);
    }
}