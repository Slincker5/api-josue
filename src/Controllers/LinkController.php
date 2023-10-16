<?php
namespace App\Controllers;

use App\Models\Link;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LinkController
{

    function create(Request $request, Response $response)
    {
        $body = $request->getParsedBody();
        $link = new Link();
        $res = $link->addLink($body['link_name'], $body['link_short'], $body['link_real']);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }

}
