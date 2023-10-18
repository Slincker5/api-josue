<?php
use App\Controllers\AuthController;
use App\Controllers\LinkController;
use App\Controllers\CounterController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
# My class
use Slim\Psr7\Response;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/api');
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

# CONTROL DEL CORS PARA LA API
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

# CONTROL PARA LAS PETICIONES A RECURSOS QUE NO EXISTEN
$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
        $response = new \Slim\Psr7\Response(); // <-- Usa la clase de respuesta de Slim
        $response->getBody()->write('404 - Recurso no encontrado.');

        return $response->withStatus(404);
    }
);

# PROTECCION DE LA API USANDO TOKENS

$validateJwtMiddleware = function ($request, $handler) {
    $response = new Response();
    $key = 'ab7aa093c22e5504aae11b58096766764f7df675a3b68826ced9d54deed12e48';
    $authHeader = $request->getHeaderLine('Authorization');
    if (!$authHeader) {
        $response->getBody()->write(json_encode(["error" => "Token no proporcionado"]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    #EXTRAER TOKEN DE LA CABEZERA
    list($jwt) = sscanf($authHeader, 'Bearer %s');

    #VALIDAR SI LA CABEZERA CONTIENE ALGUN TOKEN
    if (!$jwt) {
        $response->getBody()->write(json_encode(["error" => "Token no encontrado en la cabecera Authorization"]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        // Aquí puedes incluso agregar el payload decodificado al request si lo necesitas después
        $request = $request->withAttribute('payload', $decoded);
        $request = $request->withAttribute('jwt', $jwt);
    } catch (Exception $e) {
        $response = new Response();
        $response->getBody()->write('Token no válido: ' . $e->getMessage());
        return $response->withStatus(401); // Unauthorized
    }

    return $handler->handle($request);
};

# RUTAS DE LA API

$app->group('/link', function ($group) {

    $group->post('/create', LinkController::class . ':create');
    $group->get('/list', LinkController::class . ':list');
    $group->put('/edit', LinkController::class . ':edit');
    $group->post('/remove', LinkController::class . ':remove');
    $group->get('/clics', LinkController::class . ':clics');
    $group->post('/view', LinkController::class . ':view');

})->add($validateJwtMiddleware);

$app->group('/auth', function ($group) {

    $group->post('/register', AuthController::class . ':register');
    $group->post('/login', AuthController::class . ':login');

});

$app->group('/view', function ($group) {

    $group->post('/logger', CounterController::class . ':view');
    $group->post('/validate', CounterController::class . ':validate');

});
$app->run();
