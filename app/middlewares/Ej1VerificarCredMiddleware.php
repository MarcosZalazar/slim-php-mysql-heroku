<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class Ej1VerificarCredMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {           
        try
        {
            $response = new Response();
            if($request->getMethod()==="GET")
            {
                $response=$handler->handle($request);
            }
            else if($request->getMethod()==="POST")
            {
                $mensajeAnterior="Verifico credenciales";
                $parametros = $request->getParsedBody();
                $perfilParam = $parametros['perfil'];

                if($perfilParam=="administrador")
                {
                    $response=$handler->handle($request);
                    $mensajePosterior="Vuelvo del verificador de credenciales";

                    $contenidoAPI=(string) $response->getBody();
                    $response = new Response();
                    $response->getBody()->write("{$mensajeAnterior} <br> {$contenidoAPI} <br> {$mensajePosterior}");
                    $response = $response->withStatus(200);
                }
                else
                {
                    $response->getBody()->write("No tienes habilitado el ingreso");
                    $response = $response->withStatus(403);
                }
            }
            else
            {
                $response->getBody()->write("ERROR.Este método no es válido para el grupo credenciales");
                $response = $response->withStatus(400);
            }
        }
        catch(\Throwable $th)
        {
            $response->getBody()->write("Se produjo el error en {$th->getTrace()}");
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>