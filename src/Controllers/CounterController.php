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

    function clics(Request $request, Response $response){
        
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $link = new Counter();
        $res = $link->clicTotal($user_uuid);

    }
}
