<?php
namespace App\Controllers;

use App\Models\Link;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LinkController
{

    function create(Request $request, Response $response)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $link = new Link();
        $res = $link->addLink($user_uuid, $body['link_name'], $body['link_short'], $body['link_real']);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }

    function list(Request $request, Response $response)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $link = new Link();
        $res = $link->listLink($user_uuid);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function edit(Request $request, Response $response)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $link = new Link();
        $res = $link->editLink($body['link_name'], $body['link_short'], $body['link_real'], $body['link_uuid'], $user_uuid);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function remove(Request $request, Response $response)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $link = new Link();
        $res = $link->removeLink($body['link_uuid'], $user_uuid);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function clics(Request $request, Response $response){
        
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $link = new Link();
        $res = $link->clicTotal($user_uuid);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function view(Request $request, Response $response){
        $body = $request->getParsedBody();
        $link = new Link();
        $res = $link->viewLink($body['link_uuid'], $body['date']);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function viewCountry(Request $request, Response $response){
        $body = $request->getParsedBody();
        $link = new Link();
        $res = $link->viewCountryLink($body['link_uuid']);

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }
}
