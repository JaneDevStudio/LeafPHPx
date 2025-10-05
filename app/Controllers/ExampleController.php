<?php

namespace App\Controllers;

use Leaf\Attributes\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExampleController
{
    #[Route('/', methods: ['GET'])]
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $response->view('welcome');
    }
}
