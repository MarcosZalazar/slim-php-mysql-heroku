<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class Ej3VerificarUsuarioMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        try
        {
            $response = new Response();
            $parametros = $request->getParsedBody();
            $usuarioParam = $parametros['usuario'];
            $claveParam = $parametros['clave'];

            if(VerificadoraController::ExisteUsuario($usuarioParam, $claveParam))
            {
                $response=$handler->handle($request);
                $response = $response->withStatus(200);
            }
            else
            {
                $response->getBody()->write(json_encode(array("mensaje"=>"ERROR.Correo o clave incorrectas")));
                $response = $response->withStatus(403);
            }
        }
        catch(\Throwable $th)
        {
            $response->getBody()->write(json_encode(array("mensaje"=>"ERROR: {$th}")));
            $response = $response->withStatus(400);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>