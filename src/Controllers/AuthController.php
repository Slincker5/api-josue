<?php

namespace App\Controllers;

use App\Models\Auth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    function register(Request $request, Response $response)
    {

        $body = $request->getParsedBody();
        $classAuth = new Auth();
        $register = $classAuth->createAccount($body['username'], $body['pass']);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($register));
        return $response;
    }

    function login(Request $request, Response $response)
    {
        $body = $request->getParsedBody();
        $classAuth = new Auth();
        $login = $classAuth->logIn($body['username'], $body['pass']);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($login));
        return $response;
    }
}
