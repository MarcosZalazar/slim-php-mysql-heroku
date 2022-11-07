<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class VerificadoraController extends Usuario
{
    public function VerificadorCredenciales($request, $response, $args)
    {
      if($request->getMethod()==="GET")
      {
        $payload = 'No necesita credenciales para GET.<br>' . 'API => GET';
      }
      else if($request->getMethod()==="POST")
      {
        $parametros = $request->getParsedBody();
        $nombreParam = $parametros['nombre'];

        $payload = 'Bievenido '. $nombreParam . '<br> API => POST';
      }
        
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificadorCredenciales2($request, $response, $args)
    {
      if($request->getMethod()==="GET")
      {
        $response->getBody()->write(json_encode(array("mensaje"=>'API => GET')));
      }
      else if($request->getMethod()==="POST")
      {
        $response->getBody()->write(json_encode(array("mensaje"=>'API => POST')));
      }

      return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ExisteUsuario($usuarioAValidar, $claveAValidar)
    {
        $listaUsuariosBD = Usuario::obtenerTodos();
        foreach($listaUsuariosBD as $usuarioBD)
        {
            if($usuarioBD->usuario==$usuarioAValidar && $usuarioBD->clave==$claveAValidar)
            {
                return true;
            }
        }
        return false;
    }
}