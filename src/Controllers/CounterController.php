<?php
namespace App\Controllers;

use App\Models\Counter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CounterController
{

    function view(Request $request, Response $response)
    {
        $body = $request->getParsedBody();
        $link = new Counter();
        $res = $link->viewCounter($body['link_uuid'], $body['origin'], $body['device']);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }

    

    function validate(Request $request, Response $response) {
        $body = $request->getParsedBody();
        $link = new Counter();
        $res = $link->validateLink($body['link_short']);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}
