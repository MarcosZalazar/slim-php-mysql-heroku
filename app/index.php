<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Logger;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/Logger.php';
require_once './middlewares/Ej1VerificarCredMiddleware.php';
require_once './middlewares/Ej2VerificarCredMiddleware.php';
require_once './middlewares/Ej3VerificarUsuarioMiddleware.php';
require_once './middlewares/Ej4VerificarParamsMiddleware.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/VerificadoraController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App (instancia la app)
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
  });

//EJERCICIO 1 de 4
$app->map(['GET','POST'],'/credenciales', \VerificadoraController::class . ':VerificadorCredenciales')->add(new Ej1VerificarCredMiddleware());

//EJERCICIO 2 de 4
$app->group('/JSON', function (RouteCollectorProxy $group) {
  $group->get('[/]',  \VerificadoraController::class . ':VerificadorCredenciales2');
  $group->post('[/]',  \VerificadoraController::class . ':VerificadorCredenciales2');
})->add(new Ej2VerificarCredMiddleware());

//EJERCICIO 3 de 4
$app->group('/JSON_BD', function (RouteCollectorProxy $group) {
  $group->get('[/]',  \UsuarioController::class . ':TraerTodos');
  $group->post('[/]',  \UsuarioController::class . ':TraerTodos')->add(new Ej3VerificarUsuarioMiddleware());
});

//EJERCICIO 4 de 4
$app->group('/JSON_Parametros', function (RouteCollectorProxy $group) {
  $group->get('[/]',  \UsuarioController::class . ':TraerTodos');
  $group->post('[/]',  \UsuarioController::class . ':TraerTodos');
})->add(new Ej4VerificarParamsMiddleware());


$app->post('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("Slim Framework 4 PHP");
  return $response;
});

$app->run();
