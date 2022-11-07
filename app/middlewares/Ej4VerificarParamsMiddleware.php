<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class Ej4VerificarParamsMiddleware
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
                $parametros = $request->getParsedBody();
                $usuarioParam = $parametros['usuario'];
                $claveParam = $parametros['clave'];

                if (!empty($usuarioParam) && !empty($claveParam)) 
                {
                    $response = $handler->handle($request);
                    $response = $response->withStatus(200);
                }
                else if(!empty($usuarioParam) && empty($claveParam))
                {
                    $response->getBody()->write(json_encode(array("mensaje"=>"Falta atributo clave!!!")));
                    $response = $response->withStatus(403);
                }
                else if(empty($usuarioParam) && !empty($claveParam))
                {
                    $response->getBody()->write(json_encode(array("mensaje"=>"Falta atributo usuario!!!")));
                    $response = $response->withStatus(403);
                }
                else
                {
                    $response->getBody()->write(json_encode(array("mensaje"=>"Falta atributo usuario!!!Falta atributo clave!!!")));
                    $response = $response->withStatus(403);
                }
            }
            else
            {
                $response->getBody()->write(json_encode(array("mensaje"=>"ERROR.Este método no es válido para el grupo JSON")));
                $response = $response->withStatus(400);
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