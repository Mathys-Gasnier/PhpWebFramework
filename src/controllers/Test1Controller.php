<?php
namespace Controllers;

use Framework\Attributes\Controller;
use Framework\Attributes\Route;
use Framework\Response;

#[Controller("/test1")]
class Test1Controller {

    #[Route("/")]
    function coucou(): Response {
        return new Response("\nCoucou");
    }

    #[Route("/tt")]
    function salutation(): Response {
        return new Response("\nsalutation");
    }
    
}